<?php

namespace App\Repositories\TicketRepository;

use App\Models\Ticket;
use App\Repositories\CoreRepository;
use Illuminate\Support\Facades\Cache;

class TicketRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Ticket::class;
    }

    public function paginate(array $filter)
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return $this->model()->with('children')
            ->when(data_get($filter, 'created_by'), fn ($q, $createdBy) => $q->where('created_by', $createdBy))
            ->where('parent_id', 0)
            ->filter($filter)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }
}
