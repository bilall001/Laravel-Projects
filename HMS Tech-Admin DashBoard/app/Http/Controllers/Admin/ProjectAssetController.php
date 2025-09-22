<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectAssetController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'file'       => ['required', 'file', 'mimes:png,jpg,jpeg,gif,webp', 'max:8192'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        $disk = 'public';
        $filename = uniqid('proj_' . $project->id . '_') . '.' . $request->file('file')->getClientOriginalExtension();
        $path = $request->file('file')->storeAs('uploads/projects', $filename, $disk);

        $asset = $project->images()->create([
            'disk'          => $disk,
            'path'          => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json([
            'id'  => $asset->id,
            'url' => Storage::url($asset->path),   // <— key change
            'name' => $asset->original_name,
        ]);
    }

    public function destroy(ProjectAsset $asset)
    {
        // Model boot hook removes the file; this deletes the row.
        $asset->delete();

        return response()->noContent();
    }
}
