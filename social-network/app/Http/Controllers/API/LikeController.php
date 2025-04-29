<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    /**
     * الإعجاب بمنشور أو إلغاء الإعجاب
     */
    public function toggleLike(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $user = $request->user();

        // التحقق مما إذا كان المستخدم قد أعجب بالفعل بهذا المنشور
        $existingLike = Like::where('user_id', $user->id)
                           ->where('post_id', $post->id)
                           ->first();

        if ($existingLike) {
            // إذا كان المستخدم قد أعجب بالفعل، قم بإلغاء الإعجاب
            $existingLike->delete();
            $action = 'unliked';
        } else {
            // إذا لم يكن المستخدم قد أعجب بعد، قم بإضافة إعجاب جديد
            $like = new Like();
            $like->user_id = $user->id;
            $like->post_id = $post->id;
            $like->save();
            $action = 'liked';
        }

        // الحصول على العدد الجديد للإعجابات
        $likesCount = $post->likes()->count();

        return response()->json([
            'status' => 'success',
            'message' => "Post {$action} successfully",
            'likes_count' => $likesCount,
            'is_liked' => $action === 'liked'
        ], 200);
    }

    /**
     * الحصول على قائمة المستخدمين الذين أعجبوا بمنشور معين
     */
    public function getLikes($postId)
    {
        $post = Post::findOrFail($postId);
        $users = $post->likedBy()->get();

        return response()->json([
            'status' => 'success',
            'users' => $users,
            'likes_count' => $users->count()
        ], 200);
    }

    /**
     * التحقق مما إذا كان المستخدم الحالي قد أعجب بمنشور معين
     */
    public function checkLike(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $isLiked = $post->isLikedBy($request->user()->id);

        return response()->json([
            'status' => 'success',
            'is_liked' => $isLiked,
            'likes_count' => $post->likes()->count()
        ], 200);
    }
}
