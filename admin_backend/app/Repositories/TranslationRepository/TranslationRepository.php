<?php

namespace App\Repositories\TranslationRepository;

use App\Models\Translation;
use App\Repositories\CoreRepository;

class TranslationRepository extends CoreRepository
{

    protected function getModelClass()
    {
        return Translation::class;
    }
}
