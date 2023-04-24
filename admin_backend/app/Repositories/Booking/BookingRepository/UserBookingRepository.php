<?php

namespace App\Repositories\Booking\BookingRepository;

use App\Models\Booking\UserBooking;
use App\Models\Language;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserBookingRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return UserBooking::class;
    }

    public function paginate($filter = []): LengthAwarePaginator
    {
        /** @var UserBooking $models */
        $models = $this->model();

        return $models
            ->filter($filter)
            ->with([
                'booking:id,max_time,start_time,end_time',
                'user:id,uuid,firstname,lastname,img,active',
                'table:id,name,shop_section_id,tax,chair_count',
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param UserBooking $model
     * @return UserBooking
     */
    public function show(UserBooking $model): UserBooking
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $model->loadMissing([
            'booking:id,shop_id,max_time,start_time,end_time',
            'booking.shop:id,uuid,logo_img,open,visibility',
            'booking.shop.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),

            'user:id,uuid,firstname,lastname,img,active',

            'table:id,name,shop_section_id,tax,chair_count',
            'table.shopSection.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
        ]);
    }
}
