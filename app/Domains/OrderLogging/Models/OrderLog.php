<?php

declare(strict_types=1);

namespace App\Domains\OrderLogging\Models;

use App\Domains\Operations\Models\WhatsAppNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OrderLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'reference_code',
        'customer_name',
        'customer_phone',
        'total_items',
        'subtotal',
        'total_price',
        'whatsapp_number_id',
        'assigned_phone_number',
        'checkout_message',
        'status',
    ];

    protected $casts = [
        'total_items' => 'integer',
        'subtotal' => 'decimal:2',
        'total_price' => 'decimal:2',
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

    public function whatsappNumber(): BelongsTo
    {
        return $this->belongsTo(WhatsAppNumber::class, 'whatsapp_number_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderLogItem::class, 'order_log_id');
    }
}
