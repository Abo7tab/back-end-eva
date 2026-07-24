<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'uuid',
        'product_id',
        'image_url',
        'storage_public_id',
        'alt_text',
        'sort_order',
    ];

    protected $hidden = [
        'storage_public_id',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
