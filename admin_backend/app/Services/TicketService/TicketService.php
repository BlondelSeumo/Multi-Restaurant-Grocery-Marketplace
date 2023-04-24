<?php

namespace App\Services\TicketService;

use App\Helpers\ResponseError;
use App\Models\Ticket;
use App\Services\CoreService;
use Exception;
use Illuminate\Http\JsonResponse;

class TicketService extends CoreService
{
    protected function getModelClass(): string
    {
        return Ticket::class;
    }

    public function create(array $data): array
    {
        try {
            $ticket = $this->model()->create($data);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $ticket];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    public function update(Ticket $ticket, array $data): array
    {
        try {
            $result = $ticket->update($data);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $result];
        } catch (Exception $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function setStatus(int $id, ?string $status): JsonResponse|array
    {

        $ticket = Ticket::find($id);

        if (empty($ticket)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $status = $status ?: $ticket->status;

        if (!in_array($status, Ticket::STATUS)) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_253,
                'message' => __('errors.' . ResponseError::ERROR_253, locale: $this->language)
            ];
        }

        $ticket->update(['status' => $status]);

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $ticket];
    }
}
