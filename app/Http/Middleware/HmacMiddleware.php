<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HmacMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verify HMAC-SHA256 signature from client to prevent request forgery.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only verify for API routes that have a body
        if (!$request->is('api/*')) {
            return $next($request);
        }

        // Exempt payment webhook from HMAC (called by external gateways)
        if ($request->is('api/payment/webhook')) {
            return $next($request);
        }

        $signature = $request->header('X-Hmac-Signature');
        $timestamp = $request->header('X-Hmac-Timestamp');

        if (empty($signature) || empty($timestamp)) {
            Log::warning('HMAC: Missing signature or timestamp', [
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            return response()->json(['success' => false, 'message' => 'Missing authentication headers.'], 401);
        }

        // Check timestamp is not too old (max 30 seconds)
        $now = time();
        if (abs($now - (int)$timestamp) > 30) {
            Log::warning('HMAC: Timestamp expired', [
                'ip' => $request->ip(),
                'timestamp' => $timestamp,
                'now' => $now,
            ]);
            return response()->json(['success' => false, 'message' => 'Request expired.'], 401);
        }

        // Build payload string from request body
        $payload = $request->getContent();
        $expectedSignature = $this->computeHash($payload, $timestamp);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('HMAC: Signature mismatch', [
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            return response()->json(['success' => false, 'message' => 'Invalid signature.'], 401);
        }

        return $next($request);
    }

    /**
     * Compute HMAC-SHA256 hash.
     */
    public static function computeHash(string $payload, string $timestamp): string
    {
        $secret = config('app.hmac_secret_key');
        if (empty($secret)) {
            $secret = env('HMAC_SECRET_KEY', '');
        }
        $data = $payload . '|' . $timestamp;
        return hash_hmac('sha256', $data, $secret);
    }
}
