<?php

namespace App\Services\Interfaces;

use App\Models\Language;

interface LanguageServiceInterface
{
    public function create(array $data);

    public function update(Language $language, array $data);

    public function delete(?array $ids = []);

    public function setLanguageDefault(int $id = null, int $default = null);
}
