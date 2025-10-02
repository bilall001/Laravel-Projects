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
         $categories = $this->categoryService->getUserCategories($request->user()->id);

    return view("categories.index", [
        'categories' => $categories
    ]);
    }

    public function create(){
        return view("Categories.create");
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only('name');
        $data['user_id'] = $request->user()->id;

        $this->categoryService->createCategory($data);

         return redirect()
        ->route('categories.index')
        ->with('success', 'Category created successfully!');
    }

    public function edit($id){
        $category = $this->categoryService->getById($id);
        return view("Categories.edit", compact('category'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->categoryService->updateCategory($id, $request->only('name'));

         return redirect()
        ->route('categories.index')
        ->with('success', 'Category Updated successfully!');
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);

        return redirect()
        ->route('categories.index')
        ->with('success', 'Category deleted successfully!');
    }
}
