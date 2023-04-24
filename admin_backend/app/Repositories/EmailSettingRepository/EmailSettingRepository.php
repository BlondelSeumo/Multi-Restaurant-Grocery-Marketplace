<?php

namespace App\Repositories\EmailSettingRepository;

use App\Models\EmailSetting;
use App\Repositories\CoreRepository;
use Illuminate\Support\Collection;

class EmailSettingRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return EmailSetting::class;
    }

    /**
     * Для теста на будущее
     * @param array $data
     * @return Collection
     */
    public function get(array $data = []): Collection
    {
        /** @var EmailSetting $emailSettings */
        $emailSettings = $this->model();

        return $emailSettings
            ->list()
            ->when(data_get($data, 'host'),
                fn($q, $host) => $q->where('host', 'like', "%$host%")
            )
            ->when(isset($data['active']),
                fn($q, $active) => $q->where('active', $active)
            );
    }

    /**
     * @param EmailSetting $emailSetting
     * @return EmailSetting
     */
    public function show(EmailSetting $emailSetting): EmailSetting
    {
        return $emailSetting;
    }
}
