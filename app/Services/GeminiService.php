<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected GeminiApiService $apiService;

    public function __construct(?GeminiApiService $apiService = null)
    {
        $this->apiService = $apiService ?? app(GeminiApiService::class);
    }

    /**
     * Ask Bazaario AI shopping assistant.
     * Delegates to GeminiApiService for full capability, database tools, and graceful fallbacks.
     */
    public function chat(string $prompt, array $history = []): array
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        return $this->apiService->chat($prompt, $history, $user);
    }

    /**
     * Smart local rule-based catalog assistant fallback if called directly.
     */
    public function fallbackCatalogAssistant(string $prompt): string
    {
        $result = $this->apiService->chat($prompt, [], null);
        return $result['reply'] ?? '';
    }
}
