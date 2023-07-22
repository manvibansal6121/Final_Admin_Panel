<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image as Image;

class ImageUploadController extends Controller
{
    //IMAGE UPLOAD   
    public function imageUpload(Request $request)
    {
    //Validating Image
    $validator = Validator::make($request->all(), [
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    //If Validation Fails
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    //Setting the name and path of the requesting image
    $image = $request->file('image');
    $imageName = time() . '.' . $image->getClientOriginalExtension();
    $imagepath = $image->storeAs('original', $imageName, 's3');
    $url = Storage::disk('s3')->url($imagepath);

    //Saving image in medium folder by resizing the image
    $mediumImage = Image::make($image)->resize(800,800,function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    $mediumImageName = 'medium' . $imageName;
    $mediumImagePath = 'medium/' . $imageName;
    Storage::disk('s3')->put($mediumImagePath,(string)$mediumImage->encode());
    $mediumurl = Storage::disk('s3')->url($mediumImagePath); 

    //Saving the image in the thumbnail folder by resizing the image
    $thumbnailImage = Image::make($image)->resize(150,150,function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    $thumbnailImageName = 'thumbnail' . $imageName;
    $thumbnailImagePath = 'thumbnail/' . $imageName;
    Storage::disk('s3')->put($thumbnailImagePath,(string)$thumbnailImage->encode());
    $thumbnailurl = Storage::disk('s3')->url($thumbnailImagePath); 

    //Returning Response
    return response()->json([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'path' => $url,
        'folders'=>[
                    'original',
                    'medium',
                    'thumbnail'
                ],
        'image' => $imageName,
       
    ]);
    }
}
