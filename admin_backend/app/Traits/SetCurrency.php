<?php

namespace App\Traits;

use App\Models\Currency;

trait SetCurrency
{
    public function currency(): float
    {
        $currency = request('currency_id') ?
            Currency::currenciesList()->where('id', (int)request('currency_id'))->first() :
            Currency::currenciesList()->where('default', 1)->first();

        return data_get($currency, 'rate', 1);
    }

}
