<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\Catalog\BrandController;
use App\Http\Controllers\Api\V1\Catalog\CategoryController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
use App\Http\Controllers\Api\V1\Catalog\ProductImageController;
use App\Http\Controllers\Api\V1\Operations\WhatsAppNumberController;
use App\Http\Controllers\Api\V1\Operations\SiteSettingController;
use App\Http\Controllers\Api\V1\OrderLogging\CheckoutController;
use App\Http\Controllers\Api\V1\OrderLogging\OrderLogController;
use App\Http\Controllers\Api\V1\Admin\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\UploadController;

Route::prefix('v1')->group(function () {

    // ─── PUBLIC ROUTES (no auth) ─────────────────
    Route::middleware(['throttle:public'])->group(function () {
        Route::get('/brands', [BrandController::class, 'index']);
        Route::get('/brands/{slug}', [BrandController::class, 'show']);
        
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{slug}', [CategoryController::class, 'show']);
        
        Route::get('/products/trending', [ProductController::class, 'trending']);
        Route::get('/products/featured', [ProductController::class, 'featured']);
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{slug}', [ProductController::class, 'show']);
        
        Route::get('/settings', [SiteSettingController::class, 'index']);
    });

    // ─── CHECKOUT (stricter rate limit) ──────────
    Route::middleware(['throttle:checkout'])->group(function () {
        Route::post('/checkout', [CheckoutController::class, 'store']);
    });

    // ─── ADMIN LOGIN (very strict rate limit) ────
    Route::middleware(['throttle:admin-login'])->group(function () {
        Route::post('/admin/login', [AdminAuthController::class, 'login']);
    });

    // ─── ADMIN AUTHENTICATED ─────────────────────
    Route::middleware(['admin.auth', 'throttle:admin-crud'])
        ->prefix('admin')
        ->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/me', [AdminAuthController::class, 'me']);

            Route::get('/brands', [BrandController::class, 'indexAdmin']);
            Route::post('/brands', [BrandController::class, 'store']);
            Route::get('/brands/{uuid}', [BrandController::class, 'showAdmin']);
            Route::put('/brands/{uuid}', [BrandController::class, 'update']);
            Route::delete('/brands/{uuid}', [BrandController::class, 'destroy']);

            Route::get('/categories', [CategoryController::class, 'indexAdmin']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::get('/categories/{uuid}', [CategoryController::class, 'showAdmin']);
            Route::put('/categories/{uuid}', [CategoryController::class, 'update']);
            Route::delete('/categories/{uuid}', [CategoryController::class, 'destroy']);

            Route::get('/products', [ProductController::class, 'indexAdmin']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::get('/products/{uuid}', [ProductController::class, 'showAdmin']);
            Route::put('/products/{uuid}', [ProductController::class, 'update']);
            Route::delete('/products/{uuid}', [ProductController::class, 'destroy']);

            Route::post('/products/{productUuid}/images', [ProductImageController::class, 'store']);
            Route::patch('/products/{productUuid}/images/reorder', [ProductImageController::class, 'reorder']);
            Route::delete('/images/{uuid}', [ProductImageController::class, 'destroy']);

            Route::get('/whatsapp-numbers', [WhatsAppNumberController::class, 'indexAdmin']);
            Route::post('/whatsapp-numbers', [WhatsAppNumberController::class, 'store']);
            Route::get('/whatsapp-numbers/{uuid}', [WhatsAppNumberController::class, 'showAdmin']);
            Route::put('/whatsapp-numbers/{uuid}', [WhatsAppNumberController::class, 'update']);
            Route::delete('/whatsapp-numbers/{uuid}', [WhatsAppNumberController::class, 'destroy']);

            Route::get('/settings', [SiteSettingController::class, 'index']);
            Route::put('/settings/{key}', [SiteSettingController::class, 'update']);

            Route::post('/upload/image', [UploadController::class, 'image']);
            Route::delete('/upload/image', [UploadController::class, 'deleteImage']);

            Route::get('/orders', [OrderLogController::class, 'indexAdmin']);
            Route::get('/orders/{uuid}', [OrderLogController::class, 'showAdmin']);
            Route::patch('/orders/{uuid}/status', [OrderLogController::class, 'updateStatus']);
            Route::delete('/orders/{uuid}', [OrderLogController::class, 'destroy']);
        });
});
