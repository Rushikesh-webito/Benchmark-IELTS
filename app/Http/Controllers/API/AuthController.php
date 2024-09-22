<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,JsonResponse};
use Illuminate\Support\Facades\{Auth,Hash,Log,Validator,Password,Mail,DB};
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Str;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordSetupEmail;



class AuthController extends Controller
{

    public function __construct()
    {
        # By default we are using here auth:api middleware
        // $this->middleware('api', ['except' => ['login']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'profile_image' => 'nullable|image|max:2048', 
            'mobile_no' => 'nullable|string|max:15',
            'role_id' => 'required|integer|exists:roles,id', 
            'timezone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,blacklisted', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $profileImagePath = null;

            if ($request->hasFile('profile_image')) {
                $profileImagePath = $this->upload_media($request->file('profile_image'), 'profile');
            }

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'profile_image' => $profileImagePath,
                'mobile_no' => $request->input('mobile_no'),
                'role_id' => $request->input('role_id'),       
                'timezone' => $request->input('timezone'),      
                'status' => $request->input('status'),   
            ]);

            // Generate a JWT token for the user
            $token = JWTAuth::fromUser($user);

            $user->token = $token;
            $user->save();

            \Mail::to($user->email)->send(new WelcomeEmail($user));

            $data = [
                'user' => $user,
                'token' => $token,
            ];
            DB::commit();

            return $this->successResponse($data);


        } catch (Exception $e) {
            
            DB::rollBack();

            return $this->errorResponse($data);
        }
    }


    public function simpleRegister(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'mobile_no' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the user already exists
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists.',
                ], 409);
            }

            // Create a temporary user record
            $user = User::create([
                'email' => $request->input('email'),
                'mobile_no' => $request->input('mobile_no'),
                'status' => 'pending', // Set a pending status
            ]);

            // Generate a unique token for password setting
            $token = Str::random(60);
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => Hash::make($token), // Store a hashed token
                'created_at' => now(),
            ]);

            // Send email with the link to set password
            $link = url('api/set-password?token=' . $token . '&email=' . urlencode($user->email));
            \Mail::to($user->email)->send(new PasswordSetupEmail($user, $link));

            return response()->json([
                'success' => true,
                'message' => 'Registration successful, please check your email to set your password.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
            ], 500);
        }
    }


    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($data);
        }

        $credentials = $request->only('email', 'password');
        try {
            // DB::beginTransaction();

            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $user = Auth::guard('api')->user();

            $user->token = $token; 
            $user->save();


              $data = [
                'user' => $user,
                'token' => $token,
            ];

            // Commit transaction
            // DB::commit();

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Login successful',
            //     'token' => $token,
            // ]);
            return $this->successResponse($data);

        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            // DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Login failed',
            //     'error' => $e->getMessage(),
            // ], 500);
            return $this->errorResponse($data);
        }
    }


    public function logout(Request $request)
    {
        try {
            // Check if the user is authenticated
            if (Auth::guard('api')->check()) {
                // Invalidate the token
                Auth::guard('api')->logout();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful',
                ]);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'User is not logged in',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        # When access token will be expired, we are going to generate a new one wit this function 
        # and return it here in response
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        # This function is used to make JSON response with new
        # access token of current user
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function forgetPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $user = DB::table('users')->where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email does not exist.',
                ], 404);
            }

            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now(),
                ]
            );

            // Generate the password reset URL
            $url = url('password/reset', $token); // Adjust this to your actual route

            Mail::send('emails.password_reset', ['user' => $user, 'url' => $url], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Password Reset Request');
            });

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


        public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $passwordBroker = Password::broker();
        $response = $passwordBroker->reset(
            [
                'email' => $request->email,
                'password' => $request->password,
                'token' => $request->token,
            ],
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed. Please try again.',
            ], 400);
        }
    }

    public function getProfile(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse('User not authenticated.', [], 401); // Unauthorized
            }

            if ($user->status !== 'active') {
                return $this->errorResponse('User account is inactive.', [], 403); // Forbidden
            }

            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'mobile_no' => $user->mobile_no,
                'role_id' => $user->role_id,
                'timezone' => $user->timezone,
                'status' => $user->status
            ];
            return $this->successResponse($data);

        } catch (Exception $e) {
            return $this->errorResponse('An error occurred while fetching the profile.', [
                'error' => $e->getMessage()
            ], 500); 
        }
    }

    public function me()
    {
        return response()->json(Auth::user());
    }


    
}


