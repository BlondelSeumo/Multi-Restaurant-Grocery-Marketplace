<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Resources\BrandResource;
use App\Repositories\BrandRepository\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends RestBaseController
{
    private BrandRepository  $brandRepository;
    /**
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        parent::__construct();

        $this->brandRepository = $brandRepository;
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        $brands = $this->brandRepository->brandsPaginate($request->merge(['active' => 1])->all());

        return BrandResource::collection($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $brand = $this->brandRepository->brandDetails($id);

        if (empty($brand)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.'. ResponseError::NO_ERROR, locale: $this->language),
            BrandResource::make($brand)
        );
    }
}
