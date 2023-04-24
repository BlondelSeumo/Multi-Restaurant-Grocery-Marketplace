<?php

namespace App\Services\Interfaces;

use App\Models\Currency;

interface CurrencyServiceInterface
{
    public function create(array $data);

    public function update(Currency $currency, array $data);

    public function delete(?array $ids = []);

}
