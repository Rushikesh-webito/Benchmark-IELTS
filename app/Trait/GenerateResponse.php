<?php

namespace App\Trait;

trait GenerateResponse
{
    //
    protected function successResponse($data = [], $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'payload' => $data
        ], $code);
 
    }
 
    /**
 
     * @param null $message
 
     * @param array $data
 
     * @param int $code
 
     * @return \Illuminate\Http\JsonResponse
 
     */

    protected function errorResponse($message = null, $data = [], $code = 500)
    {
        $response = [
            'status' => 'failure',
            'message' => $message
        ];

        if ($data) {
            $response['payload'] = $data;
        }

        return response()->json($response, $code);
 
    } 
}
