<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\OrderLogging;

use App\Domains\OrderLogging\Services\OrderLogService;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderLogging\CheckoutRequest;

class CheckoutController extends Controller
{
    public function __construct(private OrderLogService $service) {}

    public function store(CheckoutRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->checkout($request->validated()),
            'message' => 'Checkout processed',
        ]);
    }
}
