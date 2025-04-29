<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // اختيار مستخدم ومنشور عشوائي
        $user = User::inRandomOrder()->first();
        $post = Post::inRandomOrder()->first();

        // التأكد من أن هذا المستخدم لم يُعجب بالفعل بهذا المنشور
        while ($post->likes()->where('user_id', $user->id)->exists()) {
            $user = User::inRandomOrder()->first();
            $post = Post::inRandomOrder()->first();
        }

        return [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
