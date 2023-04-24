<?php

namespace App\Http\Controllers\API\v1\Dashboard\Payment;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

abstract class PaymentBaseController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['sanctum.check'])->except('created');
    }
}
