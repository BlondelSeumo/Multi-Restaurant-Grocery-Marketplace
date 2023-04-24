<?php

namespace App\Services\SmsPayloadService;

use App\Helpers\ResponseError;
use App\Models\SmsPayload;
use App\Services\CoreService;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SmsPayloadService extends CoreService
{
    protected function getModelClass(): string
    {
        return SmsPayload::class;
    }

    public function create(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator|array
    {
        $prepareValidate = $this->prepareValidate($data);

        if (!data_get($prepareValidate, 'status')) {
            return $prepareValidate;
        }

        try {

            if ((int)data_get($data, 'default') === 1) {
                SmsPayload::where('default', 1)->get()->map(function (SmsPayload $payload) {
                    $payload->update([
                        'default' => 0
                    ]);
                });
            }

            $payload = $this->model()->create($data);

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $payload,
            ];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_501,
            'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
        ];
    }

    public function update(string $smsType, array $data): array
    {
        try {
            $data['type'] = $smsType;

            $prepareValidate = $this->prepareValidate($data);

            if (!data_get($prepareValidate, 'status')) {
                return $prepareValidate;
            }

            $payload = SmsPayload::where('type', $smsType)->firstOrFail();
            if ((int)data_get($data, 'default') === 1) {
                SmsPayload::where('default', 1)->get()->map(function (SmsPayload $payload) {
                    $payload->update([
                        'default' => 0
                    ]);
                });
            }
            $payload->update($data);

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $payload,
            ];
        } catch (Throwable $e) {
            $this->error($e);
        }

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_502,
            'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
        ];
    }

    public function delete(?array $ids = []): array
    {
        $payloads = SmsPayload::whereIn('type', is_array($ids) ? $ids : [])->get();

        foreach ($payloads as $payload) {
            $payload->delete();
        }

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
        ];
    }

    public function prepareValidate($data): array
    {
        if (data_get($data, 'type') === SmsPayload::FIREBASE) {

            $validator = $this->firebase($data);

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'code'   => ResponseError::ERROR_422,
                    'params' => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];

        } else if (data_get($data, 'type') === SmsPayload::TWILIO) {

            $validator = $this->twilio($data);

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'code'   => ResponseError::ERROR_422,
                    'params' => $validator->errors()->toArray(),
                ];
            }

            return ['status' => true];
        }

        return [
            'status'    => false,
            'code'      => ResponseError::ERROR_404,
            'message'   => 'Validation error',
        ];
    }
    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function firebase(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.api_key'           => ['required', 'string'],
            'payload.ios_api_key'       => ['required', 'string'],
            'payload.android_api_key'   => ['required', 'string'],
            'payload.server_key'        => ['required', 'string'],
            'payload.vapid_key'         => ['required', 'string'],
            'payload.auth_domain'       => ['required', 'string'],
            'payload.project_id'        => ['required', 'string'],
            'payload.storage_bucket'    => ['required', 'string'],
            'payload.message_sender_id' => ['required', 'string'],
            'payload.app_id'            => ['required', 'string'],
            'payload.measurement_id'    => ['required', 'string'],
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function twilio(array $data): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'payload.twilio_account_id' => ['required', 'string'],
            'payload.twilio_auth_token' => ['required', 'string'],
            'payload.twilio_number'     => ['required', 'string'],
        ]);
    }
}
