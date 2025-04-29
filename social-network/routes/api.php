<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;

Route::get('/test-api', function () {
    return response()->json(['message' => 'API is working!']);
});

// طرق عامة (لا تتطلب مصادقة)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// طرق محمية (تتطلب مصادقة)
Route::middleware('auth:sanctum')->group(function () {
    // المصادقة
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // المستخدمين
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/search', [UserController::class, 'search']);
    Route::post('/users/update', [UserController::class, 'update']);
    Route::get('/users/liked-posts', [UserController::class, 'likedPosts']);

    // المنشورات
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::get('/posts/search', [PostController::class, 'search']);
    Route::get('/users/{id}/posts', [PostController::class, 'userPosts']);

    // التعليقات
    Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    // الإعجابات
    Route::post('/posts/{postId}/like', [LikeController::class, 'toggleLike']);
    Route::get('/posts/{postId}/likes', [LikeController::class, 'getLikes']);
    Route::get('/posts/{postId}/check-like', [LikeController::class, 'checkLike']);
});
