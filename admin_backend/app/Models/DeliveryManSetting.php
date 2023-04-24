<?php

namespace App\Models;

use App\Traits\Loadable;
use Database\Factories\DeliveryManSettingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\DeliveryManSetting
 *
 * @property int $id
 * @property int $user_id
 * @property string $type_of_technique
 * @property string $brand
 * @property string $model
 * @property string $number
 * @property string $color
 * @property boolean $online
 * @property array $location
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $deliveryMan
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @method static DeliveryManSettingFactory factory(...$parameters)
 * @method static Builder|Blog newModelQuery()
 * @method static Builder|Blog newQuery()
 * @method static Builder|Blog query()
 * @method static Builder|Blog whereUserId($value)
 * @method static Builder|Blog whereTypeOfTechnique($value)
 * @method static Builder|Blog whereBrand($value)
 * @method static Builder|Blog whereModel($value)
 * @method static Builder|Blog whereNumber($value)
 * @method static Builder|Blog whereColor($value)
 * @method static Builder|Blog whereOnline($value)
 * @mixin Eloquent
 */
class DeliveryManSetting extends Model
{
    use HasFactory, Loadable, SoftDeletes;

    protected $guarded  = ['id'];

    protected $table    = 'deliveryman_settings';

    protected $casts    = ['location' => 'array'];

    const BENZINE       = 'benzine';
    const ELECTRIC      = 'electric';
    const DIESEL        = 'diesel';
    const GAS           = 'gas';
    const MOTORBIKE     = 'motorbike';
    const BIKE          = 'bike';
    const FOOT          = 'foot';

    const TYPE_OF_TECHNIQUES = [
        self::BENZINE       => self::BENZINE,
        self::DIESEL        => self::DIESEL,
        self::ELECTRIC      => self::ELECTRIC,
        self::GAS           => self::GAS,
        self::MOTORBIKE     => self::MOTORBIKE,
        self::BIKE          => self::BIKE,
        self::FOOT          => self::FOOT,
    ];

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
