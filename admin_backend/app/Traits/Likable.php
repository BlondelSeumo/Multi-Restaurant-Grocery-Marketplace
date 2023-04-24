<?php

namespace App\Traits;

use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property-read Collection|Like[] $likes
 * @property-read int|null $likes_count
 *
 */
trait Likable
{
    public function liked(): void
    {
        $like = $this->likes()->firstWhere('user_id', auth('sanctum')->id());

        if (empty($like)) {
            $this->likes()->create([
                'user_id' =>  auth('sanctum')->id()
            ]);
            return;
        }

        $like->delete();
    }
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likable');
    }
}
