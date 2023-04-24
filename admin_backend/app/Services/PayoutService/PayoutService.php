<?php

namespace App\Services\PayoutService;

use App\Helpers\ResponseError;
use App\Models\Payout;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Services\CoreService;
use App\Services\WalletHistoryService\WalletHistoryService;
use Throwable;

class PayoutService extends CoreService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $data['status'] = 'pending';
            $this->model()->create($data);

            return [
                'status'  => true,
                'message' => ResponseError::NO_ERROR,
            ];

        } catch (Throwable $e) {

            $this->error($e);

            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function update(Payout $payout, array $data): array
    {
        try {
            $payout->update($data);

            return [
                'status'  => true,
                'message' => ResponseError::NO_ERROR,
            ];

        } catch (Throwable $e) {

            $this->error($e);

            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function delete(?array $ids = []) {

        foreach (Payout::find(is_array($ids) ? $ids : []) as $payout) {

            if ($payout->created_by !== auth('sanctum')->id()) {
                continue;
            }

            $payout->delete();
        }

    }

    public function statusChange(int $id, ?string $status = null): array
    {
        if (!in_array($status, Payout::STATUSES)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_253,
                'message' => __('errors.' . ResponseError::ERROR_253, locale: $this->language)
            ];
        }

        $payout = Payout::find($id);

        if (empty($payout)) {
            return [
                'status' => false,
                'code'   => ResponseError::ERROR_404
            ];
        }

        if ($payout->status === Payout::STATUS_ACCEPTED) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_400,
                'message'   => __(
                    'errors.' . ResponseError::PAYOUT_ACCEPTED,
                    [
                        'status' => Payout::STATUS_ACCEPTED
                    ],
                    $this->language
                )
            ];
        }

        if (empty($payout->createdBy)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        /** @var Wallet $authWallet */
        $authWallet = auth('sanctum')->user()->wallet;

        if ((data_get($authWallet, 'price', 0)) < $payout->price) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_109,
                'message'   => __('errors.' . ResponseError::ERROR_109, locale: $this->language)
            ];
        }

        $payout->update([
            'status'        => $status,
            'approved_by'   => auth('sanctum')->id(),
        ]);


        $createdByNote = "Receipts for {$payout->createdBy->firstname}/{$payout->createdBy->lastname}";

        $approveBydNote = "Payment for {$payout->approvedBy->firstname}/{$payout->approvedBy->lastname}";

        if (!$payout->createdBy?->wallet?->id) {
            return ['status' => false, 'code' => ResponseError::ERROR_108, 'message' => $payout->createdBy?->firstname];
        }

        if ($status === Payout::STATUS_ACCEPTED && $payout->payment?->tag === 'wallet') {
            $this->walletHistory($payout, $authWallet, $createdByNote, $approveBydNote);
        }

        $payout->createdBy->wallet->createTransaction([
            'price'                 => $payout->price,
            'user_id'               => $payout->created_by,
            'payment_sys_id'        => $payout->payment_id,
            'payment_trx_id'        => null,
            'note'                  => $createdByNote,
            'perform_time'          => now(),
            'status_description'    => $createdByNote
        ]);

        $authWallet->createTransaction([
            'price'                 => $payout->price,
            'user_id'               => $payout->approved_by,
            'payment_sys_id'        => $payout->payment_id,
            'payment_trx_id'        => null,
            'note'                  => $approveBydNote,
            'perform_time'          => now(),
            'status_description'    => $approveBydNote
        ]);

        return ['status' => true, 'code' => ResponseError::NO_ERROR];
    }

    public function walletHistory(Payout $payout, Wallet $authWallet, string $createdByNote, string $approveBydNote) {
        (new WalletHistoryService)->create([
            'type'      => 'topup',
            'price'     => $payout->price,
            'note'      => $createdByNote,
            'status'    => WalletHistory::PAID,
            'user'      => $payout->createdBy
        ]);

        $authWallet->update([
            'price' => $authWallet->price - $payout->price,
        ]);

        (new WalletHistoryService)->create([
            'type'      => 'withdraw',
            'price'     => $payout->price,
            'note'      => $approveBydNote,
            'status'    => WalletHistory::PAID,
            'user'      => $payout->approvedBy
        ]);
    }
}
