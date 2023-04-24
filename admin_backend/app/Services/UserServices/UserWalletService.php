<?php

namespace App\Services\UserServices;

use App\Helpers\ResponseError;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CoreService;
use Exception;
use Illuminate\Support\Str;
use Throwable;

class UserWalletService extends CoreService
{
    protected function getModelClass(): string
    {
        return Wallet::class;
    }

    /**
     * @param User $user
     * @return User
     */
    public function create(User $user): User
    {
        try {
            $this->model()
                ->withTrashed()
                ->updateOrCreate(['user_id' => $user->id], [
                    'uuid'          => Str::uuid(),
                    'user_id'       => $user->id,
                    'currency_id'   => Currency::whereDefault(1)->pluck('id')->first(),
                    'price'         => 0,
                    'deleted_at'    => null,
                ]);

        } catch (Throwable $e) {
            $this->error($e);
        }

        return $user->loadMissing('wallet');
    }

    public function update(User $user, array $array): array
    {
        try {
            $user->wallet()
                ->withTrashed()
                ->updateOrCreate([
                    'user_id' => $user->id
                ],[
                    'price' => ($user->wallet?->price ?? 0) + data_get($array, 'price'),
                    'deleted_at' => null,
                    'uuid' => $user->wallet?->uuid ?? Str::uuid(),
                    'currency_id' => $this->currency
                ]);

            $this->historyCreate($user->fresh(['wallet'])->wallet, $array);
            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $user];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function historyCreate($wallet, $array) {

        $wallet->histories()->create([
            'uuid'              => Str::uuid(),
            'transaction_id'    => data_get($array, 'transaction_id'),
            'type'              => data_get($array, 'type', 'topup'),
            'price'             => data_get($array, 'price', 0),
            'note'              => data_get($array, 'note'),
            'created_by'        => auth('sanctum')->id(),
        ]);
    }
}
