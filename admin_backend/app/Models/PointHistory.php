<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\PointHistory
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property float $price
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PointHistory newModelQuery()
 * @method static Builder|PointHistory newQuery()
 * @method static Builder|PointHistory query()
 * @method static Builder|PointHistory whereCreatedAt($value)
 * @method static Builder|PointHistory whereId($value)
 * @method static Builder|PointHistory whereNote($value)
 * @method static Builder|PointHistory whereOrderId($value)
 * @method static Builder|PointHistory wherePrice($value)
 * @method static Builder|PointHistory whereUpdatedAt($value)
 * @method static Builder|PointHistory whereUserId($value)
 * @mixin Eloquent
 */
class PointHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

}
