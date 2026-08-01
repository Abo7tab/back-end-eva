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
        $perPage = (int) $request->query('per_page', 12);
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
        $data = $request->validated();
        
        if (isset($data['is_active']) && $data['is_active'] === false && $number->is_active) {
            $activeCount = \App\Domains\Operations\Models\WhatsAppNumber::active()->count();
            if ($activeCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تعطيل آخر رقم واتساب نشط.',
                    'errors' => (object)[],
                ], 422);
            }
        }

        $updated = $this->service->update($number, $data);
        return new WhatsAppNumberResource($updated);
    }

    public function destroy(string $uuid)
    {
        $number = $this->service->findByUuid($uuid) ?? abort(404, 'Number not found');
        
        if ($number->is_active) {
            $activeCount = \App\Domains\Operations\Models\WhatsAppNumber::active()->count();
            if ($activeCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف آخر رقم واتساب نشط. يجب إبقاء رقم واحد نشط على الأقل.',
                    'errors' => (object)[],
                ], 422);
            }
        }
        
        $this->service->delete($number);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
