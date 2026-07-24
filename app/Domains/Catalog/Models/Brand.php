<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasSlug, SoftDeletes;

    protected $table = 'brands';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'logo_url',
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }
}
