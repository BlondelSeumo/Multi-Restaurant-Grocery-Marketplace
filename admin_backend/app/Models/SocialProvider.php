<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\SocialProvider
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string|null $avatar
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SocialProvider newModelQuery()
 * @method static Builder|SocialProvider newQuery()
 * @method static Builder|SocialProvider query()
 * @method static Builder|SocialProvider whereAvatar($value)
 * @method static Builder|SocialProvider whereCreatedAt($value)
 * @method static Builder|SocialProvider whereId($value)
 * @method static Builder|SocialProvider whereProvider($value)
 * @method static Builder|SocialProvider whereProviderId($value)
 * @method static Builder|SocialProvider whereUpdatedAt($value)
 * @method static Builder|SocialProvider whereUserId($value)
 * @mixin Eloquent
 */
class SocialProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];
}
