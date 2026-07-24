<?php declare(strict_types=1);

namespace App\Domains\Catalog\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryUploadService
{
    public function __construct(
        private readonly Cloudinary $cloudinary
    ) {}
    
    public function upload(UploadedFile $file, string $folder): array
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            $this->defaultOptions($folder)
        );
        
        return $this->formatResult($result);
    }
    
    public function uploadFromBase64(string $base64Data, string $folder): array
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $base64Data,
            $this->defaultOptions($folder)
        );
        
        return $this->formatResult($result);
    }
    
    public function delete(string $publicId): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return ($result['result'] ?? '') === 'ok';
        } catch (\Throwable $e) {
            \Log::warning('Cloudinary delete failed', [
                'public_id' => $publicId,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    private function defaultOptions(string $folder): array
    {
        return [
            'folder'          => 'eva-beauty-hub/' . $folder,
            'fetch_format'    => 'auto',
            'quality'         => 'auto:good',
            'resource_type'   => 'image',
            'unique_filename' => true,
            'overwrite'       => false,
        ];
    }
    
    private function formatResult($result): array
    {
        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }
}
