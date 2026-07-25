<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Domains\Admin\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Admin::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($request->query('per_page', 20));
        $admins = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $admins->items(),
            'meta' => [
                'current_page' => $admins->currentPage(),
                'per_page' => $admins->perPage(),
                'total' => $admins->total(),
                'last_page' => $admins->lastPage(),
            ],
            'message' => 'OK',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'تم إنشاء الأدمن بنجاح',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $admin = Admin::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'OK',
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($admin->id),
            ],
        ]);

        $admin->update($validated);

        return response()->json([
            'success' => true,
            'data' => $admin->fresh(),
            'message' => 'تم تحديث بيانات الأدمن',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $currentAdmin = $request->user('sanctum');

            if ($currentAdmin && $currentAdmin->id === $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك حذف حسابك الخاص',
                    'errors' => (object) [],
                ], 422);
            }

            $admin = Admin::findOrFail($id);
            $admin->delete();

            return response()->json([
                'success' => true,
                'data' => (object) [],
                'message' => 'تم حذف الأدمن بنجاح',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Deletion Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف. قد يكون الأدمن مرتبطاً ببيانات أخرى.',
                'errors' => (object) [],
            ], 500);
        }
    }

    public function resetPassword(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::findOrFail($id);
        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'data' => (object) [],
            'message' => 'تم إعادة تعيين كلمة المرور',
        ]);
    }
}
