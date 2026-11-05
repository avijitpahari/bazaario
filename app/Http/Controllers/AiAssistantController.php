<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\GeminiApiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    /**
     * Customer support and shopping assistant chatbot endpoint.
     */
    public function chat(Request $request, GeminiApiService $gemini): JsonResponse
    {
        $request->validate([
            'prompt' => 'required_without:message|nullable|string|max:1000',
            'message' => 'required_without:prompt|nullable|string|max:1000',
        ]);

        $prompt = trim((string) ($request->input('prompt') ?: $request->input('message')));
        $history = $request->input('history', []);
        $conversationId = $request->input('conversation_id');
        $user = Auth::guard('user')->user() ?? Auth::user();

        $result = $gemini->chat(
            $prompt,
            is_array($history) ? $history : [],
            $user,
            $conversationId ? (int) $conversationId : null
        );

        return response()->json([
            'success' => true,
            'reply' => $result['reply'] ?? '',
            'source' => $result['source'] ?? 'local_fallback',
            'tools_used' => $result['tools_used'] ?? [],
        ]);
    }

    /**
     * Personalized recommendations endpoint (JSON / View).
     */
    public function recommendations(Request $request, GeminiApiService $gemini): JsonResponse|View
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        $limit = (int) $request->input('limit', 8);

        $result = $gemini->getPersonalizedRecommendations($user, $limit);

        if ($request->wantsJson() || $request->is('ai/*') || $request->ajax()) {
            return response()->json($result);
        }

        return $this->renderRecommendationsView($result, $user);
    }

    /**
     * User account recommendations view.
     */
    public function recommendationsView(Request $request, GeminiApiService $gemini): View
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        $limit = (int) $request->input('limit', 8);
        $result = $gemini->getPersonalizedRecommendations($user, $limit);

        return $this->renderRecommendationsView($result, $user);
    }

    /**
     * Structured product comparison endpoint (JSON / View).
     */
    public function compare(Request $request, GeminiApiService $gemini): JsonResponse|View
    {
        $productIds = $request->input('product_ids', $request->input('products', []));
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_values(array_filter(array_map('intval', (array) $productIds)));

        $result = $gemini->compareProducts($productIds);

        if ($request->wantsJson() || $request->is('ai/*') || $request->ajax()) {
            return response()->json($result);
        }

        return $this->renderCompareView($result, $productIds);
    }

    /**
     * User account product comparison view.
     */
    public function compareView(Request $request, GeminiApiService $gemini): View
    {
        $productIds = $request->input('product_ids', $request->input('products', []));
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_values(array_filter(array_map('intval', (array) $productIds)));

        $result = $gemini->compareProducts($productIds);

        return $this->renderCompareView($result, $productIds);
    }

    /**
     * Helper to render recommendations view with eager-loaded models and AI metadata.
     */
    protected function renderRecommendationsView(array $result, mixed $user): View
    {
        $items = $result['items'] ?? [];
        $ids = collect($items)->pluck('id')->filter()->all();

        $recommendedProducts = collect();
        if (!empty($ids)) {
            $reasonsById = collect($items)->keyBy('id')->map(fn($i) => $i['reason'] ?? 'Recommended for you');

            $models = Product::whereIn('id', $ids)
                ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                ->get()
                ->keyBy('id');

            // Preserve recommendation ranking order and attach reason
            foreach ($ids as $id) {
                if ($models->has($id)) {
                    $prod = $models->get($id);
                    $prod->ai_reason = $reasonsById->get($id, 'Curated community favorite');
                    $recommendedProducts->push($prod);
                }
            }
        }

        return view('user.account.recommendations', [
            'user' => $user,
            'recommendations' => $result,
            'recommendedProducts' => $recommendedProducts,
        ]);
    }

    /**
     * Helper to render comparison view.
     */
    protected function renderCompareView(array $result, array $productIds): View
    {
        $selectableProducts = Product::where('status', 'active')
            ->whereNotIn('id', $productIds)
            ->with(['category', 'primaryImage'])
            ->orderByDesc('average_rating')
            ->take(8)
            ->get();

        return view('user.account.compare', [
            'user' => Auth::guard('user')->user() ?? Auth::user(),
            'comparison' => $result,
            'selectedProductIds' => $productIds,
            'selectableProducts' => $selectableProducts,
        ]);
    }
}
