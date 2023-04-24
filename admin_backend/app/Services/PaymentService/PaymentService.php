<?php

namespace App\Services\PaymentService;

use App\Helpers\ResponseError;
use App\Models\Payment;
use App\Services\CoreService;
use Throwable;

class PaymentService extends CoreService
{
    protected function getModelClass(): string
    {
        return Payment::class;
    }

    public function create(array $data): array
    {
        try {
            $payment = $this->model()->create($data);
            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $payment,
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

    public function update(Payment $payment, array $data): array
    {
        try {
            $payment->update([
                'sandbox' => data_get($data,'sandbox', 0),
            ]);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $payment,
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

    /**
     * Change Active Status of Model.
     *
     * @param int $id
     * @return array
     */
    public function setActive(int $id): array
    {
        /** @var Payment $payment */
        $payment = $this->model()->find($id);

        if (empty($payment)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $payment->update(['active' => !$payment->active]);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => $payment,
        ];
    }
}
