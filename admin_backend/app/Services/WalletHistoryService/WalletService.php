<?php

namespace App\Services\WalletHistoryService;

use App\Models\Wallet;
use App\Services\CoreService;

class WalletService extends CoreService
{
    protected function getModelClass(): string
    {
        return Wallet::class;
    }
}
