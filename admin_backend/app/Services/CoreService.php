<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Language;
use App\Traits\ApiResponse;
use App\Traits\Loggable;
use Cache;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Throwable;

abstract class CoreService
{
    use ApiResponse, Loggable;

    private mixed $model;
    protected string $language;
    protected int $currency;

    public function __construct()
    {
        $this->model    = app($this->getModelClass());
        $this->language = $this->setLanguage();
        $this->currency = $this->setCurrency();
    }

    abstract protected function getModelClass();

    protected function model()
    {
        return clone $this->model;
    }

    /**
     * Set default Currency
     */
    protected function setCurrency(): int
    {
        $default = Currency::where('default', 1)->first(['id', 'default'])?->id ?? null;

        $currency = request('currency_id', $default);

        if (is_bool($currency) || is_object($currency) || is_array($currency)) {
            $currency = $default;
        }

        return (int)$currency;
    }

    protected function setLanguage(): string
    {
        $default = Language::where('default', 1)->first(['locale', 'default'])?->locale ?? 'en';

        $lang = request('lang', $default);

        if (!is_string($lang)) {
             $lang = $default;
        }

        return (string)$lang;
    }

    /**
     * @param array|null $exclude
     * @return void
     */
    public function dropAll(?array $exclude = []): void
    {
        /** @var Model|Language $models */

        $models = $this->model();

        $models = $models->when(data_get($exclude, 'column') && data_get($exclude, 'value'),
            function (Builder $query) use($exclude) {
                $query->where(data_get($exclude, 'column'), '!=', data_get($exclude, 'value'));
            }
        )->get();

        foreach ($models as $model) {

            try {

                $model->delete();

            } catch (Throwable $e) {

                $this->error($e);

            }

        }

        Cache::flush();
    }

    /**
     * @return void
     */
    public function restoreAll(): void
    {
        /** @var Model|Language $models */
        $models = $this->model();

        foreach ($models->withTrashed()->whereNotNull('deleted_at')->get() as $model) {

            try {

                $model->update([
                    'deleted_at' => null
                ]);

            } catch (Throwable $e) {

                $this->error($e);

            }

        }

        Cache::flush();
    }

    /**
     * @param string $name
     * @return void
     */
    public function truncate(string $name = ''): void
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table($name ?: $this->model()->getTable())->truncate();
        DB::statement("SET foreign_key_checks = 1");

        Cache::flush();
    }

    /**
     * @param array $ids
     * @return array|int[]|void
     */
    public function destroy(array $ids)
    {
        foreach ($this->model()->whereIn('id', $ids)->get() as $model) {
            try {
                $model->delete();
            } catch (Throwable $e) {
                $this->error($e);
            }
        }

        Cache::flush();
    }

    /**
     * @param array $ids
     * @return array|int|int[]|void
     */
    public function delete(array $ids)
    {
        $this->destroy($ids);
        Cache::flush();
    }
}
