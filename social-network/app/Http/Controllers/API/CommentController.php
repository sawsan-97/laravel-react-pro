<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    /**
     * الحصول على تعليقات منشور معين
     */
    public function index($postId)
    {
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->with('user')->latest()->get();

        return response()->json([
            'status' => 'success',
            'comments' => $comments
        ], 200);
    }

    /**
     * إضافة تعليق جديد
     */
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = new Comment();
        $comment->user_id = $request->user()->id;
        $comment->post_id = $post->id;
        $comment->content = $request->content;
        $comment->save();

        // تحميل علاقة المستخدم للعرض في الاستجابة
        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'comment' => $comment
        ], 201);
    }

    /**
     * تحديث تعليق محدد
     */
    public function update(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // التحقق من أن المستخدم هو مالك التعليق
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment->content = $request->content;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ], 200);
    }

    /**
     * حذف تعليق محدد
     */
    public function destroy(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // التحقق من أن المستخدم هو مالك التعليق
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully'
        ], 200);
    }
}
