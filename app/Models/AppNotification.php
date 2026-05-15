<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser(Builder $query, string $type, int $id): Builder
    {
        return $query->where('notifiable_type', $type)->where('notifiable_id', $id);
    }
}
