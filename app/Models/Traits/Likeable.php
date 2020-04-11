<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        if (! auth()->check()) {
            return;
        }

        // check if the current user has already liked the model
        if ($this->isLikedByUser(auth()->id())) {
            return;
        };

        $this->likes()->create(['user_id', auth()->id()]);
    }

    public function unlike()
    {
        if (! auth()->check()) return;

        if (! $this->isLikeByUser(auth()->id())) {
            return;
        }

        $this->likes()->whtere('user_id', auth()
                      ->id())->delete();
    }

    public function likedByUser($user_id)
    {
        return (bool)$this->likes()
                ->where('user_id', $user_id)
                ->count();
    }
}
