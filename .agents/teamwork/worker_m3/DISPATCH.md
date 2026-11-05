## 2026-10-12T06:26:32Z
You are Worker M3 tasked with implementing Milestone 3: Gemini AI Integration Suite with Graceful Fallbacks (R3) for Bazaario.
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3
Project root: c:\xampp\htdocs\bazaario
Original Request file: c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md (read timestamp ## 2026-10-12T05:17:47Z).
Project Specification: c:\xampp\htdocs\bazaario\.agents\teamwork\orchestrator_10\PROJECT.md
Explorer Survey Report: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r3\report.md
Explorer Survey Handoff: c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r3\handoff.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

File Ownership:
You own exclusively:
- `app/Services/GeminiApiService.php`
- `app/Services/GeminiService.php`
- `config/services.php`
- `app/Http/Controllers/AiAssistantController.php`
- `routes/web.php` (lines for AI endpoints)
- `resources/views/user/account/compare.blade.php`
- `resources/views/user/account/recommendations.blade.php`
- `tests/Feature/GeminiAiIntegrationTest.php`

Implementation Tasks:
1. `config/services.php`:
   - Add `'gemini'` configuration key:
     `'gemini' => ['api_key' => env('GEMINI_API_KEY'), 'model' => env('GEMINI_MODEL', 'gemini-1.5-flash')],`
2. `app/Services/GeminiApiService.php`:
   - Implement complete, extensible service:
     - `resolveApiKey()`: checks `SiteSetting::get('gemini_api_key')`, then `config('services.gemini.api_key')`, then `env('GEMINI_API_KEY')`.
     - `getPersonalizedRecommendations(?User $user = null, int $limit = 6): array`
       - If API key is present: sends prompt to Gemini API with user interaction context (`user_product_interactions` table) or category interests, requesting JSON structure.
       - If API key is absent or API error occurs: executes deterministic local fallback scoring products by rating/sales/views, generating human-like recommendation reasons (e.g. "Trending in Fresh Produce", "Matches your recent browsing"). Returns `['status' => 'success', 'source' => 'local_fallback', 'items' => [...]]`. Never throws 500.
     - `compareProducts(array $productIds): array`
       - Queries products by IDs.
       - If API key is present: queries Gemini API with product specs for structured comparison.
       - If API key is absent or API error occurs: generates structured deterministic local comparison:
         `'products' => [...], 'attributes' => ['price', 'unit_type', 'stock', 'rating'], 'pros_cons' => [...], 'recommendation' => string, 'verdict' => string, 'source' => 'local_fallback'`. Never throws 500.
     - `chat(string $prompt, array $history = [], ?User $user = null, ?int $conversationId = null): array`
       - Secure database lookup tools:
         - `lookupUserOrders(?User $user, ?string $query = null)`: queries `$user->orders` (strictly scoped to `$user->id`, returns order number, status, tracking, items). If user is unauthenticated, prompts user to log in before sharing order info. Never leaks other users' orders.
         - `lookupProducts(string $keyword)`: queries active products (`Product::where('status', 'active')`), returns name, price, unit, stock, rating, link.
         - `lookupStorePolicies(string $topic)`: injects authoritative return and escrow policies (48h for perishables, 7d for handicrafts, escrow payment held safely until delivery, 24-48h escrow refund).
         - `lookupLiveAuctions()`: queries active auctions with current price and time remaining.
       - If API key is present: injects database context into Gemini system prompt and parses conversational response.
       - If API key is absent or API error occurs: executes conversational keyword-matching rule engine utilizing the tool data (e.g. "Your order #... is currently ...", "Here are matching products: ...", "Our return policy for perishables is 48 hours..."), returning structured conversational reply with `source => 'local_fallback'`. Zero 500 errors.
3. `app/Services/GeminiService.php`:
   - Update to delegate `chat()` to `GeminiApiService` to maintain 100% backward compatibility for existing callers.
4. `app/Http/Controllers/AiAssistantController.php`:
   - `chat(Request $request)`: validates message, passes `Auth::user()`, calls `GeminiApiService::chat()`, returns JSON response (`['success' => true, 'reply' => $reply, 'source' => $source]`).
   - `recommendations(Request $request)`: calls `GeminiApiService::getPersonalizedRecommendations(Auth::user())`, returns JSON or view.
   - `compare(Request $request)`: accepts `product_ids` array, calls `GeminiApiService::compareProducts($productIds)`, returns JSON or view.
   - Wire view rendering for `/user/compare` (or `/compare`) and `/user/recommendations` (or `/recommendations`).
5. Update `resources/views/user/account/compare.blade.php`:
   - Modern UI rendering the structured comparison table/matrix, pros & cons pills, and AI verdict badge. If fewer than 2 products selected, displays an interactive product picker or helpful empty state.
6. Test Suite `tests/Feature/GeminiAiIntegrationTest.php`:
   - Test missing API key returns structured recommendations with zero 500 errors (`source => 'local_fallback'`).
   - Test missing API key returns structured comparison with zero 500 errors.
   - Test missing API key returns conversational chatbot response with zero 500 errors.
   - Test chatbot order lookup queries authenticated user's own orders accurately.
   - Test chatbot order lookup rejects or blocks access to other users' orders (security tenant isolation).
   - Test chatbot product search finds active products.
   - Test chatbot policy query explains return and escrow policies.
   - Test simulated valid API response with `Http::fake()` parses live structured response correctly (`source => 'gemini'`).
   - Test HTTP endpoints `/ai/chat`, `/ai/recommendations`, `/ai/compare` respond with status 200.
   Run tests:
   `php artisan test tests/Feature/GeminiAiIntegrationTest.php`
   `php artisan test tests/Feature/Milestone2LogicReliabilityTest.php`
   Ensure all tests pass with 0 failures.

Deliverables:
- Write changes to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3\changes.md`.
- Write handoff report to `c:\xampp\htdocs\bazaario\.agents\teamwork\worker_m3\handoff.md`.
- Send completion message to parent upon finishing.
