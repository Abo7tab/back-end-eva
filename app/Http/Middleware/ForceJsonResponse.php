<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        // Force request to accept JSON
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        $content = $response->getContent();
        $decoded = json_decode((string)$content, true);
        
        // Strip the existing content-type so JsonResponse can set it to application/json
        $headers = $response->headers->all();
        unset($headers['content-type']);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            
            // Case A: Already fully wrapped by us (has "success" key)
            if (array_key_exists('success', $decoded)) {
                if (isset($decoded['errors']) && is_array($decoded['errors']) && empty($decoded['errors'])) {
                    $decoded['errors'] = new \stdClass();
                }
                return new JsonResponse(
                    $decoded,
                    $response->getStatusCode(),
                    $headers
                );
            }
            
            // Case B: Laravel Resource collection or single Resource
            // (has "data" key but no "success" key)
            if (array_key_exists('data', $decoded) && count($decoded) === 1) {
                return new JsonResponse([
                    'success' => $response->isSuccessful(),
                    'data'    => $decoded['data'],
                    'message' => $response->isSuccessful() ? 'OK' : 'Error',
                ], $response->getStatusCode(), $headers);
            }
            
            // Case C: Laravel Resource with meta (paginated collection)
            if (array_key_exists('data', $decoded) 
                && (array_key_exists('meta', $decoded) || array_key_exists('links', $decoded))
            ) {
                return new JsonResponse([
                    'success' => $response->isSuccessful(),
                    'data'    => $decoded['data'],
                    'meta'    => $decoded['meta'] ?? null,
                    'message' => $response->isSuccessful() ? 'OK' : 'Error',
                ], $response->getStatusCode(), $headers);
            }
            
            // Case D: Plain associative array
            return new JsonResponse([
                'success' => $response->isSuccessful(),
                'data'    => $decoded,
                'message' => $response->isSuccessful() ? 'OK' : 'Error',
            ], $response->getStatusCode(), $headers);
        }
        
        // Case 2: plain string/other content
        return new JsonResponse([
            'success' => $response->isSuccessful(),
            'data'    => $content,
            'message' => $response->isSuccessful() ? 'OK' : 'Error',
        ], $response->getStatusCode(), $headers);
    }
}
