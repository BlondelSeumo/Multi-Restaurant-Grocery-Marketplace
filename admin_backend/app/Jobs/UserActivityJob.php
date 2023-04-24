<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\UserServices\UserActivityService;
use App\Traits\Loggable;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class UserActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Loggable;

    /**
     * Create a new event instance.
     *
     * @param int $modelId
     * @param string $modelType
     * @param string $type
     * @param string|int $value
     * @param User|null $user
     */
    public function __construct(
        private int $modelId,
        private string $modelType,
        private string $type,
        private string|int $value,
        private User|null $user
    )
    {}

    /**
     * Handle the event
     * @return void
     */
    public function handle(): void
    {
        try {
            $activity = new UserActivityService;
            $activity->create($this->modelId, $this->modelType, $this->type, $this->value, $this->user);
        } catch (Exception $e) {
            $this->error($e);
        }
    }
}
