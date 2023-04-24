<?php

namespace App\Exports;

use App\Models\Gallery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BaseExport
{
    /**
     * @param Collection $galleries
     * @return string
     */
    protected function imageUrl(Collection $galleries): string
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return $galleries->transform(function (Gallery $gallery) {
            return [
                'path' => request()->getScheme() . '://' . request()->getHttpHost() . "/storage/images/$gallery->path"
            ];
        })->implode('path', ',');
    }
}
