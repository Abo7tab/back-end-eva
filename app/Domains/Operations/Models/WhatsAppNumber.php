<?php

declare(strict_types=1);

namespace App\Domains\Operations\Models;

use App\Domains\OrderLogging\Models\OrderLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WhatsAppNumber extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_numbers';

    protected $fillable = [
        'uuid',
        'phone_number',
        'display_name',
        'is_active',
        'order_count',
        'last_assigned_at',
    ];

    protected $attributes = [
        'is_active'    => true,
        'order_count'  => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_count' => 'integer',
        'last_assigned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    public function orderLogs(): HasMany
    {
        return $this->hasMany(OrderLog::class, 'whatsapp_number_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
