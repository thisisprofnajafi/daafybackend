<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use tihiy\Compressor\ImageCompressor;
use App\Models\Photo;
class PhotosController extends Controller
{
    public function decodePhoto($file){
        $image_64 = $file; //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

        $replace = substr($image_64, 0, strpos($image_64, ',')+1);

        // find substring fro replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = Str::random(40).'.'.$extension;

        Storage::disk('public')->put($imageName, base64_decode($image));

        return $imageName;
    }
    public function newPhoto(Request $request){

        if (auth()->user()->photos()->count() <= 5){

            $validatedData = $request->validate([
                'image' => 'required'
            ]);

            $savedname = $this->decodePhoto($request->image);
            $savedFile = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
            $user = auth()->user();
            $name = md5($user->username . $user->id . 'image') . '-image' .$savedname;
            $xPath = $savedFile . $savedname;
            $img = Image::make($xPath);
            if ($img->height() >= $img->width()) {
                $img->crop($img->width(), $img->width(), 0, (int)(($img->height() - $img->width()) / 2));
            }else{
                $img->crop($img->height(), $img->height(), (int)(($img->width() - $img->height()) / 2),  0);
            }
            $img->resize(500,500);
            $path = Storage::disk('images')->getDriver()->getAdapter()->getPathPrefix().$name;
            $img->save( $path , 100);
            $savePath = env('SITE_URL').'images/user/images/'.$name;
            $newImage = new Photo();
            $newImage->path = $savePath;
            auth()->user()->photos()->save($newImage);
            return RespondHandler::respond(['status'=>true,'image' => $newImage], 200);
        }else{
            return RespondHandler::respond(['status'=>true , 'limit'=>true],200);
        }
    }

    public function deletePhoto($id){

        $image = auth()->user()->photos()->where('id',$id);
        $image->delete();

        return RespondHandler::respond(['status'=>true],200);


    }
    public function getPhotos(){
        
        $images = auth()->user()->photos;
        return RespondHandler::respond(['status'=>true , 'images'=>$images],200);


    }
}
