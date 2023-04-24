<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Ticket
 *
 * @property int $id
 * @property string $uuid
 * @property int $created_by
 * @property int|null $user_id
 * @property int|null $order_id
 * @property int $parent_id
 * @property string $type
 * @property string $subject
 * @property string $content
 * @property string $status
 * @property int $read
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Ticket[] $children
 * @property-read int|null $children_count
 * @method static TicketFactory factory(...$parameters)
 * @method static Builder|Ticket filter($array)
 * @method static Builder|Ticket newModelQuery()
 * @method static Builder|Ticket newQuery()
 * @method static Builder|Ticket query()
 * @method static Builder|Ticket whereContent($value)
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereCreatedBy($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket whereOrderId($value)
 * @method static Builder|Ticket whereParentId($value)
 * @method static Builder|Ticket whereRead($value)
 * @method static Builder|Ticket whereStatus($value)
 * @method static Builder|Ticket whereSubject($value)
 * @method static Builder|Ticket whereType($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 * @method static Builder|Ticket whereUserId($value)
 * @method static Builder|Ticket whereUuid($value)
 * @mixin Eloquent
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const STATUS = [
        'open'      => 'open',
        'answered'  => 'answered',
        'progress'  => 'progress',
        'closed'    => 'closed',
        'rejected'  => 'rejected',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeFilter($query, $array) {
        $query
            ->when(isset($array['status']),             fn($q)              => $q->where('status', $array['status']))
            ->when(isset($filter['deleted_at']),        fn($q)              => $q->onlyTrashed())
            ->when(data_get($array, 'created_by'),  fn($q, $createdBy)  => $q->where('created_by', $createdBy))
            ->when(data_get($array, 'user_id'),     fn($q, $userId)     => $q->where('user_id', $userId))
            ->when(data_get($array, 'type'),        fn($q, $type)       => $q->where('type', $type));
    }
}
