<?php

namespace App\Services\SMSGatewayService;

use App\Helpers\ResponseError;
use App\Models\SmsGateway;
use App\Services\CoreService;
use Throwable;

class SmsGatewayService extends CoreService
{
    protected function getModelClass(): string
    {
        return SmsGateway::class;
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
            return [
                'status' => false,
                'code'   => ResponseError::ERROR_501,
            ];
        }
    }

    public function update(SmsGateway $smsGateway, array $data): array
    {
        try {
            $smsGateway->update($data);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $smsGateway,
            ];
        } catch (Throwable $e) {
            return [
                'status' => false,
                'code'   => ResponseError::ERROR_501,
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
        /** @var SmsGateway $smsGateway */
        $smsGateway = $this->model()->find($id);

        if (empty($smsGateway)) {
            return [
                'status' => false,
                'code'   => ResponseError::ERROR_501,
            ];
        }

        $smsGateway->update(['active' => !$smsGateway->active]);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => $smsGateway,
        ];
    }
}
