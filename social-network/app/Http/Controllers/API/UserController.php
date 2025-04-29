<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{
    /**
     * الحصول على تفاصيل مستخدم معين
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'user' => $user
        ], 200);
    }

    /**
     * البحث عن مستخدمين
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        $users = User::where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->get();

        return response()->json([
            'status' => 'success',
            'users' => $users
        ], 200);
    }

    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث الاسم والسيرة الذاتية إذا كانت موجودة في الطلب
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        // معالجة تحميل الصورة إذا كانت موجودة
        if ($request->hasFile('profile_image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // تحميل الصورة الجديدة
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);
    }

    /**
     * الحصول على المنشورات التي أعجب بها المستخدم
     */
    public function likedPosts(Request $request)
    {
        $user = $request->user();
        $likedPosts = $user->likedPosts()->with('user')->latest()->get();

        return response()->json([
            'status' => 'success',
            'posts' => $likedPosts
        ], 200);
    }
}
