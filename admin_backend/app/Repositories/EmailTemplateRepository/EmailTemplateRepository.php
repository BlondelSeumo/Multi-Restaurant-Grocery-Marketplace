<?php

namespace App\Repositories\EmailTemplateRepository;

use App\Models\EmailTemplate;
use App\Repositories\CoreRepository;
use Illuminate\Support\Facades\Cache;

class EmailTemplateRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return EmailTemplate::class;
    }

    public function paginate(array $filter) {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return $this->model()->paginate(data_get($filter, 'perPage', 10));
    }

    public function show(EmailTemplate $emailTemplate): EmailTemplate
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        return $emailTemplate->loadMissing(['emailSetting']);
    }
}
