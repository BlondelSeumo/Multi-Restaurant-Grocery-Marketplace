<?php

namespace App\Services\AuthService;

use App\Helpers\ResponseError;
use App\Http\Resources\UserResource;
use App\Models\Notification;
use App\Models\User;
use App\Services\CoreService;
use App\Services\SMSGatewayService\SMSBaseService;
use App\Services\UserServices\UserWalletService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class AuthByMobilePhone extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * @param array $array
     * @return JsonResponse
     */
    public function authentication(array $array): JsonResponse
    {
        $phone = preg_replace('/\D/', '', data_get($array, 'phone'));

        $sms = (new SMSBaseService)->smsGateway($phone);

        if (!data_get($sms, 'status')) {

            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_400,
                'message' => data_get($sms, 'message', '')
            ]);

        }

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), [
            'verifyId'  => data_get($sms, 'verifyId'),
            'phone'     => data_get($sms, 'phone'),
            'message'   => data_get($sms, 'message', '')
        ]);
    }

    /**
     * @todo REMOVE IN THE FUTURE
     * @param array $array
     * @return JsonResponse
     */
    public function confirmOPTCode(array $array): JsonResponse
    {
        if (data_get($array, 'type') !== 'firebase') {

            $data = Cache::get('sms-' . data_get($array, 'verifyId'));

            if (empty($data)) {
                return $this->onErrorResponse([
                    'code'      => ResponseError::ERROR_404,
                    'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
                ]);
            }

            $count = $this->setOTPCount($data);

            if ($count == 0) {
                return $this->onErrorResponse([
                    'code'      => ResponseError::ERROR_202,
                    'message'   => __('errors.' . ResponseError::ERROR_202, locale: $this->language)
                ]);
            }

            if (Carbon::parse(data_get($data, 'expiredAt')) < now()) {
                return $this->onErrorResponse([
                    'code'      => ResponseError::ERROR_203,
                    'message'   => __('errors.' . ResponseError::ERROR_203, locale: $this->language)
                ]);
            }

            if (data_get($data, 'OTPCode') !== data_get($data, 'verifyCode')) {
                return $this->onErrorResponse([
                    'code'      => ResponseError::ERROR_201,
                    'message'   => __('errors.' . ResponseError::ERROR_201, locale: $this->language)
                ]);
            }

            $user = $this->model()->where('phone', data_get($data, 'phone'))->first();

        } else {
            $data['phone']      = data_get($array, 'phone');
            $data['referral']   = data_get($array, 'referral');
            $data['firstname']  = data_get($array, 'firstname');
            $data['lastname']   = data_get($array, 'lastname');
            $data['password']   = data_get($array, 'password');
            $data['gender']     = data_get($array, 'gender', 'male');
        }

        if (empty($user)) {
            $user = $this->model()
                ->withTrashed()
                ->updateOrCreate([
                    'phone'             => data_get($data, 'phone')
                ], [
                    'phone'             => data_get($data, 'phone'),
                    'referral'          => data_get($data, 'referral'),
                    'active'            => 1,
                    'phone_verified_at' => now(),
                    'deleted_at'        => null,
                    'firstname'         => data_get($data, 'firstname'),
                    'lastname'          => data_get($data, 'lastname'),
                    'gender'            => data_get($data, 'gender'),
                    'password'          => bcrypt(data_get($data, 'password', 'password')),
                ]);

            $id = Notification::where('type', Notification::PUSH)->select(['id', 'type'])->first()?->id;

            if ($id) {
                $user->notifications()->sync([$id]);
            } else {
                $user->notifications()->forceDelete();
            }

            $user->emailSubscription()->updateOrCreate([
                'user_id' => $user->id
            ], [
                'active' => true
            ]);
        }

        if (!$user->hasAnyRole(Role::query()->pluck('name')->toArray())) {
            $user->syncRoles('user');
        }

        if(empty($user->wallet)) {
            $user = (new UserWalletService)->create($user);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        Cache::forget('sms-' . data_get($array, 'verifyId'));

        return $this->successResponse(__('errors.'. ResponseError::SUCCESS, locale: $this->language), [
            'token' => $token,
            'user'  => UserResource::make($user),
        ]);

    }

    public function forgetPasswordVerify(array $data): JsonResponse
    {
        $user = User::withTrashed()->where('phone', data_get($data, 'phone'))->first();

        if (!empty($user->deleted_at)) {
            $user->update([
                'deleted_at' => null
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return $this->successResponse(__('errors.'. ResponseError::SUCCESS, locale: $this->language), [
            'token' => $token,
            'user'  => UserResource::make($user),
        ]);
    }

    public function setOTPCount(array $array)
    {
        $verifyId = data_get($array, 'verifyId');

        $count = Cache::get($verifyId);

        if ($count > 0) {
            Cache::forget($verifyId);
            Cache::put($verifyId, $count - 1, 300);
        }

        return $count;
    }

}
