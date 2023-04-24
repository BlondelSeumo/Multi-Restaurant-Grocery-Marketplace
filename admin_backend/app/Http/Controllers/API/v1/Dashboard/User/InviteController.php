<?php

namespace App\Http\Controllers\API\v1\Dashboard\User;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\InviteResource;
use App\Models\Invitation;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InviteController extends UserBaseController
{
    private Invitation $model;

    public function __construct(Invitation $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $invites = Invitation::filter($request->all())->with([
            'user.roles',
            'user' => function($q) {
                $q->select('id', 'firstname', 'lastname');
            },
            'shop.translation' => fn($q) => $q->where('locale', $this->language)
        ])
            ->where('user_id', auth('sanctum')->id())
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 15));

        return InviteResource::collection($invites);
    }

    /**
     * @param $shop
     * @return JsonResponse
     */
    public function create($shop): JsonResponse
    {
        $shop = Shop::firstWhere('uuid', $shop);

        $invite = $this->model->updateOrCreate(['user_id' => auth('sanctum')->id()],[
            'shop_id' => $shop->id,
        ]);

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            InviteResource::make($invite)
        );
    }
}
