<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\DeliveryTranslation
 *
 * @property int $id
 * @property int $delivery_id
 * @property string $locale
 * @property string $title
 * @method static Builder|DeliveryTranslation newModelQuery()
 * @method static Builder|DeliveryTranslation newQuery()
 * @method static Builder|DeliveryTranslation query()
 * @method static Builder|DeliveryTranslation whereDeliveryId($value)
 * @method static Builder|DeliveryTranslation whereId($value)
 * @method static Builder|DeliveryTranslation whereLocale($value)
 * @method static Builder|DeliveryTranslation whereTitle($value)
 * @mixin Eloquent
 */
class DeliveryTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
