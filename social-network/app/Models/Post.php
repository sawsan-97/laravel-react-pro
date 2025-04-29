<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content',
        'image',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the likes for the post.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the users who liked this post.
     */
    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id')->withTimestamps();
    }

    /**
     * Check if post is liked by a specific user
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
