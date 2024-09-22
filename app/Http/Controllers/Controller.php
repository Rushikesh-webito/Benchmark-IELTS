<?php
 
namespace App\Http\Controllers;
 
use App\Trait\GenerateResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
 
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, GenerateResponse;


    public function upload_media($media_object, $prefix='') {
        $fileImage = '';
        if ($media_object) {
            $destinationPath = storage_path('app/public/uploads');
            $fileImage = ($prefix ? $prefix .'_':'') . date('YmdHis') . '_' . rand(111,333) . "." . $media_object->getClientOriginalExtension();
            $media_object->move($destinationPath, $fileImage);
        }
 
        return $fileImage;
    }
 
}
