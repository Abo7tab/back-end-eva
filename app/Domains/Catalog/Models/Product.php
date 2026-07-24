<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasSlug, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'brand_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'weight',
        'weight_unit',
        'price',
        'compare_at_price',
        'stock_quantity',
        'cover_image_url',
        'storage_public_id',
        'is_trending',
        'is_featured',
        'views_count',
        'sort_order',
        'active_status',
        'seo_title',
        'meta_description',
    ];

    protected $attributes = [
        'active_status'  => true,
        'is_trending'    => false,
        'is_featured'    => false,
        'sort_order'     => 0,
        'views_count'    => 0,
        'stock_quantity' => 0,
    ];

    protected $hidden = [
        'storage_public_id',
    ];

    protected $casts = [
        'brand_id'         => 'integer',
        'category_id'      => 'integer',
        'price'            => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'weight'           => 'decimal:2',
        'stock_quantity'    => 'integer',
        'is_trending'      => 'boolean',
        'is_featured'      => 'boolean',
        'views_count'      => 'integer',
        'sort_order'       => 'integer',
        'active_status'    => 'boolean',
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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
