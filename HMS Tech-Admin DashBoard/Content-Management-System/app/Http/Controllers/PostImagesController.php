<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostImages;
use Illuminate\Support\Facades\Storage;

class PostImagesController extends Controller
{
    /**
     * Handle Dropzone upload
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('post_images', 'public');

            $image = PostImages::create([
                'post_id'   => $request->post_id ?? null, // null at create stage
                'image_path'=> $path,
                'alt_text'  => $file->getClientOriginalName(),
            ]);

            return response()->json([
                'success' => true,
                'file_id' => $image->id,
                'file_path' => $path
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Handle Dropzone delete
     */
    public function destroy($id)
    {
        $image = PostImages::findOrFail($id);

        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }
}