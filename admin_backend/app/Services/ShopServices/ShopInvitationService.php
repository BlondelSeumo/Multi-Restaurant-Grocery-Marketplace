<?php

namespace App\Services\ShopServices;

//use App\Helpers\ResponseError;
use App\Models\Shop;
use App\Services\CoreService;

class ShopInvitationService extends CoreService
{
    protected function getModelClass(): string
    {
        return Shop::class;
    }

//    public function createInvitation(string $uuid, int $id): array
//    {
//       $shop = $this->model()->with('users')->firstWhere(['uuid' => $uuid, 'id' => $id]);
//
//       if (!$shop) {
//           return ['status' => false, 'code' => ResponseError::ERROR_404];
//       }
//
//        $shop->invitations()->updateOrCreate(['user_id' => auth('sanctum')->id()], [
//            'status' => 'new',
//        ]);
//
//        return  ['status' => true, 'code' => ResponseError::NO_ERROR];
//    }
}
