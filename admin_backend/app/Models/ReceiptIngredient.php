<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReceiptIngredient
 *
 * @property int $id
 * @property int $receipt_id
 * @property Receipt|null $receipt
 * @property string $locale
 * @property string $title
 * @property Carbon|null $deleted_at
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ReceiptIngredient extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }
}
