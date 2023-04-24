<?php

namespace App\Repositories\OrderRepository;

use App\Models\Language;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Repositories\CoreRepository;
use App\Traits\SetCurrency;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

class AdminOrderRepository extends CoreRepository
{
    use SetCurrency;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Order::class;
    }

    /**
     * @param array $filter
     * @param string $paginate
     * @return Paginator
     */
    public function ordersPaginate(array $filter = [], string $paginate = 'simplePaginate'): Paginator
    {
        /** @var Order $order */
        $order = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $order
            ->withCount('orderDetails')
            ->with([
                'user:id,lastname,firstname,img',
                'shop:id,logo_img',
                'shop.translation' => fn($q) => $q->select('id', 'shop_id', 'locale', 'title')
                    ->where('locale', $this->language)->orWhere('locale', $locale),
                'deliveryMan:id,lastname,firstname',
                'transaction:id,payable_type,payable_id,payment_sys_id',
                'transaction.paymentSystem:id,tag',
            ])
            ->updatedDate($this->updatedDate)
            ->filter($filter)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->$paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param array $filter
     * @param string $paginate
     * @return Paginator
     */
    public function userOrdersPaginate(array $filter = [], string $paginate = 'simplePaginate'): Paginator
    {
        /** @var Order $order */
        $order = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $order
            ->withCount('orderDetails')
            ->with([
                'user:id,lastname,firstname',
                'shop:id,location,tax,price,price_per_km,background_img,logo_img',
                'shop.translation' => fn($q) => $q->select('id', 'shop_id', 'locale', 'title')
                    ->where('locale', $this->language)->orWhere('locale', $locale),
                'deliveryMan:id,lastname,firstname',
                'transaction:id,payable_type,payable_id,payment_sys_id',
                'transaction.paymentSystem:id,tag',
                'currency'           => fn($q) => $q->select('id', 'title', 'symbol'),
                'orderDetails'       => fn($q) => $q->whereNull('parent_id'),
                'orderDetails.stock' => fn($q) => $q->select('id', 'countable_id', 'countable_type'),
                'orderDetails.stock.countable.translation' => fn($q) => $q->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'orderDetails.children.stock' => fn($q) => $q->select('id', 'countable_id', 'countable_type'),
                'orderDetails.children.stock.countable.translation' => fn($q) => $q->where('locale', $this->language)
                    ->orWhere('locale', $locale),
            ])
            ->updatedDate($this->updatedDate)
            ->filter($filter)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->$paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param string $id
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function userOrder(string $id, array $filter = []): LengthAwarePaginator
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        /** @var User $user */
        $user = User::select(['id', 'uuid'])->where('uuid', $id)->first();

        $orders = Order::select(['user_id', 'id'])->where('user_id', $user?->id)->pluck('id')->toArray();

        return OrderDetail::with([
            'stock'    => fn($q) => $q->select('id', 'countable_id', 'countable_type'),
            'stock.stockExtras.group.translation' => fn($q) => $q->select('id', 'extra_group_id', 'locale', 'title')
                ->where('locale', $this->language)->orWhere('locale', $locale),
            'stock.countable' => fn($q) => $q->select('id', 'uuid', 'img', 'status', 'active'),
            'stock.countable.translation' => fn($q) => $q->select('id', 'product_id', 'locale', 'title')
                ->where('locale', $this->language)->orWhere('locale', $locale),
        ])
            ->whereHas('stock')
            ->whereIn('order_id', $orders)
            ->whereNull('parent_id')
            ->groupBy(['stock_id'])
            ->select([
                'stock_id',
                DB::raw('count(stock_id) as count'),
                DB::raw('sum(total_price) as total_price')
            ])
            ->orderBy('count', 'desc')
            ->paginate(data_get($filter, 'perPage', 10));
    }

}
