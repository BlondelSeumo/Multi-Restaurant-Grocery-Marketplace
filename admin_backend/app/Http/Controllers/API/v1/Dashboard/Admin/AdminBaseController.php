<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

abstract class AdminBaseController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['sanctum.check', 'role:admin|manager']);
    }
}
