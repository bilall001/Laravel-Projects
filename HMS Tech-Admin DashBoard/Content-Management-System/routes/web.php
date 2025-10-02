<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImagesController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');


Route::resource('categories', CategoryController::class);
Route::resource('subcategories', SubcategoryController::class);
Route::resource('tags', TagController::class);
Route::resource('posts', PostController::class);
// AJAX routes for dynamic fetching of subcategories and tags
Route::get('/categories/{id}/subcategories', [PostController::class, 'getSubcategories'])->name('categories.subcategories');
Route::get('/categories/{id}/tags', [PostController::class, 'getTags'])->name('categories.tags');
Route::post('/posts/upload-image', [PostImagesController::class, 'store'])->name('posts.uploadImage');
Route::delete('/posts/delete-image/{id}', [PostImagesController::class, 'destroy'])->name('posts.deleteImage');