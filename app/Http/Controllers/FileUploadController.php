<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    /**
     * @param Request $oRequest
     * @return JsonResponse
     */
    public function uploadImage(Request $oRequest): JsonResponse
    {
        if($oRequest->hasFile('image') === true) {
            $oRequest->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = time().'.'.$oRequest->image->extension();
            $oRequest->image->move(public_path('upload'), $imageName);
            return response()->json(['result' => true, 'url' => '/upload/' . $imageName]);
        }
        return response()->json(['result' => false, 'message' => 'Failed to upload image']);
    }
}
