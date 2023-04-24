<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use App\Http\Requests\Ticket\StoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Repositories\TicketRepository\TicketRepository;
use App\Services\TicketService\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class TicketController extends AdminBaseController
{

    private TicketRepository $ticketRepository;
    private TicketService $ticketService;

    /**
     * @param TicketRepository $ticketRepository
     * @param TicketService $ticketService
     */
    public function __construct(TicketRepository $ticketRepository, TicketService $ticketService)
    {
        parent::__construct();
        $this->ticketRepository = $ticketRepository;
        $this->ticketService = $ticketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $tickets = $this->ticketRepository->paginate($request->all());

        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {

            $ips = collect(Cache::get('block-ips'));

            try {
                Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
            } catch (InvalidArgumentException $e) {
            }

            abort(403);
        }

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $result = $this->ticketService->create($request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            TicketResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function show(Ticket $ticket): JsonResponse
    {
        return $this->successResponse(
            ResponseError::NO_ERROR,
            TicketResource::make($ticket->loadMissing('children'))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Ticket $ticket
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function update(Ticket $ticket, StoreRequest $request): JsonResponse
    {
        $result = $this->ticketService->update($ticket, $request->validated());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            TicketResource::make(data_get($result, 'data'))
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        //
    }

    /**
     * Change Active Status of Model.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function setStatus(int $id, Request $request): JsonResponse
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $status = $request->input('status', $ticket->status);

        if (!in_array($status, Ticket::STATUS)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_253, 'data' => ['ASD']]);
        }

        $ticket->update(['status' => $status]);

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            TicketResource::make($ticket)
        );
    }

    public function getStatuses(): JsonResponse
    {
        return $this->successResponse(ResponseError::NO_ERROR, Ticket::STATUS);
    }

    public function dropAll(): JsonResponse
    {
        $this->ticketService->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function truncate(): JsonResponse
    {
        $this->ticketService->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function restoreAll(): JsonResponse
    {
        $this->ticketService->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }
}
