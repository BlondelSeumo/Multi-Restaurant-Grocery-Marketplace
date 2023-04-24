<?php

namespace App\Services\UserServices;

use App\Helpers\ResponseError;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\CoreService;
use Exception;
use Jenssegers\Agent\Agent;

class UserActivityService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * @param int $modelId
     * @param string $modelType
     * @param string $type
     * @param string|int $value
     * @param User|null $user
     * @return array
     */
    public function create(int $modelId, string $modelType, string $type, string|int $value, User|null $user): array
    {
        try {
            $agent = new Agent;

            $attributes = [
                'model_id'          => $modelId,
                'model_type'        => $modelType,
                'device'            => $agent->device(),
                'ip'                => request()->ip(),
                'agent->browser'    => $agent->browser(),
            ];

            $values = [
                'type'       => $type,
                'ip'         => request()->ip(),
                'device'     => $agent->device(),
                'agent'      => [
                    'device'        => $agent->device(),
                    'platform'      => $agent->platform(),
                    'browser'       => $agent->browser(),
                    'robot'         => $agent->robot(),
                    'deviceType'    => $agent->deviceType(),
                    'languages'     => $agent->languages(),
                    'getUserAgent'  => $agent->getUserAgent(),
                    'ip'            => request()->ip(),
                ]
            ];

            $activity = !empty($user) ?
                $user->activities()->updateOrCreate($attributes, $values) :
                UserActivity::updateOrCreate($attributes, $values);

            $activity->value .= "| $value";

            if (is_int($value)) {
                $activity->value = ((int)$activity->value ?? 0) + $value;
            }

            $activity->save();

            return [
                'status'  => true,
                'code'    => ResponseError::SUCCESS,
                'message' => __('errors.' . ResponseError::SUCCESS, locale: $this->language)
            ];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'    => false,
                'code'      => $e->getCode(),
                'message'   => $e->getMessage()
            ];
        }
    }

}
