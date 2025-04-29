<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * عرض قائمة المنشورات
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
                     ->withCount('likes')
                     ->latest()
                     ->paginate(10);

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    /**
     * إنشاء منشور جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = new Post();
        $post->user_id = $request->user()->id;
        $post->content = $request->content;

        // معالجة تحميل الصورة إذا كانت موجودة
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('post_images', 'public');
            $post->image = $path;
        }

        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    /**
     * عرض منشور محدد
     */
    public function show($id)
    {
        $post = Post::with(['user', 'comments.user'])
                    ->withCount('likes')
                    ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'post' => $post
        ], 200);
    }

    /**
     * تحديث منشور محدد
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // التحقق من أن المستخدم هو مالك المنشور
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $post->content = $request->content;

        // معالجة تحميل الصورة إذا كانت موجودة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            // تحميل الصورة الجديدة
            $path = $request->file('image')->store('post_images', 'public');
            $post->image = $path;
        }

        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }

    /**
     * حذف منشور محدد
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // التحقق من أن المستخدم هو مالك المنشور
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        // حذف الصورة المرتبطة بالمنشور إذا كانت موجودة
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully'
        ], 200);
    }

    /**
     * البحث في المنشورات
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        $posts = Post::where('content', 'like', "%{$query}%")
                    ->with('user')
                    ->withCount('likes')
                    ->latest()
                    ->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    /**
     * الحصول على منشورات مستخدم معين
     */
    public function userPosts($userId)
    {
        $posts = Post::where('user_id', $userId)
                    ->with('user')
                    ->withCount('likes')
                    ->latest()
                    ->paginate(10);

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
