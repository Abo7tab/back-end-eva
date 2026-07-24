<?php

declare(strict_types=1);

namespace App\Domains\OrderLogging\Models;

use App\Domains\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_log_id',
        'product_id',
        'product_name_snapshot',
        'product_slug_snapshot',
        'product_image_snapshot',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function orderLog(): BelongsTo
    {
        return $this->belongsTo(OrderLog::class, 'order_log_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
