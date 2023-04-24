<?php

namespace App\Services\Interfaces;

use App\Models\Brand;

interface BrandServiceInterface
{
    public function create(array $data);

    public function update(Brand $brand, array $data);

    public function delete(?array $ids = []);
}
