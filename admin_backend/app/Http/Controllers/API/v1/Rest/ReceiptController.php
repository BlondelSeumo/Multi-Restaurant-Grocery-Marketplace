<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\RestReceiptResource;
use App\Models\Receipt;
use App\Repositories\ReceiptRepository\ReceiptRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReceiptController extends RestBaseController
{
    public function __construct(private ReceiptRepository $repository)
    {
        parent::__construct();
    }

    /**
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        return RestReceiptResource::collection($this->repository->restPaginate($request->all()));
    }

    /**
     * @param int $id
     * @return RestReceiptResource|JsonResponse
     */
    public function show(int $id): RestReceiptResource|JsonResponse
    {
        $receipt = Receipt::find($id);

        if (empty($receipt)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return RestReceiptResource::make($this->repository->restShow($receipt));
    }

}
