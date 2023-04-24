<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Models\Language;
use App\Traits\Loggable;
use Cache;

abstract class CoreRepository
{
    use Loggable;

    protected object $model;
    protected $currency;
    protected $language;
    protected string $updatedDate;

    /**
     * CoreRepository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->getModelClass());
        $this->language = $this->setLanguage();
        $this->currency = $this->setCurrency();
        $this->updatedDate = request('updated_at', '2021-01-01');
    }

    abstract protected function getModelClass();

    protected function model()
    {
        return clone $this->model;
    }

    /**
     * Set default Currency
     */
    protected function setCurrency() {
        return request(
            'currency_id',
            data_get(Currency::where('default', 1)->first(['id', 'default']), 'id')
        );
    }

    /**
     * Set default Language
     */
    protected function setLanguage() {
        return request(
            'lang',
            data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale')
        );
    }

}
