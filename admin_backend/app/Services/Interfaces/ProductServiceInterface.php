<?php

namespace App\Services\Interfaces;

interface ProductServiceInterface
{
    public function create(array $data);

    public function update(string $uuid, array $data);
}
