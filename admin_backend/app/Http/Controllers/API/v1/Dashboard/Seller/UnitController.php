<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Resources\UnitResource;
use App\Repositories\UnitRepository\UnitRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends SellerBaseController
{
    private UnitRepository $unitRepository;

    /**
     * @param UnitRepository $unitRepository
     */
    public function __construct(UnitRepository $unitRepository)
    {
        parent::__construct();
        $this->unitRepository = $unitRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $units = $this->unitRepository->unitsPaginate($request->all());

        return UnitResource::collection($units);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $unit = $this->unitRepository->unitDetails($id);

        if (empty($unit)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UnitResource::make($unit)
        );
    }
}
