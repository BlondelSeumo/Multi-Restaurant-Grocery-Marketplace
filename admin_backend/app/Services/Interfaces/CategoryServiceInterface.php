<?php

namespace App\Services\Interfaces;

interface CategoryServiceInterface
{
    public function create(array $data = []);

    public function update(string $uuid, array $data = []);

    public function delete(?array $ids = []);
}
