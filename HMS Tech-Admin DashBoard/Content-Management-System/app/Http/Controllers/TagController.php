<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
       public function index()
    {
        $tags = Tag::with('category')->latest()->paginate(10);
        return view('tags.index', compact('tags'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('tags.create', compact('categories'));
    }
     public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|unique:tags,name|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Tag::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }
    public function edit(Tag $tag)
    {
        $categories = Category::all();
        return view('tags.edit', compact('tag', 'categories'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name'        => 'required|max:255|unique:tags,name,' . $tag->id,
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $tag->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}
