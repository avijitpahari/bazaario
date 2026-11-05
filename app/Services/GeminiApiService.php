<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserProductInteraction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiApiService
{
    /**
     * Resolve the Gemini API Key following configuration hierarchy:
     * 1. SiteSetting::get('gemini_api_key')
     * 2. config('services.gemini.api_key')
     * 3. env('GEMINI_API_KEY')
     */
    public function resolveApiKey(): ?string
    {
        $dbKey = SiteSetting::get('gemini_api_key');
        if (!empty($dbKey) && is_string($dbKey) && trim($dbKey) !== '') {
            return trim($dbKey);
        }

        $configKey = config('services.gemini.api_key');
        if (!empty($configKey) && is_string($configKey) && trim($configKey) !== '') {
            return trim($configKey);
        }

        $envKey = env('GEMINI_API_KEY');
        if (!empty($envKey) && is_string($envKey) && trim($envKey) !== '') {
            return trim($envKey);
        }

        return null;
    }

    /**
     * Resolve active model.
     */
    public function resolveModel(): string
    {
        $dbModel = SiteSetting::get('gemini_model');
        if (!empty($dbModel) && is_string($dbModel) && trim($dbModel) !== '') {
            return trim($dbModel);
        }

        return config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-1.5-flash');
    }

    /**
     * Resolve temperature setting.
     */
    public function resolveTemperature(): float
    {
        $val = SiteSetting::get('temperature');
        return is_numeric($val) ? (float) $val : 0.7;
    }

    /**
     * Determine if a live API key is configured.
     */
    public function isLiveApiConfigured(): bool
    {
        return !empty($this->resolveApiKey());
    }

    /**
     * Feature 1: Personalized Recommendations
     * Generates tailored product suggestions based on user interaction history,
     * category affinity, or top community ratings, accompanied by transparent explanations.
     *
     * @return array{status: string, source: string, items: array}
     */
    public function getPersonalizedRecommendations(?User $user = null, int $limit = 6): array
    {
        try {
            $apiKey = $this->resolveApiKey();

            // 1. Gather candidate products and user context
            $candidateData = $this->gatherRecommendationCandidates($user, $limit);
            $candidates = $candidateData['candidates'];
            $userContext = $candidateData['context'];

            if ($candidates->isEmpty()) {
                return [
                    'status' => 'success',
                    'source' => 'local_fallback',
                    'items' => [],
                ];
            }

            // 2. If API Key is present, try Gemini
            if (!empty($apiKey)) {
                $geminiResult = $this->requestGeminiRecommendations($apiKey, $candidates, $userContext, $limit);
                if ($geminiResult !== null) {
                    return [
                        'status' => 'success',
                        'source' => 'gemini',
                        'items' => $geminiResult,
                    ];
                }
            }

            // 3. Fallback to deterministic local engine
            return [
                'status' => 'success',
                'source' => 'local_fallback',
                'items' => $this->buildFallbackRecommendations($candidates, $userContext),
            ];
        } catch (\Throwable $e) {
            Log::warning('GeminiApiService getPersonalizedRecommendations fallback: ' . $e->getMessage());

            $fallbackCandidates = Product::where('status', 'active')
                ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                ->orderByDesc('average_rating')
                ->take($limit)
                ->get();

            return [
                'status' => 'success',
                'source' => 'local_fallback',
                'items' => $this->buildFallbackRecommendations($fallbackCandidates, ['preferred_categories' => []]),
            ];
        }
    }

    /**
     * Feature 2: Structured Product Comparison
     * Generates side-by-side matrices, itemized pros & cons, use cases, and an AI verdict.
     *
     * @return array{status: string, products: array, attributes: array, pros_cons: array, recommendation: string, verdict: string, source: string}
     */
    public function compareProducts(array $productIds): array
    {
        try {
            $productIds = array_values(array_filter(array_map('intval', $productIds)));

            $products = Product::whereIn('id', $productIds)
                ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile', 'reviews'])
                ->get();

            // If fewer than 2 products, try finding complementary product from same category or top catalog
            if ($products->count() === 1) {
                $first = $products->first();
                $additional = Product::where('status', 'active')
                    ->where('id', '!=', $first->id)
                    ->when($first->category_id, fn($q) => $q->where('category_id', $first->category_id))
                    ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                    ->take(1)
                    ->get();

                if ($additional->isEmpty()) {
                    $additional = Product::where('status', 'active')
                        ->where('id', '!=', $first->id)
                        ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                        ->take(1)
                        ->get();
                }

                $products = $products->merge($additional);
            }

            if ($products->isEmpty()) {
                return [
                    'status' => 'success',
                    'products' => [],
                    'attributes' => ['price', 'unit_type', 'stock', 'rating'],
                    'pros_cons' => [],
                    'recommendation' => 'Select at least 2 products to view comparative analysis.',
                    'verdict' => 'No products selected for comparison.',
                    'source' => 'local_fallback',
                ];
            }

            $apiKey = $this->resolveApiKey();

            if (!empty($apiKey) && $products->count() >= 2) {
                $geminiResult = $this->requestGeminiComparison($apiKey, $products);
                if ($geminiResult !== null) {
                    return array_merge([
                        'status' => 'success',
                        'source' => 'gemini',
                    ], $geminiResult);
                }
            }

            return array_merge([
                'status' => 'success',
                'source' => 'local_fallback',
            ], $this->buildFallbackComparison($products));
        } catch (\Throwable $e) {
            Log::warning('GeminiApiService compareProducts fallback: ' . $e->getMessage());

            return [
                'status' => 'success',
                'products' => [],
                'attributes' => ['price', 'unit_type', 'stock', 'rating'],
                'pros_cons' => [],
                'recommendation' => 'Comparative overview temporarily unavailable. Please browse individual product specifications.',
                'verdict' => 'Comparison analysis complete.',
                'source' => 'local_fallback',
            ];
        }
    }

    /**
     * Feature 3: Conversational Shopping Assistant with Database Tools
     *
     * @return array{success: bool, reply: string, tools_used: array, source: string}
     */
    public function chat(string $prompt, array $history = [], ?User $user = null, ?int $conversationId = null): array
    {
        try {
            $toolsData = [];
            $toolsUsed = [];
            $lower = strtolower($prompt);

            // 1. Tool execution: Order Lookup
            if ($this->shouldLookupOrders($lower)) {
                $orderInfo = $this->lookupUserOrders($user, $prompt);
                $toolsData['orders'] = $orderInfo;
                $toolsUsed[] = 'lookupUserOrders';
            }

            // 2. Tool execution: Product Catalog Lookup
            if ($this->shouldLookupProducts($lower)) {
                $productInfo = $this->lookupProducts($prompt);
                if (!empty($productInfo)) {
                    $toolsData['products'] = $productInfo;
                    $toolsUsed[] = 'lookupProducts';
                }
            }

            // 3. Tool execution: Platform Store Policies Lookup
            if ($this->shouldLookupPolicies($lower)) {
                $policyInfo = $this->lookupStorePolicies($prompt);
                $toolsData['policies'] = $policyInfo;
                $toolsUsed[] = 'lookupStorePolicies';
            }

            // 4. Tool execution: Live Auctions Lookup
            if ($this->shouldLookupAuctions($lower)) {
                $auctionInfo = $this->lookupLiveAuctions();
                if (!empty($auctionInfo)) {
                    $toolsData['auctions'] = $auctionInfo;
                    $toolsUsed[] = 'lookupLiveAuctions';
                }
            }

            $apiKey = $this->resolveApiKey();

            // If API key is available, call Gemini with injected tool context
            if (!empty($apiKey)) {
                $geminiReply = $this->requestGeminiChat($apiKey, $prompt, $history, $toolsData, $user);
                if (!empty($geminiReply)) {
                    $this->persistChatMessage($user, $prompt, $geminiReply, $toolsUsed, $conversationId);
                    return [
                        'success' => true,
                        'reply' => $geminiReply,
                        'tools_used' => $toolsUsed,
                        'source' => 'gemini',
                    ];
                }
            }

            // Deterministic local fallback chat engine
            $fallbackReply = $this->buildFallbackChatReply($prompt, $toolsData, $user);
            $this->persistChatMessage($user, $prompt, $fallbackReply, $toolsUsed, $conversationId);

            return [
                'success' => true,
                'reply' => $fallbackReply,
                'tools_used' => $toolsUsed,
                'source' => 'local_fallback',
            ];
        } catch (\Throwable $e) {
            Log::warning('GeminiApiService chat fallback: ' . $e->getMessage());

            $safeReply = "Namaskar! I am Bazaario AI Assistant. I can help you search our catalog of artisanal crafts, fresh agricultural harvest, live wholesale auctions, check store policies, and track your delivery orders.\n\nTry asking me:\n• Where is my order?\n• What is the return policy for fresh fruits?\n• Show me organic Alphonso mangoes\n• What live auctions are running today?";

            return [
                'success' => true,
                'reply' => $safeReply,
                'tools_used' => [],
                'source' => 'local_fallback',
            ];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Database Lookup Tools
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Tool: Scoped User Order Tracking Telemetry
     * Strictly queries $user->orders to prevent cross-user tenant data leakage.
     */
    public function lookupUserOrders(?User $user, ?string $query = null): ?array
    {
        if (!$user) {
            return [
                'authenticated' => false,
                'message' => 'User is not logged in. Prompt user to log in to view their orders.',
            ];
        }

        // Try extracting order code / digits from query
        $extractedCode = null;
        if ($query) {
            if (preg_match('/ORD-[A-Za-z0-9\-]+/i', $query, $m)) {
                $extractedCode = $m[0];
            } elseif (preg_match('/\b\d{4,}\b/', $query, $m)) {
                $extractedCode = $m[0];
            }
        }

        $orderQuery = $user->orders()->with(['sellerOrders.items.product']);

        if ($extractedCode) {
            $orderQuery->where(function ($q) use ($extractedCode) {
                $q->where('order_number', 'like', "%{$extractedCode}%")
                  ->orWhere('id', $extractedCode);
            });
        }

        $orders = $orderQuery->latest('placed_at')->latest('id')->take(3)->get();

        // If specific code was searched but not found in this user's orders
        if ($extractedCode && $orders->isEmpty()) {
            return [
                'authenticated' => true,
                'found' => false,
                'searched_code' => $extractedCode,
                'orders' => [],
                'message' => "Order #{$extractedCode} was not found under your account.",
            ];
        }

        if ($orders->isEmpty()) {
            return [
                'authenticated' => true,
                'found' => false,
                'orders' => [],
                'message' => 'No past orders found in your account.',
            ];
        }

        $ordersData = $orders->map(function ($order) {
            $firstSellerOrder = $order->sellerOrders->first();
            $courier = $firstSellerOrder?->courier_name ?? 'Bazaario Express Fleet';
            $tracking = $firstSellerOrder?->tracking_number ?? 'BZ-TRK-' . $order->id;
            $deliverySlot = $firstSellerOrder?->delivery_slot ?? 'Standard Express Window';

            $items = $order->sellerOrders->flatMap(function ($so) {
                return $so->items->map(function ($item) {
                    return [
                        'name' => $item->product_name ?? optional($item->product)->name ?? 'Marketplace Item',
                        'quantity' => (int) $item->quantity,
                        'price' => (float) $item->unit_price,
                    ];
                });
            })->values()->all();

            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'total_amount' => (float) $order->total_amount,
                'placed_at' => $order->placed_at ? $order->placed_at->format('M d, Y') : null,
                'courier_name' => $courier,
                'tracking_number' => $tracking,
                'delivery_slot' => $deliverySlot,
                'items' => $items,
                'url' => url('/user/orders/' . $order->id),
            ];
        })->all();

        return [
            'authenticated' => true,
            'found' => true,
            'orders' => $ordersData,
        ];
    }

    /**
     * Tool: Product Catalog Lookup
     * Queries active products matching keywords.
     */
    public function lookupProducts(string $keyword): array
    {
        $stopWords = ['do', 'you', 'have', 'show', 'me', 'find', 'what', 'is', 'are', 'the', 'a', 'an', 'best', 'recommend', 'products', 'product', 'items', 'item', 'catalog', 'price', 'of', 'in', 'for', 'any', 'please', 'can', 'i', 'get', 'there'];
        $words = array_values(array_filter(preg_split('/[^a-zA-Z0-9]+/', strtolower($keyword)), function ($w) use ($stopWords) {
            return strlen($w) >= 3 && !in_array($w, $stopWords);
        }));

        $query = Product::where('status', 'active')
            ->with(['category', 'seller.sellerProfile', 'primaryImage']);

        if (!empty($words)) {
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', "%{$word}%")
                      ->orWhere('description', 'like', "%{$word}%")
                      ->orWhere('short_description', 'like', "%{$word}%")
                      ->orWhereHas('category', function ($catQ) use ($word) {
                          $catQ->where('name', 'like', "%{$word}%");
                      });
                }
            });
        }

        $products = $query->take(4)->get();

        if ($products->isEmpty() && empty($words)) {
            $products = Product::where('status', 'active')
                ->with(['category', 'seller.sellerProfile', 'primaryImage'])
                ->orderByDesc('average_rating')
                ->take(3)
                ->get();
        }

        return $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => (float) $p->price,
                'unit_type' => $p->unit_type ?? 'item',
                'stock' => (int) $p->stock,
                'rating' => (float) ($p->average_rating ?? 4.8),
                'category' => $p->category?->name ?? 'General',
                'seller' => $p->seller?->sellerProfile?->business_name ?? 'Verified Store',
                'url' => url('/products/' . ($p->slug ?? $p->id)),
            ];
        })->all();
    }

    /**
     * Tool: Platform Store Policies
     * Authoritative ground truth on return windows, escrow guarantees, and refunds.
     */
    public function lookupStorePolicies(string $topic = ''): array
    {
        return [
            'perishables_return_window' => '48 hours from delivery (fresh produce, fruits, vegetables, dairy, grains).',
            'handicrafts_return_window' => '7 days from delivery (handicrafts, textiles, pottery, dry goods).',
            'escrow_protection' => 'Smart Escrow Guarantee: Buyer funds are locked securely in platform escrow until delivery is verified and the return inspection window elapses.',
            'refund_timelines' => [
                'online' => '24 to 48 hours for UPI, Credit/Debit cards, and Net Banking released back to the source bank account.',
                'cod' => 'Instant Bazaario wallet credit voucher, or direct NEFT/UPI bank settlement within 3 business days.',
            ],
            'shipping' => 'Hyperlocal express and standard delivery slots scheduled directly with verified local farmers and merchants.',
            'seller_onboarding' => 'Open to verified farmers, artisans, and kiranas at /seller/onboarding with GSTIN/PAN and escrow bank verification.',
        ];
    }

    /**
     * Tool: Live Auctions Telemetry
     */
    public function lookupLiveAuctions(): array
    {
        $auctions = Auction::whereIn('status', ['live', 'active'])
            ->where('ends_at', '>', now())
            ->with(['product', 'bids'])
            ->take(3)
            ->get();

        return $auctions->map(function ($a) {
            $highestBid = $a->bids->sortByDesc('amount')->first();
            $currentPrice = $highestBid ? (float) $highestBid->amount : (float) $a->starting_price;

            $diffMinutes = max(1, (int) now()->diffInMinutes($a->ends_at, false));
            $timeRemaining = $diffMinutes >= 60
                ? round($diffMinutes / 60, 1) . ' hours'
                : "{$diffMinutes} minutes";

            return [
                'id' => $a->id,
                'product_name' => $a->product?->name ?? 'Mandi Lot #' . $a->id,
                'starting_price' => (float) $a->starting_price,
                'current_price' => $currentPrice,
                'time_remaining' => $timeRemaining,
                'url' => url('/auctions/' . $a->id),
            ];
        })->all();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal Helper & External Gemini API Handlers
    // ─────────────────────────────────────────────────────────────────────────

    protected function gatherRecommendationCandidates(?User $user, int $limit): array
    {
        $preferredCategoryIds = [];
        $interactedProductIds = [];

        if ($user) {
            $interactions = UserProductInteraction::where('user_id', $user->id)
                ->latest('created_at')
                ->take(30)
                ->get();

            $interactedProductIds = $interactions->pluck('product_id')->filter()->unique()->all();

            if (!empty($interactedProductIds)) {
                $preferredCategoryIds = Product::whereIn('id', $interactedProductIds)
                    ->pluck('category_id')
                    ->filter()
                    ->unique()
                    ->all();
            }

            // Also check recent orders
            $orderedProductIds = $user->orders()
                ->latest('placed_at')
                ->take(3)
                ->with('sellerOrders.items')
                ->get()
                ->flatMap(fn($o) => $o->sellerOrders->flatMap(fn($so) => $so->items->pluck('product_id')))
                ->filter()
                ->all();

            $interactedProductIds = array_unique(array_merge($interactedProductIds, $orderedProductIds));
        }

        $candidates = collect();

        if (!empty($preferredCategoryIds)) {
            $categoryCandidates = Product::where('status', 'active')
                ->whereIn('category_id', $preferredCategoryIds)
                ->whereNotIn('id', $interactedProductIds)
                ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                ->orderByDesc('average_rating')
                ->take($limit)
                ->get();

            $candidates = $candidates->merge($categoryCandidates);
        }

        if ($candidates->count() < $limit) {
            $needed = $limit - $candidates->count();
            $excludeIds = array_unique(array_merge($interactedProductIds, $candidates->pluck('id')->all()));

            $topRated = Product::where('status', 'active')
                ->whereNotIn('id', $excludeIds)
                ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                ->orderByDesc('average_rating')
                ->orderByDesc('total_reviews')
                ->take($needed)
                ->get();

            $candidates = $candidates->merge($topRated);
        }

        // If still empty (e.g. fresh DB), get any active products
        if ($candidates->isEmpty()) {
            $candidates = Product::where('status', 'active')
                ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile'])
                ->take($limit)
                ->get();
        }

        return [
            'candidates' => $candidates->take($limit),
            'context' => [
                'user_id' => $user?->id,
                'preferred_categories' => $preferredCategoryIds,
                'interacted_count' => count($interactedProductIds),
            ],
        ];
    }

    protected function requestGeminiRecommendations(string $apiKey, Collection $candidates, array $userContext, int $limit): ?array
    {
        $model = $this->resolveModel();
        $catalogSummary = $candidates->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->category?->name ?? 'General',
                'price' => (float) $p->price,
                'rating' => (float) ($p->average_rating ?? 4.8),
            ];
        })->all();

        $systemInstruction = "You are an intelligent recommendation engine for Bazaario Marketplace. Rank and select up to {$limit} products for the user. Return ONLY a valid JSON array of objects with the keys: 'id' (integer), 'reason' (string explaining why recommended, e.g. 'Trending in Fresh Produce', 'Matches your browsing history'). Do not wrap in markdown tags or extra text.";

        $promptText = "Candidate Products:\n" . json_encode($catalogSummary) . "\nUser Context:\n" . json_encode($userContext);

        $response = $this->callGeminiApi($apiKey, $model, $promptText, $systemInstruction);
        if (!$response) {
            return null;
        }

        $decoded = $this->extractJsonPayload($response);
        if (!is_array($decoded) || empty($decoded)) {
            return null;
        }

        $itemsById = $candidates->keyBy('id');
        $result = [];

        foreach ($decoded as $entry) {
            if (!is_array($entry) || empty($entry['id'])) {
                continue;
            }
            $p = $itemsById->get($entry['id']);
            if (!$p) {
                continue;
            }

            $result[] = $this->formatProductItem($p, $entry['reason'] ?? 'Recommended by Bazaario AI');
        }

        // If Gemini returned fewer items than candidates, fill with remaining
        if (count($result) < $candidates->count()) {
            $alreadyIncludedIds = collect($result)->pluck('id')->all();
            foreach ($candidates as $p) {
                if (!in_array($p->id, $alreadyIncludedIds)) {
                    $result[] = $this->formatProductItem($p, 'Curated community favorite');
                }
            }
        }

        return !empty($result) ? array_slice($result, 0, $limit) : null;
    }

    protected function buildFallbackRecommendations(Collection $candidates, array $userContext): array
    {
        $items = [];
        $preferredCatIds = $userContext['preferred_categories'] ?? [];

        foreach ($candidates as $p) {
            $categoryName = $p->category?->name ?? 'Marketplace';
            $rating = (float) ($p->average_rating ?? 4.8);

            if (!empty($preferredCatIds) && in_array($p->category_id, $preferredCatIds)) {
                $reason = "Matches your recent interest in {$categoryName}";
            } elseif ($p->is_perishable) {
                $reason = "Fresh seasonal harvest direct from local farm gates";
            } elseif ($rating >= 4.7) {
                $reason = "Community favorite with exceptional {$rating}★ rating";
            } else {
                $reason = "Trending listing in {$categoryName}";
            }

            $items[] = $this->formatProductItem($p, $reason);
        }

        return $items;
    }

    protected function formatProductItem(Product $p, string $reason): array
    {
        $imgPath = $p->primaryImage?->image_path ?? optional($p->images->first())->image_path;
        $imgUrl = $imgPath ? asset($imgPath) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600';

        return [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug ?? (string) $p->id,
            'price' => (float) $p->price,
            'unit_type' => $p->unit_type ?? 'item',
            'stock' => (int) $p->stock,
            'average_rating' => (float) ($p->average_rating ?? 4.8),
            'total_reviews' => (int) ($p->total_reviews ?? 0),
            'category' => $p->category?->name ?? 'General',
            'seller_name' => $p->seller?->sellerProfile?->business_name ?? 'Verified Store',
            'image_url' => $imgUrl,
            'reason' => $reason,
        ];
    }

    protected function requestGeminiComparison(string $apiKey, Collection $products): ?array
    {
        $model = $this->resolveModel();
        $specs = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'unit_type' => $p->unit_type ?? 'unit',
                'stock' => (int) $p->stock,
                'rating' => (float) ($p->average_rating ?? 4.8),
                'perishable' => (bool) $p->is_perishable,
                'category' => $p->category?->name ?? 'Artisan',
                'seller' => $p->seller?->sellerProfile?->business_name ?? 'Verified Store',
            ];
        })->all();

        $systemInstruction = "You are a product comparison specialist for Bazaario. Compare the provided products. Return ONLY a valid JSON object with the following schema:
{
  \"attributes\": [\"price\", \"unit_type\", \"stock\", \"rating\"],
  \"pros_cons\": {
     \"<productId>\": { \"pros\": [\"string\"], \"cons\": [\"string\"] }
  },
  \"recommendation\": \"string\",
  \"verdict\": \"string\"
}
Do not wrap in extra markdown or introductory text.";

        $promptText = "Products to compare:\n" . json_encode($specs);

        $response = $this->callGeminiApi($apiKey, $model, $promptText, $systemInstruction);
        if (!$response) {
            return null;
        }

        $decoded = $this->extractJsonPayload($response);
        if (!is_array($decoded) || empty($decoded['recommendation']) || empty($decoded['verdict'])) {
            return null;
        }

        $productsData = $products->map(fn($p) => $this->formatProductItem($p, ''))->all();

        return [
            'products' => $productsData,
            'attributes' => $decoded['attributes'] ?? ['price', 'unit_type', 'stock', 'rating'],
            'pros_cons' => $decoded['pros_cons'] ?? [],
            'recommendation' => (string) $decoded['recommendation'],
            'verdict' => (string) $decoded['verdict'],
        ];
    }

    protected function buildFallbackComparison(Collection $products): array
    {
        $productsData = $products->map(fn($p) => $this->formatProductItem($p, ''))->all();

        $cheapest = $products->sortBy('price')->first();
        $priciest = $products->sortByDesc('price')->first();
        $highestRated = $products->sortByDesc('average_rating')->first();

        $prosCons = [];

        foreach ($products as $p) {
            $pros = [];
            $cons = [];

            if ($cheapest && $p->id === $cheapest->id && $products->count() > 1) {
                $pros[] = 'Most economical price point with maximum savings';
            }
            if ($highestRated && $p->id === $highestRated->id) {
                $pros[] = "Top-tier customer satisfaction rating ({$p->average_rating}★)";
            }
            if ($p->is_perishable) {
                $pros[] = 'Farm-fresh harvest covered by 48-hour freshness escrow guarantee';
            } else {
                $pros[] = 'Extended shelf life protected by 7-day return policy';
            }
            if ($p->stock > 20) {
                $pros[] = 'Abundant inventory ready for immediate express dispatch';
            }

            if ($priciest && $p->id === $priciest->id && $products->count() > 1) {
                $cons[] = 'Higher upfront investment';
            }
            if ($p->stock < 5) {
                $cons[] = 'Low stock warning — order soon to secure inventory';
            }
            if (($p->total_reviews ?? 0) < 5) {
                $cons[] = 'Limited customer review volume compared to older listings';
            }

            if (empty($pros)) {
                $pros[] = 'Quality verified listing from trusted merchant';
            }
            if (empty($cons)) {
                $cons[] = 'Standard delivery window applies';
            }

            $prosCons[(string) $p->id] = [
                'pros' => $pros,
                'cons' => $cons,
            ];
        }

        $rec = ($cheapest && $highestRated && $cheapest->id !== $highestRated->id)
            ? "Choose {$cheapest->name} for best everyday value, or {$highestRated->name} for top-rated artisanal quality."
            : "Both items offer competitive marketplace specs backed by Bazaario Escrow Guarantee.";

        $verdict = ($highestRated)
            ? "{$highestRated->name} is our top recommendation based on verified customer ratings and overall reliability."
            : "All compared products meet Bazaario quality benchmarks.";

        return [
            'products' => $productsData,
            'attributes' => ['price', 'unit_type', 'stock', 'rating'],
            'pros_cons' => $prosCons,
            'recommendation' => $rec,
            'verdict' => $verdict,
        ];
    }

    protected function requestGeminiChat(string $apiKey, string $prompt, array $history, array $toolsData, ?User $user): ?string
    {
        $model = $this->resolveModel();
        $systemInstruction = "You are 'Bazaario AI', the official intelligent shopping and customer support assistant for Bazaario Marketplace (West Bengal / India).
You help buyers find products, track their orders, check live auctions, calculate shipping and delivery windows, and explain platform policies in English, Bengali, or Hindi.
Security Rules:
- If asked about an order and order data is present in DATABASE_CONTEXT, report only that specific order's details. Never fabricate other users' orders.
- If asked about an order and the user is not authenticated, instruct them to log in at /login.
- Keep responses courteous, helpful, concise, and formatted in clean markdown (use **bold** and [links](/path)).
- Use the provided DATABASE_CONTEXT as authoritative real-time truth.";

        $contextBlock = "\n\nDATABASE_CONTEXT:\n" . json_encode($toolsData, JSON_PRETTY_PRINT);
        $fullPrompt = $prompt . $contextBlock;

        $response = $this->callGeminiApi($apiKey, $model, $fullPrompt, $systemInstruction);
        if (!$response) {
            return null;
        }

        return trim($response);
    }

    protected function buildFallbackChatReply(string $prompt, array $toolsData, ?User $user): string
    {
        $lower = strtolower($prompt);

        // 1. Order tracking response
        if (isset($toolsData['orders'])) {
            $ordersTool = $toolsData['orders'];
            if (!$ordersTool['authenticated']) {
                return "Please [log in to your Bazaario account](/login) to check your order details, delivery status, and tracking telemetry.";
            }

            if (!$ordersTool['found']) {
                $searched = $ordersTool['searched_code'] ?? null;
                if ($searched) {
                    return "Order **#{$searched}** was not found in your account. Please check your order ID or review all past orders in [My Orders](/user/orders).";
                }
                return "You do not have any orders placed yet on Bazaario. Explore our [Product Catalog](/products) to discover fresh harvest and artisan goods!";
            }

            $orderLines = [];
            foreach ($ordersTool['orders'] as $ord) {
                $itemsDesc = !empty($ord['items'])
                    ? implode(', ', array_map(fn($i) => "{$i['name']} (x{$i['quantity']})", array_slice($ord['items'], 0, 2)))
                    : 'Consignment items';

                $orderLines[] = "✦ **Order #{$ord['order_number']}**\n- **Status:** **" . ucfirst($ord['order_status']) . "**\n- **Total:** ₹" . number_format($ord['total_amount'], 2) . "\n- **Courier:** {$ord['courier_name']}\n- **Tracking ID:** `{$ord['tracking_number']}`\n- **Items:** {$itemsDesc}\n- [Track Consignment on Dashboard]({$ord['url']})";
            }

            return "Here are the live tracking details for your order:\n\n" . implode("\n\n", $orderLines);
        }

        // 2. Store policy inquiries
        if (isset($toolsData['policies']) || $this->shouldLookupPolicies($lower)) {
            $p = $this->lookupStorePolicies($prompt);
            return "✦ **Bazaario Platform Policies & Escrow Protection:**\n\n" .
                   "- **Fresh Produce & Perishables:** Return window is **{$p['perishables_return_window']}**\n" .
                   "- **Handicrafts & Non-perishables:** Return window is **{$p['handicrafts_return_window']}**\n" .
                   "- **Escrow Guarantee:** {$p['escrow_protection']}\n" .
                   "- **Online Refunds:** Escrow funds released within **{$p['refund_timelines']['online']}** to original payment method.\n" .
                   "- **COD Refunds:** Handled via **{$p['refund_timelines']['cod']}**.\n\n" .
                   "To initiate an inspection claim, visit [Returns & Refunds](/user/returns).";
        }

        // 3. Product catalog inquiries
        if (isset($toolsData['products'])) {
            $items = $toolsData['products'];
            $list = array_map(function ($p) {
                return "- [{$p['name']}](/products/{$p['slug']}) — **₹" . number_format($p['price'], 2) . "** / {$p['unit_type']} ({$p['rating']}★, Store: {$p['seller']})";
            }, $items);

            return "Here are top matching listings available on Bazaario right now:\n\n" . implode("\n", $list) .
                   "\n\nAll goods ship directly from verified producers with Smart Escrow protection 🚚!";
        }

        // 4. Live Auctions inquiries
        if (isset($toolsData['auctions']) || $this->shouldLookupAuctions($lower)) {
            $auctions = $toolsData['auctions'] ?? $this->lookupLiveAuctions();
            if (!empty($auctions)) {
                $lines = array_map(function ($a) {
                    return "- [{$a['product_name']}]({$a['url']}) — Current Bid: **₹" . number_format($a['current_price'], 2) . "** (Time left: {$a['time_remaining']})";
                }, $auctions);
                return "✦ **Active Mandi Live Auctions:**\n\n" . implode("\n", $lines) .
                       "\n\nPlace your bids with zero buyer commission at [Live Auctions](/auctions)!";
            }
            return "✦ **Bazaario Live Mandi Auctions:**\nExplore active wholesale batches and rare antiques on our [Live Auctions](/auctions) terminal! Place dynamic bids in real-time with zero buyer commission.";
        }

        // 5. Seller inquiries
        if (str_contains($lower, 'seller') || str_contains($lower, 'sell') || str_contains($lower, 'onboard')) {
            return "✦ **Become a Verified Bazaario Seller:**\nFarmers, artisans, and kirana stores can register via the [Seller Onboarding Hub](/seller/onboarding). Complete KYC with GSTIN, Trade License, and Escrow Bank verification to sell nationwide!";
        }

        // Default shopping assistant greeting
        return "Hello! I am Bazaario AI Assistant. I can help you search our catalog of artisanal crafts, fresh agricultural harvest, live wholesale auctions, check store policies, and track your delivery orders.\n\nTry asking me:\n• Where is my order?\n• What is the return policy for fresh fruits?\n• Show me organic Alphonso mangoes\n• What live auctions are running today?";
    }

    protected function shouldLookupOrders(string $prompt): bool
    {
        return (bool) preg_match('/\b(order|track|tracking|shipment|delivery\s*status|where\s*is\s*my|ord-|courier)\b/i', $prompt);
    }

    protected function shouldLookupProducts(string $prompt): bool
    {
        if (preg_match('/\b(product|products|item|items|price|buy|cost|shop|search|catalog|stock|available|have|show|find|looking|recommend|mango|tea|fruit|vegetable|produce|craft|honey|rice|spice|saree|silk|sweet|sweets|sondesh|fish|grain|flour|oil|ghee)\b/i', $prompt)) {
            return true;
        }

        $stopWords = ['do', 'you', 'what', 'is', 'are', 'the', 'a', 'an', 'in', 'for', 'any', 'please', 'can', 'i', 'get', 'there'];
        $words = array_values(array_filter(preg_split('/[^a-zA-Z0-9]+/', strtolower($prompt)), function ($w) use ($stopWords) {
            return strlen($w) >= 3 && !in_array($w, $stopWords);
        }));

        if (!empty($words)) {
            return Product::where('status', 'active')
                ->where(function ($q) use ($words) {
                    foreach (array_slice($words, 0, 5) as $w) {
                        $q->orWhere('name', 'like', "%{$w}%");
                    }
                })->exists();
        }

        return false;
    }

    protected function shouldLookupPolicies(string $prompt): bool
    {
        return (bool) preg_match('/\b(return|returns|refund|refunds|policy|policies|escrow|guarantee|warranty|cancel|rules)\b/i', $prompt);
    }

    protected function shouldLookupAuctions(string $prompt): bool
    {
        return (bool) preg_match('/\b(auction|auctions|bid|bids|bidding|mandi|lot)\b/i', $prompt);
    }

    protected function callGeminiApi(string $apiKey, string $model, string $prompt, string $systemInstruction): ?string
    {
        try {
            $baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
            $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";
            $temperature = $this->resolveTemperature();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($url, [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => 1200,
                ],
            ]);

            if ($response->successful()) {
                $candidate = $response->json('candidates.0.content.parts.0.text');
                if (!empty($candidate)) {
                    return $candidate;
                }
            }

            Log::warning('Gemini API call failed or returned empty candidate', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::warning('Gemini API HTTP Exception: ' . $e->getMessage());
            return null;
        }
    }

    protected function extractJsonPayload(string $raw): mixed
    {
        $clean = trim($raw);

        // Strip ```json ... ``` or ``` ... ```
        if (preg_match('/^```(?:json)?\s*([\s\S]*?)\s*```$/i', $clean, $matches)) {
            $clean = trim($matches[1]);
        }

        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // If JSON string starts in middle
        $firstBracket = strpos($clean, '[');
        $firstBrace = strpos($clean, '{');

        $startPos = false;
        if ($firstBracket !== false && $firstBrace !== false) {
            $startPos = min($firstBracket, $firstBrace);
        } elseif ($firstBracket !== false) {
            $startPos = $firstBracket;
        } elseif ($firstBrace !== false) {
            $startPos = $firstBrace;
        }

        if ($startPos !== false) {
            $substr = substr($clean, $startPos);
            $decoded = json_decode($substr, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }

    protected function persistChatMessage(?User $user, string $prompt, string $reply, array $toolsUsed, ?int $conversationId): void
    {
        try {
            if (!$user) {
                return;
            }

            $conv = null;
            if ($conversationId) {
                $conv = ChatConversation::where('id', $conversationId)->where('user_id', $user->id)->first();
            }

            if (!$conv) {
                $conv = ChatConversation::where('user_id', $user->id)->where('status', 'active')->latest()->first();
            }

            if (!$conv) {
                $conv = ChatConversation::create([
                    'user_id' => $user->id,
                    'title' => 'Support & Shopping Chat',
                    'status' => 'active',
                ]);
            }

            ChatMessage::create([
                'conversation_id' => $conv->id,
                'role' => 'user',
                'message' => $prompt,
                'tool_name' => null,
                'tool_data' => null,
            ]);

            ChatMessage::create([
                'conversation_id' => $conv->id,
                'role' => 'assistant',
                'message' => $reply,
                'tool_name' => !empty($toolsUsed) ? implode(',', $toolsUsed) : null,
                'tool_data' => !empty($toolsUsed) ? ['tools' => $toolsUsed] : null,
            ]);
        } catch (\Throwable $e) {
            // Silently ignore logging failures to guarantee zero 500 errors
            Log::debug('Chat message persistence ignored: ' . $e->getMessage());
        }
    }
}
