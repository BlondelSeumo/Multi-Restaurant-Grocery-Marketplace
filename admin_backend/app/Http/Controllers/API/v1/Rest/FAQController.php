<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\FAQResource;
use App\Models\Faq;
use App\Models\Language;
use App\Models\PrivacyPolicy;
use App\Models\TermCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FAQController extends RestBaseController
{
    /**
     * Display a listing of the FAQ.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        $faqs = Faq::with([
                'translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
            ])
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale))
            ->where('active', 1)
            ->when($request->input('deleted_at'), fn($q) => $q->onlyTrashed())
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 10));

        return FAQResource::collection($faqs);
    }

    /**
     * Display Terms & Condition.
     *
     * @return JsonResponse
     */
    public function term(): JsonResponse
    {
        $model = TermCondition::with([
            'translation' => fn($q) => $q->where('locale', $this->language)
        ])->first();

        if (empty($model)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $model);
    }

    /**
     * Display Terms & Condition.
     *
     * @return JsonResponse
     */
    public function policy(): JsonResponse
    {
        $model = PrivacyPolicy::with([
            'translation' => fn($q) => $q->where('locale', $this->language)
        ])->first();

        if (empty($model)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $model);
    }

}
