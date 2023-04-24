<?php

namespace App\Models;

use Database\Factories\PaymentPayloadFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PaymentPayload
 *
 * @property int|null $payment_id
 * @property Payment|null $payment
 * @property array|null $payload
 * @property string|null $deleted_at
 * @method static PaymentPayloadFactory factory(...$parameters)
 * @method static Builder|PaymentPayload newModelQuery()
 * @method static Builder|PaymentPayload newQuery()
 * @method static Builder|PaymentPayload query()
 * @method static Builder|PaymentPayload whereDeletedAt($value)
 * @method static Builder|PaymentPayload whereId($value)
 * @mixin Eloquent
 */
class PaymentPayload extends Model
{
    use HasFactory, SoftDeletes;

    public $primaryKey      = 'payment_id';
    public $incrementing    = false;
    public $timestamps      = false;
    protected $guarded      = [];
    protected $casts        = [
        'payload' => 'array',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
