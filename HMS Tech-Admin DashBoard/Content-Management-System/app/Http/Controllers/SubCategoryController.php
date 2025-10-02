<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('category')->latest()->paginate(10);
        return view('subcategory.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('subcategory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sub_categories,name',
            'slug' => 'required|string|max:255|unique:sub_categories,slug',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        SubCategory::create($request->all());

        return redirect()->route('subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function edit(SubCategory $subcategory)
    {
        $categories = Category::all();
        return view('subcategory.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subcategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $subcategory->id,
            'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $subcategory->id,
            'description' => 'nullable|string' ,
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory->update($request->all());

        return redirect()->route('subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(SubCategory $subcategory)
    {
        $subcategory->delete();
        return redirect()->route('subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }
}
