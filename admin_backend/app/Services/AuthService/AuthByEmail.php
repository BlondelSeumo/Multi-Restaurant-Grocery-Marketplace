<?php

namespace App\Services\AuthService;

use App\Events\Mails\SendEmailVerification;
use App\Helpers\ResponseError;
use App\Models\User;
use App\Services\CoreService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class AuthByEmail extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    public function authentication(array $array): JsonResponse
    {
        /** @var User $user */

        $user = $this->model()
            ->withTrashed()
            ->updateOrCreate([
                'email'         => data_get($array, 'email')
            ], [
                'firstname'     => data_get($array, 'email', data_get($array, 'email')),
                'email'         => data_get($array, 'email'),
                'ip_address'    => request()->ip(),
                'deleted_at'    => null
        ]);

        if (!$user->hasAnyRole(Role::query()->pluck('name')->toArray())) {
            $user->syncRoles('user');
        }

        event((new SendEmailVerification($user)));

        return $this->successResponse(__('errors.' . ResponseError::ERROR_217, locale: $this->language), []);
    }

}
