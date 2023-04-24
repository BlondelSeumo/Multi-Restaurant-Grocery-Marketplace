<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

abstract class UserBaseController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('sanctum.check')->except(['successTransaction', 'cancelTransaction']);
    }
}
