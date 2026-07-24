<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Operations;

use App\Domains\Operations\Resources\WhatsAppNumberResource;
use App\Domains\Operations\Services\WhatsAppRoutingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\StoreWhatsAppNumberRequest;
use App\Http\Requests\Operations\UpdateWhatsAppNumberRequest;
use Illuminate\Http\Request;

class WhatsAppNumberController extends Controller
{
    public function __construct(private WhatsAppRoutingService $service) {}

    public function indexAdmin(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        return WhatsAppNumberResource::collection($this->service->listForAdmin($perPage));
    }

    public function showAdmin(string $uuid)
    {
        $number = $this->service->findByUuid($uuid) ?? abort(404, 'Number not found');
        return new WhatsAppNumberResource($number);
    }

    public function store(StoreWhatsAppNumberRequest $request)
    {
        $number = $this->service->create($request->validated());
        return new WhatsAppNumberResource($number);
    }

    public function update(UpdateWhatsAppNumberRequest $request, string $uuid)
    {
        $number = $this->service->findByUuid($uuid) ?? abort(404, 'Number not found');
        $updated = $this->service->update($number, $request->validated());
        return new WhatsAppNumberResource($updated);
    }

    public function destroy(string $uuid)
    {
        $number = $this->service->findByUuid($uuid) ?? abort(404, 'Number not found');
        $this->service->delete($number);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
