<?php

namespace App\Traits;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property static|void $create
 * @property Collection|Activity[] $activities
 * @property int $activities_count
*/
trait Activity
{
    public function create($files): void
    {
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Gallery::class, 'loadable');
    }
}

