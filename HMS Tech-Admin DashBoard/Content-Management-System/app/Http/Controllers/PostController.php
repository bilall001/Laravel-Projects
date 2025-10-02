<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostImages;
use App\Models\SubCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'subcategory', 'tags', 'images', 'user'])->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();

        // Initially no subcategories (you will fetch them dynamically via AJAX when a category is selected)
        $subcategories = collect();

        // Initially no tags (you will fetch them dynamically via AJAX when a category is selected)
        $tags = collect();
        return view('posts.create', compact('categories', 'subcategories', 'tags'));
    }
    // Fetch subcategories for a given category
    public function getSubcategories($categoryId)
    {
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json($subcategories);
    }

    // Fetch tags for a given category
    public function getTags($categoryId)
    {
        $tags = Tag::where('category_id', $categoryId)->get();
        return response()->json($tags);
    }
    public function store(Request $request)
    {
        // dd($request->all(), $request->file('post_images'));
        $request->validate([
            'title'          => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'content'        => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
            // 'post_images.*'  => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
        ]);

        $data = $request->only([
            'category_id',
            'sub_category_id',
            'title',
            'content',
            'status',
            'published_at'
        ]);

        // $data['user_id'] = '1';
        $data['slug'] = Str::slug($request->title);

        // Upload featured image
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts/featured', 'public');
        }

        $post = Post::create($data);

        // Attach tags
        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }
        // dd($request->post_images);
        // Save multiple images
        if ($request->has('post_images')) {
            foreach ($request->post_images as $imagePath) {
                PostImages::create([
                    'post_id'   => $post->id,
                    'image_path' => $imagePath,
                    'alt_text'  => 'bilal is great', // optional, you can customize
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {    $categories = Category::all();

        // Initially no subcategories (you will fetch them dynamically via AJAX when a category is selected)
        $subcategories = collect();

        // Initially no tags (you will fetch them dynamically via AJAX when a category is selected)
        $tags = collect();
        $post->load('tags', 'images','category','subcategory');
        // dd($post);
        return view('posts.edit', compact('post','categories','subcategories','tags'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'content'        => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
            // 'images.*'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'category_id',
            'sub_category_id',
            'title',
            'content',
            'status',
            'published_at'
        ]);

        $data['slug'] = Str::slug($request->title);

        // Update featured image
        if ($request->hasFile('featured_image')) {
             if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
            Storage::disk('public')->delete($post->featured_image);
        }
            $data['featured_image'] = $request->file('featured_image')->store('posts/featured', 'public');
        }

        $post->update($data);

        // Sync tags
        if ($request->tags) {
            $post->tags()->sync($request->tags);
        }

        // Update/Add new images
        if ($request->has('post_images')) {
            foreach ($request->post_images as $imagePath) {
                PostImages::create([
                    'post_id'   => $post->id,
                    'image_path' => $imagePath,
                    'alt_text'  => 'bilal is great', // optional, you can customize
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->tags()->detach();
        $post->images()->delete();
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
