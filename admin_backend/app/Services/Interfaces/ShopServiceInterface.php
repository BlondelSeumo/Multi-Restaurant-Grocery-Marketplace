<?php

namespace App\Services\Interfaces;

interface ShopServiceInterface
{
    public function create(array $data);

    public function update(string $uuid, array $data);

    public function delete(?array $ids = []);

    public function imageDelete(string $uuid, array $data);

}
