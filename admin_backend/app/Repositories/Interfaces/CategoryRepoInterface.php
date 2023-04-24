<?php

namespace App\Repositories\Interfaces;

interface CategoryRepoInterface
{
    public function childrenCategory(int $id);

    public function categoryDetails(int $id);

    public function categoryByUuid(string $uuid);

    public function parentCategories(array $filter = []);

    public function categoriesList(array $filter = []);

    public function categoriesPaginate(array $filter = []);

    public function categoriesSearch(array $filter = []);

    public function shopCategoryProduct(array $filter = []);

    public function shopCategory(array $filter = []);

    public function reportChart(array $filter = []);
}
