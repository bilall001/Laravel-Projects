<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Container\Attributes\Auth;

class CategoryService
{
    /**
     * Create a new class instance.
     */
   protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function getUserCategories($userId)
    {
        return $this->categoryRepo->getAllByUser($userId);
    }
   public function getById($id)
    {
        return $this->categoryRepo->findById($id);
    }
    public function createCategory($data)
    {
        return $this->categoryRepo->create($data);
    }

    public function updateCategory($id, $data)
    {
        return $this->categoryRepo->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepo->delete($id);
    }
}
