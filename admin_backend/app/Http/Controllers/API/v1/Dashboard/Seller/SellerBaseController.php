<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Traits\ApiResponse;

abstract class SellerBaseController extends Controller
{
    use ApiResponse;

    protected Shop|null $shop;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('check.shop')
            ->except('shopCreate', 'shopShow', 'shopUpdate');

        $this->shop = auth('sanctum')->user()?->shop ?? auth('sanctum')->user()?->moderatorShop;
    }

}
