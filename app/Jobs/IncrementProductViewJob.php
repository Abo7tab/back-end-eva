<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncrementProductViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 60, 300];

    public function __construct(private int $productId)
    {
    }

    public function handle(): void
    {
        $updated = DB::table('products')
            ->where('id', $this->productId)
            ->increment('views_count');

        if (!$updated) {
            Log::warning("IncrementProductViewJob failed: Product with ID {$this->productId} not found.");
            // Do not retry if product is not found
            $this->fail(new \Exception("Product not found."));
        }
    }
}
