<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToSetVisibility;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('image');
        $altName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $spacesPath = env('DO_SPACES_IMAGE_PATH') . 'products';

        try {
            $path = $file->store($spacesPath, 'spaces');
        } catch (UnableToWriteFile|UnableToSetVisibility $e) {
            return response()->json(['message' => 'Failed to upload image', 'errors' => $e], 500);
        }

        if(!$path) {
            response()->json(['message' => 'Failed to upload image'], 500);
        }

        $url = Storage::disk('spaces')->url($path);

        $image = Image::create([
            'alt' => $altName,
            'url' => $url,
        ]);

        return response()->json($image, 201);
    }
}
