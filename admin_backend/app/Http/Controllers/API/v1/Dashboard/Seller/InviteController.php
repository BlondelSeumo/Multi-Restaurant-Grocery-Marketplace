<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\InviteResource;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InviteController extends SellerBaseController
{
    public function paginate(FilterParamsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $invites = Invitation::filter($request->all())->with([
            'user.roles',
            'user' => fn($q) => $q->select('id', 'firstname', 'lastname'),
            'shop.translation' => fn($q) => $q->where('locale', $this->language)
        ])
            ->where('shop_id', $this->shop->id)
            ->orderBy($request->input('column', 'id'), $request->input('sort', 'desc'))
            ->paginate($request->input('perPage', 10));

        return InviteResource::collection($invites);
    }

    public function changeStatus(int $id): InviteResource|JsonResponse
    {
        $invite = Invitation::firstWhere(['id' => $id, 'shop_id' => $this->shop->id]);

        if (!$invite) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $data = [
            'status'    => data_get(Invitation::STATUS, 'rejected'),
            'role'      => 'user'
        ];

        $role = 'user';

        if (request('role') == 'moderator' || request('role') == 'deliveryman') {

            $data = [
                'status'    => data_get(Invitation::STATUS, 'excepted'),
                'role'      => request('role')
            ];

            $role = request('role');

        }

        $invite->update($data);

        $invite->user->syncRoles($role);

        return InviteResource::make($invite);
    }

}
