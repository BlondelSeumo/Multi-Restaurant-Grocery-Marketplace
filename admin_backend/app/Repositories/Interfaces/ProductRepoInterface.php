<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;

interface ProductRepoInterface
{
    public function productsPaginate(array $filter);

    public function productDetails(int $id);

    public function productByUUID(string $uuid): ?Product;

    public function productsByIDs(array $filter = []);

    public function productsSearch(array $filter = []);

    public function selectStockPaginate(array $data);

    public function reportChart(array $filter);

    public function productReportPaginate(array $filter);

    public function stockReportPaginate(array $filter);

    public function extrasReportPaginate(array $filter);

    public function history(array $filter);

    public function mostPopulars(array $filter);
}
