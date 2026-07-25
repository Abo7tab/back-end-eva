<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\OrderLogging;

use App\Domains\OrderLogging\Resources\OrderLogResource;
use App\Domains\OrderLogging\Services\OrderLogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderLogController extends Controller
{
    public function __construct(private OrderLogService $service) {}

    public function indexAdmin(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $search = $request->query('search');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        return OrderLogResource::collection($this->service->listForAdmin($perPage, $search, $status, $sortBy));
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getStats(),
            'message' => 'Stats retrieved',
        ]);
    }

    public function updateStatus(Request $request, string $uuid)
    {
        $request->validate([
            'status' => 'required|string|in:pending,redirected,completed,failed',
        ]);
        
        $order = $this->service->updateStatus($uuid, $request->input('status'));
        return new OrderLogResource($order);
    }

    public function showAdmin(string $uuid)
    {
        $orderLog = $this->service->findByUuid($uuid) ?? abort(404, 'Order log not found');
        return new OrderLogResource($orderLog);
    }

    public function destroy(string $uuid)
    {
        $orderLog = $this->service->findByUuid($uuid) ?? abort(404, 'Order log not found');
        $this->service->delete($orderLog);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
