<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ShopDeliverymanSetting
 *
 * @property int $id
 * @property string|null $shop_id
 * @property string|null $type
 * @property string|null $value
 * @property string|null $period
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|self filter($filter)
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereId($value)
 * @mixin Eloquent
 */
class ShopDeliverymanSetting extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function scopeFilter($query, $filter)
    {}
}
