<?php

namespace App\Services\Interfaces;


interface UserServiceInterface
{
    public function create(array $data);

    public function update(string $uuid, array $data);
}
