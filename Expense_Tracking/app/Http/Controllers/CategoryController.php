<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
class CategoryController extends Controller
{
   protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:sanctum');
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        return response()->json($this->categoryService->getUserCategories($request->user()->id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only('name');
        $data['user_id'] = $request->user()->id;

        $category = $this->categoryService->createCategory($data);

     
        return response()->json([
            'category' =>$category,
            'message' => 'Category created successfully'],201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = $this->categoryService->updateCategory($id, $request->only('name'));

        return response()->json([
            'category' =>$category,
            'message' => 'Category updated successfully'],201);
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
