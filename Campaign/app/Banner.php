<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

/**
 * App\Banner
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $target_url
 * @property string $text
 * @property string $dimensions
 * @property string $text_align
 * @property string $text_color
 * @property string $font_size
 * @property string $background_color
 * @property string $position
 * @property string $transition
 * @property int $display_delay
 * @property bool $closeable
 * @property int $close_timeout
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereBackgroundColor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereCloseTimeout($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereCloseable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereDimensions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereDisplayDelay($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereFontSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereTargetUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereTextAlign($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereTextColor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereTransition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner whereUuid($value)
 * @mixin \Eloquent
 */
class Banner extends Model
{
    use Notifiable;

    protected $fillable = [
        'name',
        'dimensions',
        'text',
        'text_align',
        'font_size',
        'target_url',
        'text_color',
        'background_color',
        'position',
        'transition',
        'closeable',
        'display_delay',
        'close_timeout',
    ];

    protected $casts = [
        'closeable' => 'boolean',
    ];

    protected $attributes = [
        'closeable' => false
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function(Banner $banner) {
            $banner->uuid = Uuid::uuid4()->toString();
        });
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}