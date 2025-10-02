<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TaskAssetController extends Controller
{
    /**
     * Upload an image from the editor and attach to a task.
     * Frontend should POST: file (image), task_id
     * Returns: { url: "...", id: task_image_id }
     */
    public function upload(Request $request)
    {
        $request->validate([
            'task_id' => ['required', Rule::exists('tasks', 'id')],
            'file'    => ['required', 'file', 'image', 'max:5120'], // 5MB
            'alt'     => ['nullable', 'string', 'max:255'],
        ]);

        $task = Task::findOrFail($request->task_id);

        $path = $request->file('file')->store('tasks/images', 'public');
        $image = getimagesize($request->file('file')->getRealPath());
        $width = $image[0] ?? null;
        $height = $image[1] ?? null;

        $ti = TaskImage::create([
            'task_id'    => $task->id,
            'path'       => $path,
            'mime'       => $request->file('file')->getMimeType(),
            'size'       => $request->file('file')->getSize(),
            'alt_text'   => $request->input('alt'),
            'width'      => $width,
            'height'     => $height,
            'uploaded_by'=> auth()->id(),
        ]);

        return response()->json([
            'id'  => $ti->id,
            'url' => asset('storage/' . $path),
        ]);
    }

    /**
     * Delete an image (optional)
     */
    public function destroy(TaskImage $image)
    {
        // Optionally, ensure the current user can delete it (policy)
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();

        return response()->json(['success' => true]);
    }
}
