<?php

namespace App\Repositories\Interfaces;

interface UserRepoInterface
{
    public function userById(int $id);

    public function userByUUID(string $uuid);

    public function usersPaginate(string $perPage, array $array = [], $active = null);

    public function usersSearch(string $search, $active = null,  $roles = []);
}
