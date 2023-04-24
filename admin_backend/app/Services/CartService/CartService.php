<?php

namespace App\Services\CartService;

use App\Helpers\ResponseError;
use App\Http\Resources\Cart\CartResource;
use App\Models\Bonus;
use App\Models\Receipt;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\User;
use App\Models\UserCart;
use App\Services\CoreService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Str;
use Throwable;

class CartService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Cart::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        /** @var User $user */
        /** @var Stock $stock */

        $user             = auth('sanctum')->user();
        $data['user_id']  = $user->id;
        $data['owner_id'] = $user->id;
        $data['name']     = $user->name_or_email;

        $stock = Stock::with([
            'countable:id,status,shop_id,min_qty,max_qty,tax,img',
            'countable.discounts' => fn($q) => $q
                ->where('start', '<=', today())
                ->where('end', '>=', today())
                ->where('active', 1)
        ])->find(data_get($data, 'stock_id', 0));

        if (!$stock?->id || $stock->countable?->status !== Product::PUBLISHED) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        if ($stock->countable?->shop_id !== data_get($data, 'shop_id')) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_400,
                'message' => __('errors.' . ResponseError::OTHER_SHOP, locale: $this->language)
            ];
        }

        $quantity = $this->actualQuantity($stock, data_get($data, 'quantity', 0)) ?? 0;

        data_set($data, 'quantity', $quantity);
        data_set($data, 'price', ($stock->price + $stock->tax_price) * $quantity);
        data_set($data, 'discount', $stock->actual_discount * $quantity);

        /** @var Cart $cart */
        $cart = $this->model()
            ->where('owner_id', $user->id)
            ->first();

        if ($cart) {
            return $this->createToExistCart($data, $cart, $stock, $user);
        }

        return $this->createCart($data);
    }

    #region create when exist cart

    /**
     * @param array $data
     * @param Cart $cart
     * @param Stock $stock
     * @param User $user
     * @return array
     */
    private function createToExistCart(array $data, Cart $cart, Stock $stock, User $user): array
    {
        try {

            if ($cart->shop_id !== data_get($stock->countable, 'shop_id')) {
                return [
                    'status'  => false,
                    'code'    => ResponseError::ERROR_400,
                    'message' => __('errors.' . ResponseError::OTHER_SHOP, locale: $this->language)
                ];
            }

            $cartId = DB::transaction(function () use ($data, $cart, $stock, $user) {

                /** @var UserCart $userCart */
                $userCart = $cart->userCarts()->firstOrCreate([
                    'cart_id' => data_get($cart, 'id'),
                    'user_id' => $user->id,
                ], $data);

                /** @var CartDetail $cartDetail */
                $cartDetail = $userCart->cartDetails()->updateOrCreate([
                    'stock_id'      => data_get($data, 'stock_id'),
                    'user_cart_id'  => $userCart->id,
                ], [
                    'quantity'      => data_get($data, 'quantity', 0),
                    'price'         => data_get($data, 'price', 0),
                    'discount'      => data_get($data, 'discount', 0),
                ]);

                $this->bonus($cartDetail);

                return $cart->id;
            });

            return $this->successReturn($cartId);
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }
    #endregion

    #region create new cart
    /**
     * @param array $data
     * @return array
     */
    private function createCart(array $data): array
    {
        try {
            $cartId = DB::transaction(function () use ($data) {

                /** @var Cart $cart */
                $cart = $this->model()->create($data);

                /** @var UserCart $userCarts */
                $userCarts = $cart->userCarts()->create($data);

                /** @var CartDetail $cartDetail */
                $cartDetail = $userCarts->cartDetails()->create($data);

                $this->bonus($cartDetail);

                return $cart->id;
            });

            return $this->successReturn($cartId);
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }
    #endregion

    /**
     * @param array $data
     * @return array
     */
    public function groupCreate(array $data): array
    {
        /** @var Stock $stock */
        $stock = Stock::with([
            'countable:id,status,shop_id,min_qty,max_qty,tax,img',
            'countable.discounts' => fn($q) => $q
                ->where('start', '<=', today())
                ->where('end', '>=', today())
                ->where('active', 1)
        ])->find(data_get($data, 'stock_id', 0));

        if (!data_get($stock, 'id') || $stock->countable?->status !== Product::PUBLISHED) {
            return [
                'status' => false,
                'code'   => ResponseError::ERROR_404,
            ];
        }

        $quantity = data_get($data, 'quantity', 0);

        data_set($data, 'price', ($stock->price + $stock->tax_price) * $quantity);
        data_set($data, 'discount', $stock->actual_discount * $quantity);

        $checkQuantity = $this->checkQuantity($stock, $quantity);

        if (!data_get($checkQuantity, 'status')) {
            return $this->errorCheckQuantity($checkQuantity);
        }

        /**
         * @var Cart $model
         * @var UserCart $userCart
         */
        $model    = $this->model()->find(data_get($data, 'cart_id', 0));
        $userCart = $model->userCarts->where('uuid', data_get($data, 'user_cart_uuid'))->first();

        if (!$userCart) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_404,
                'message' => ResponseError::USER_CARTS_IS_EMPTY
            ];
        }

        try {
            if ($model->shop_id !== data_get($stock->countable, 'shop_id')) {
                return [
                    'status'  => true,
                    'code'    => ResponseError::ERROR_400,
                    'message' => __('errors.' . ResponseError::OTHER_SHOP, locale: $this->language)
                ];
            }

            $cartId = DB::transaction(function () use ($data, $model, $userCart) {

                $cartDetail = $userCart->cartDetails()->updateOrCreate([
                    'stock_id'      => data_get($data, 'stock_id'),
                    'user_cart_id'  => $userCart->id,
                    'parent_id'     => data_get($data, 'parent_id'),
                ], [
                    'quantity'      => data_get($data, 'quantity', 0),
                    'price'         => data_get($data, 'price', 0),
                    'discount'      => data_get($data, 'discount', 0),
                ]);

                $this->bonus($cartDetail);

                return $model->id;
            });

            return $this->successReturn($cartId);
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }

    }

    /**
     * @param array $data
     * @return array
     */
    public function groupInsertProducts(array $data): array
    {
        /**
         * @var Cart $model
         * @var UserCart $userCart
         */
        $userCart = UserCart::where('uuid', data_get($data, 'user_cart_uuid'))->first();

        $model = $userCart?->cart?->loadMissing([
            'shop',
            'userCarts.cartDetails' => fn($q) => $q->whereNull('parent_id'),
            'userCarts.cartDetails.stock.countable.discounts' => fn($q) => $q->where('start', '<=', today())
                ->where('end', '>=', today())
                ->where('active', 1),
            'userCarts.cartDetails.stock.countable:id,status,shop_id,min_qty,max_qty,tax,img',
            'userCarts.cartDetails.children.stock.countable:id,status,shop_id,min_qty,max_qty,tax',
        ]);

        if (empty($model)) {
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        try {
            $cartId = $this->collectProducts($data, $model, $userCart);

            return $this->successReturn($cartId);
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }

    }

    /**
     * @param array $data
     * @return array
     */
    public function openCart(array $data): array
    {
        /** @var Cart $cart */
        $cart = $this->model()
            ->with('userCarts')
            ->where('shop_id', data_get($data, 'shop_id', 0))
            ->find(data_get($data, 'cart_id', 0));

        if (empty($cart)) {
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $data['user_id'] = auth('sanctum')->id();

        $model = $cart->userCart()->create($data);

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $model];
    }

    /**
     * @param array $data
     * @return array
     */
    public function openCartOwner(array $data): array
    {
        /** @var User $user */
        /** @var Cart $cart */
        $user  = auth('sanctum')->user();
        $model = $this->model();
        $cart  = $model->where('owner_id', $user->id)->first();

        if (data_get($cart, 'userCart')) {
            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::OTHER_SHOP, locale: $this->language)
            ];
        }

        try {
            $cartId = DB::transaction(function () use ($data, $user) {

                $cart = Cart::firstOrCreate([
                    'shop_id' => data_get($data, 'shop_id', 0),
                    'owner_id' => $user->id,
                ], $data);

                $cart->userCarts()
                     ->firstOrCreate([
                        'cart_id' => data_get($cart, 'id'),
                        'user_id' => $user->id,
                     ], $data);

                return data_get($cart, 'id');
            });

            return $this->successReturn($cartId);
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }

    /**
     * @param array|null $ids
     * @return array
     */
    public function delete(?array $ids = []): array
    {
        foreach (Cart::whereIn('id', is_array($ids) ? $ids : [])->get() as $cart) {
            $cart->delete();
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR];
    }

    /**
     * @param array|null $ids
     * @return array
     */
    public function cartProductDelete(?array $ids = []): array
    {
        /** @var CartDetail $cartDetail */
        $cartDetails = CartDetail::whereIn('id', is_array($ids) ? $ids : [])->get();

        $cart = $cartDetails->first()?->userCart?->cart;

        foreach ($cartDetails as $cartDetail) {

            $cartDetail->userCart?->cart?->decrement(
                'total_price',
                $cartDetail->price - $cartDetail->discount
            );

            $cartDetail->delete();

            CartDetail::where([
                ['bonus', true],
                ['user_cart_id', $cartDetail->user_cart_id],
                ['stock_id', $cartDetail->stock_id]
            ])->delete();

        }

        return $this->successReturn($cart->id);
    }

    /**
     * @param array|null $ids
     * @param int|null $cartId
     * @return array
     */
    public function userCartDelete(?array $ids = [], ?int $cartId = null): array
    {
        /** @var Cart $cart */
        $cart = $this->model()->with([
            'shop:id',
            'shop.bonus',
            'userCarts' => fn($q) => $q->whereIn('uuid', is_array($ids) ? $ids : []),
            'userCarts.cartDetails',
        ])->find($cartId);

        if (!$cart?->userCarts) {
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $cart->userCarts()->whereIn('uuid', is_array($ids) ? $ids : [])->delete();

        $this->calculateTotalPrice($cart->fresh(['userCarts.cartDetails']));

        return $this->successReturn($cartId);
    }

    /**
     * @param string $uuid
     * @param int $cartId
     * @return array
     */
    public function statusChange(string $uuid, int $cartId): array
    {
        /** @var Cart $cart */
        $cart = Cart::with([
            'userCart' => fn($query) => $query->where('uuid', $uuid)
        ])
            ->whereHas('userCart', fn($query) => $query->where('uuid', $uuid))
            ->find($cartId);

        if (empty($cart)) {
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $cart->userCart->update(['status' => !$cart->userCart->status]);

        return [
            'status' => true,
            'code' => ResponseError::NO_ERROR,
            'data' => CartResource::make($cart),
        ];
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function setGroup(?int $id): array
    {
        /** @var Cart $cart */
        $cart = Cart::with([
            'userCart' => fn($query) => $query->where('user_id', auth('sanctum')->id())
        ])
            ->whereHas('userCart', fn($query) => $query->where('user_id', auth('sanctum')->id()))
            ->find($id);

        if (empty($cart)) {
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ];
        }

        $cart->update(['group' => !$cart->group]);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => $cart
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function insertProducts(array $data): array
    {
        /** @var User $user */
        $user             = auth('sanctum')->user();
        $userId           = $user->id;
        $data['owner_id'] = $userId;
        $data['user_id']  = $userId;
        $data['rate']     = Currency::find($data['currency_id'])->rate;

        /** @var Cart $exist */
        $exist = $this->model()->select(['id', 'shop_id', 'owner_id'])->where('owner_id', $userId)->first();

        if ($exist && $exist->shop_id !== data_get($data, 'shop_id')) {
            return [
                'status'  => true,
                'code'    => ResponseError::ERROR_400,
                'message' => __('errors.' . ResponseError::OTHER_SHOP, locale: $this->language)
            ];
        }

        $cart = $this->model()->with([
            'shop',
            'userCarts.cartDetails' => fn($q) => $q->whereNull('parent_id'),
            'userCarts.cartDetails.stock.countable.discounts' => fn($q) => $q->where('start', '<=', today())
                ->where('end', '>=', today())
                ->where('active', 1),
            'userCarts.cartDetails.stock.countable:id,status,shop_id,min_qty,max_qty,tax,img',
            'userCarts.cartDetails.children.stock.countable:id,status,shop_id,min_qty,max_qty,tax',
        ])
            ->firstOrCreate([
                'owner_id'  => $userId,
                'shop_id'   => data_get($data, 'shop_id', 0)
            ], $data);

        return $this->cartDetailsUpdate($data, $cart);
    }

    /**
     * @param array $data
     * @param Cart $cart
     * @return array
     */
    private function cartDetailsUpdate(array $data, Cart $cart): array
    {
        /** @var UserCart $userCart */

        $userCart = $cart->userCarts()->firstOrCreate([
            'user_id' => auth('sanctum')->id(),
            'cart_id' => $cart->id,
        ], [
            'uuid'    => Str::uuid()
        ]);

        $cartId = $this->collectProducts($data, $cart, $userCart);

        return $this->successReturn($cartId);
    }

    /**
     * @param array $data
     * @param Cart $cart
     * @param UserCart $userCart
     * @return int
     */
    private function collectProducts(array $data, Cart $cart, UserCart $userCart): int
    {
        try {
            return DB::transaction(function () use ($data, $cart, $userCart) {

                $products = collect(data_get($data, 'products', []));

                $stocks = Stock::with([
                    'countable' => fn($q) => $q->select(['id', 'status', 'shop_id', 'min_qty', 'max_qty', 'tax'])
                        ->where('status', Product::PUBLISHED)
                        ->where('shop_id', data_get($data, 'shop_id')),
                    'countable.discounts' => fn($q) => $q
                        ->where('start', '<=', today())
                        ->where('end', '>=', today())
                        ->where('active', 1)
                ])
                    ->whereHas('countable', fn($q) => $q->where('status', Product::PUBLISHED)
                            ->where('shop_id', data_get($data, 'shop_id'))
                    )
                    ->find(data_get($data, 'products.*.stock_id', []));

                foreach ($stocks as $stock) {

                    /** @var Stock $stock */
                    $product = $products->where('stock_id', $stock->id)->first();

                    if ((int)data_get($product, 'quantity', 0) === 0) {

                        $parent = CartDetail::where([
                            ['stock_id', data_get($product, 'parent_id')],
                            ['user_cart_id', $userCart->id],
                        ])->first();

                        CartDetail::where([
                            ['stock_id', $stock->id],
                            ['user_cart_id', $userCart->id],
                            ['parent_id', $parent?->id],
                        ])->delete();

                        continue;
                    }

                    $quantity = $this->actualQuantity($stock, data_get($product, 'quantity', 0)) ?? 0;
                    $parentCartDetail = data_get($product, 'parent_id');

                    if (!empty($parentCartDetail)) {
                        $parentCartDetail = CartDetail::firstOrCreate([
                            'stock_id'      => data_get($product, 'parent_id'),
                            'user_cart_id'  => $userCart->id,
                        ])?->id;
                    }

                    if ($stock->addon && empty($parentCartDetail)) {
                        continue;
                    }

                    $cartDetail = CartDetail::updateOrCreate([
                        'stock_id'      => $stock->id,
                        'user_cart_id'  => $userCart->id,
                        'parent_id'     => $parentCartDetail,
                    ], [
                        'quantity'      => $quantity,
                        'price'         => ($stock->price + $stock->tax_price) * $quantity,
                        'discount'      => $stock->actual_discount * $quantity,
                    ]);

                    $this->bonus($cartDetail);
                }

                return data_get($cart, 'id');
            });
        } catch (Throwable $e) {
            $this->error($e);
            return 0;
        }
    }

    private function bonus(CartDetail $cartDetail)
    {
        $stock = $cartDetail->stock;

        $bonusStock = Bonus::where([
            ['bonusable_id', data_get($stock, 'id', 0)],
            ['bonusable_type', Stock::class],
            ['expired_at', '>', now()],
            ['status', 1],
        ])
            ->first();

        $bonusShop = Bonus::where([
            ['bonusable_id', data_get($cartDetail->userCart, 'cart.shop_id')],
            ['bonusable_type', Shop::class],
            ['expired_at', '>', now()],
        ])
            ->first();

        if (data_get($bonusStock, 'id') && data_get($bonusStock, 'stock.id')) {
            $this->checkBonus($cartDetail, $bonusStock);
        }

        if (data_get($bonusShop, 'id') && data_get($bonusShop, 'stock.id')) {
            $this->checkBonus($cartDetail, $bonusShop);
        }

    }

    /**
     * @param CartDetail $cartDetail
     * @param Bonus $bonus
     * @return void
     */
    private function checkBonus(CartDetail $cartDetail, Bonus $bonus): void
    {
        $checkByCount = $bonus->type === Bonus::TYPE_COUNT && floor($cartDetail->quantity / $bonus->value) > 0;
        $checkBySum   = $bonus->type === Bonus::TYPE_SUM   && $cartDetail->price >= $bonus->value;

        if ($checkByCount || $checkBySum) {
            try {
                $cartDetail->userCart->cartDetails()->updateOrCreate([
                    'stock_id'      => $bonus->bonus_stock_id,
                    'user_cart_id'  => $cartDetail->user_cart_id,
                    'price'         => 0,
                    'bonus'         => true,
                    'discount'      => 0,
                ], [
                    'quantity' => $bonus->type === Bonus::TYPE_COUNT ?
                        $bonus->bonus_quantity * (int)floor($cartDetail->quantity / $bonus->value) :
                        $bonus->bonus_quantity,
                ]);
            } catch (Throwable $e) {
                $this->error($e);
            }

        }

    }

    /**
     * @param Stock $stock
     * @param int $quantity
     * @return array
     */
    protected function checkQuantity(Stock $stock, int $quantity): array
    {
        if ($stock->quantity < $quantity) {
            return [
                'status'   => false,
                'code'     => ResponseError::NO_ERROR,
                'quantity' => $stock->quantity,
            ];
        }

        $countable  = $stock->countable;
        $minQty     = $countable?->min_qty ?? 0;
        $maxQty     = $countable?->max_qty ?? 0;

        if ($quantity < $minQty || $quantity > $maxQty) {
            return [
                'status'   => false,
                'code'     => ResponseError::NO_ERROR,
                'quantity' => "$minQty-$maxQty",
            ];
        }

        return ['status' => true, 'code' => ResponseError::NO_ERROR,];
    }

    /**
     * @param array $checkQuantity
     * @return array
     */
    private function errorCheckQuantity(array $checkQuantity): array
    {
        $data = [ 'quantity' => data_get($checkQuantity, 'quantity', 0) ];

        return [
            'status'  => false,
            'code'    => ResponseError::ERROR_111,
            'message' => __('errors.' . ResponseError::ERROR_111, $data, $this->language),
            'data'    => $data
        ];
    }

    /**
     * @param Stock $stock
     * @param $quantity
     * @return int|mixed|null
     */
    private function actualQuantity(Stock $stock, $quantity): mixed
    {
        $countable = $stock->countable;

        if ($quantity < ($countable?->min_qty ?? 0)) {

            $quantity = $countable->min_qty;

        } else if($quantity > ($countable?->max_qty ?? 0)) {

            $quantity = $countable->max_qty;

        }

        return $quantity > $stock->quantity ? max($stock->quantity, 0) : $quantity;
    }

    /**
     * @param int $cartId
     * @return array
     */
    private function successReturn(int $cartId): array
    {
        /** @var Cart $cart */
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        if (empty($this->language)) {
            $this->language = $locale;
        }

        $cart = $this->model()->with([
            'userCarts:id,cart_id',
            'userCarts.cartDetails:id,user_cart_id,stock_id,price,discount,quantity',
            'userCarts.cartDetails.stock:id',
            'shop',
            'shop.bonus',
        ])
            ->select('id', 'total_price', 'shop_id')
            ->find($cartId);

        $this->calculateTotalPrice($cart);

        $cart = Cart::with([
            'userCarts.cartDetails' => fn($q) => $q->whereNull('parent_id'),
            'userCarts.cartDetails.stock.countable.translation' => fn($q) => $q
                ->where('locale', $this->language)->orWhere('locale', $locale),
            'userCarts.cartDetails.stock.stockExtras.group.translation' => fn($q) => $q
                ->where('locale', $this->language)->orWhere('locale', $locale),
            'userCarts.cartDetails.children.stock.countable.translation' => fn($q) => $q
                ->where('locale', $this->language)->orWhere('locale', $locale),
            'userCarts.cartDetails.children.stock.stockExtras.group.translation' => fn($q) => $q
                ->where('locale', $this->language)->orWhere('locale', $locale),
        ])->find($cart->id);

        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => CartResource::make($cart),
        ];
    }

    /**
     * @param Cart $cart
     * @return bool
     */
    public function calculateTotalPrice(Cart $cart): bool
    {
        $price = 0;

        $inReceipts = [];

        foreach ($cart->userCarts as $userCart) {

            $cartDetails = $userCart->cartDetails;

            if (empty($cartDetails)) {
                $userCart->delete();
                continue;
            }

            foreach ($userCart->cartDetails as $cartDetail) {

                if (empty($cartDetail?->stock) || $cartDetail?->quantity === 0) {
                    $cartDetail->delete();
                    continue;
                }

                if (!$cartDetail->bonus) {
                    $inReceipts[$cartDetail->stock_id] = $cartDetail->quantity;
                }

                $price += ($cartDetail->price - $cartDetail->discount);
            }

        }

        $bonus = $cart->shop?->bonus
            ?->where('type',Bonus::TYPE_SUM)
            ?->where('expired_at', '>', now())
            ?->where('status', 1)
            ?->first();

        if ($bonus?->value > $price && $bonus?->type === Bonus::TYPE_SUM) {

            $ids = $cart->userCarts?->pluck('id')?->toArray();

            DB::table('cart_details')
                ->whereIn('user_cart_id', is_array($ids) ? $ids : [])
                ->where('stock_id', $bonus->bonus_stock_id)
                ->delete();

            unset($inReceipts[$bonus->bonus_stock_id]);
        }

        // recalculate shop bonus
        $receiptDiscount = $this->recalculateReceipt($cart, $inReceipts);

        $cart = $cart->fresh('userCarts');

        $totalPrice = max($price - $receiptDiscount, 0);

        if ($cart->userCarts?->count() === 0) {
            $totalPrice = 0;
        }

        return $cart->update(['total_price' => $totalPrice]);
    }

    /**
     * @param Cart $cart
     * @param array $inReceipts
     * @return int|float
     */
    public function recalculateReceipt(Cart $cart, array $inReceipts): int|float
    {
        /** @var Receipt|null $receipt */
        $receipts = Receipt::with('stocks')
            ->whereHas('stocks')
            ->where('shop_id', $cart->shop_id)
            ->get();

        return $this->receipts($receipts, $inReceipts);
    }

    /**
     * @param Collection|Receipt[] $receipts
     * @param array $inReceipts
     * @return float|int|null
     */
    public function receipts(array|Collection $receipts, array $inReceipts): float|int|null
    {
        $receiptDiscount    = 0;
        $totalReceiptCount  = 0;

        foreach ($receipts as $receipt) {

            $stocks = $receipt?->stocks?->pluck('pivot.min_quantity', 'id')->toArray();

            $receiptStockIds = array_intersect(array_keys($stocks), array_keys($inReceipts));

            if($receiptStockIds !== array_keys($stocks)) {
                continue;
            }

            $receiptCount = [];

            foreach ($inReceipts as $key => $inReceipt) {

                if ($inReceipt === data_get($stocks, $key)) {
                    $receiptCount[] = 1;
                    break;
                } else if ($inReceipt > data_get($stocks, $key)) {
                    try {
                        $divideQty = ($inReceipt / data_get($stocks, $key));

                        $receiptCount[] = $divideQty;
                    } catch (Throwable) {}
                }

            }

            $receiptCount = !empty($receiptCount) ? min($receiptCount) : 1;

            $originPrice = $receipt->stocks->reduce(fn($c, $i) => $c + ($i?->total_price * ($i?->pivot?->min_quantity ?? 1)));

            $discountPrice = $receipt->discount_type === 0 ? $receipt->discount_price : $originPrice / 100 * $receipt->discount_price;

            $receiptDiscount    += ($discountPrice * $receiptCount);
            $totalReceiptCount  += $receiptCount;

        }

        request()->offsetSet('receipt_discount', $receiptDiscount);
        request()->offsetSet('receipt_count', $totalReceiptCount);

        return $receiptDiscount;
    }
}
