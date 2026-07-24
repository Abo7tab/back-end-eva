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

class Category extends Model
{
    use HasSlug, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'uuid',
        'parent_id',
        'name',
        'slug',
        'description',
        'cover_image_url',
        'storage_public_id',
        'seo_title',
        'meta_description',
        'active_status',
        'sort_order',
    ];

    protected $attributes = [
        'active_status' => true,
        'sort_order'    => 0,
    ];

    protected $hidden = [
        'storage_public_id',
    ];

    protected $casts = [
        'active_status' => 'boolean',
        'sort_order'    => 'integer',
        'parent_id'     => 'integer',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
