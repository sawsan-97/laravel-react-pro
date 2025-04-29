<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء 10 مستخدمين
        User::factory(10)->create();

        // إنشاء مستخدم ثابت للاختبار
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // إنشاء 50 منشور
        Post::factory(50)->create();

        // إنشاء 100 تعليق
        Comment::factory(100)->create();

        // إنشاء 200 إعجاب
        Like::factory(200)->create();
    }
}
