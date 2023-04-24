<?php

namespace App\Observers;

use App\Models\Gallery;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GalleryObserver
{
    use Loggable;
    /**
     * Handle the Gallery "deleted" event.
     *
     * @param Gallery $gallery
     * @return void
     */
    public function deleted(Gallery $gallery)
    {
//        try {
//            Storage::disk('public')->delete('images/' . $gallery->path);
//        } catch (Throwable $e) {
//            $this->error($e);
//        }
    }
}
