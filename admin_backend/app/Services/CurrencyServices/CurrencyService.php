<?php
namespace App\Services\CurrencyServices;

use App\Helpers\ResponseError;
use App\Models\Currency;
use App\Services\CoreService;
use App\Services\Interfaces\CurrencyServiceInterface;
use Exception;
use Throwable;

class CurrencyService extends CoreService implements CurrencyServiceInterface
{
    protected function getModelClass(): string
    {
        return Currency::class;
    }

    public function create(array $data): array
    {
        $first = $this->model()->first();

        try {
            $currency = $this->model()->create($data);

            $first ?? $this->setCurrencyDefault($currency);

            cache()->forget('currencies-list');

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $currency];

        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    /**
     * @param Currency $currency
     * @param array $data
     * @return array
     */
    public function update(Currency $currency, array $data): array
    {
        try {
            $currency->update($data);

            cache()->forget('currencies-list');

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $currency];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    /**
     * @param array|null $ids
     * @return void
     */
    public function delete(?array $ids = []): void
    {
        foreach (Currency::whereIn('id', is_array($ids) ? $ids : [])->get() as $currency) {
            /** @var Currency $currency */

            if ($currency->default) {
                continue;
            }

            $currency->delete();
        }

        try {
            cache()->forget('currencies-list');
        } catch (Throwable) {}
    }


    private function setCurrencyDefault(Currency $currency) {
        $currency->default = 1;
        $currency->active = 1;
        $currency->save();
    }

}
