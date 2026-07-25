<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Domains\Catalog\Services\CloudinaryUploadService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function __construct(
        private readonly CloudinaryUploadService $cloudinary
    ) {}

    /**
     * Upload a single image to Cloudinary (generic).
     * Used by admin panel for product covers, brand logos, etc.
     */
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'folder' => 'nullable|string|in:products,brands,categories,general',
        ]);

        $folder = $request->input('folder', 'general');

        try {
            $result = $this->cloudinary->upload(
                $request->file('image'),
                $folder
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $result['url'],
                    'public_id' => $result['public_id'],
                ],
                'message' => 'تم رفع الصورة بنجاح',
            ]);
        } catch (\Throwable $e) {
            Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'folder' => $folder,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل رفع الصورة',
                'errors' => (object)[],
            ], 500);
        }
    }

    /**
     * Delete an image from Cloudinary by public_id.
     */
    public function deleteImage(Request $request): JsonResponse
    {
        $request->validate([
            'public_id' => 'required|string',
        ]);

        $deleted = $this->cloudinary->delete($request->input('public_id'));

        return response()->json([
            'success' => $deleted,
            'data' => (object)[],
            'message' => $deleted ? 'تم حذف الصورة' : 'فشل حذف الصورة',
        ], $deleted ? 200 : 500);
    }
}
