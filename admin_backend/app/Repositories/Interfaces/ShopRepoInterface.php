<?php

namespace App\Repositories\Interfaces;

interface ShopRepoInterface
{
    public function shopsList(array $filter);

    public function shopsPaginate(array $filter);

    public function selectPaginate(array $filter);

    public function shopDetails(string $uuid);

    public function takes();

    public function productsAvgPrices();

    public function shopsSearch(array $filter);

    public function shopsByIDs(array $filter);

    public function recommended(array $filter);

    public function products(array $filter);

    public function categories(array $filter);

    public function productsPaginate(array $filter);

    public function productsRecommendedPaginate(array $filter);

}
