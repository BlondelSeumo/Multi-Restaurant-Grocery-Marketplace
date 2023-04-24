<?php

namespace App\Repositories\Interfaces;

interface CountryRepoInterface
{
    public function countriesList($array = []);

    public function countriesPaginate($perPage = 15, $array = []);

    public function countriesDetails($id);
}
