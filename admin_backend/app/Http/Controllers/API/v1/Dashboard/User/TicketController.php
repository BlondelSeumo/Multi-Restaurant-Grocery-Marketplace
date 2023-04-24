<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Helpers\ResponseError;
use App\Http\Requests\Ticket\StoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Repositories\TicketRepository\TicketRepository;
use App\Services\TicketService\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends UserBaseController
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
        $this->ticketService    = $ticketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $categories = $this->ticketRepository->paginate(
            $request->merge(['created_by' => auth('sanctum')->id()])->all()
        );

        return TicketResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = auth('sanctum')->id();

        $result = $this->ticketService->create($validated);

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
        if ($ticket->created_by !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(ResponseError::NO_ERROR, TicketResource::make($ticket));
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
        if ($ticket->created_by !== auth('sanctum')->id()) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $validated = $request->validated();
        $validated['created_by'] = auth('sanctum')->id();

        $result = $this->ticketService->update($ticket, $validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED),
            TicketResource::make(data_get($result, 'data'))
        );
    }

}
