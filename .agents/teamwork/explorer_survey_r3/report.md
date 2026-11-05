# Gemini AI Integration Suite (Requirement 3) — Comprehensive Architecture & Survey Report

**Author:** Explorer Subagent (explorer_survey_r3)  
**Date:** October 12, 2026  
**Target Module:** Requirement 3 (R3) — Gemini AI Integration Suite  
**Working Directory:** `c:\xampp\htdocs\bazaario\.agents\teamwork\explorer_survey_r3`  
**Project Root:** `c:\xampp\htdocs\bazaario`  

---

## 1. Executive Summary

Requirement 3 requires building an enterprise-grade, extensible Gemini AI service (`App\Services\GeminiApiService`) for the **Bazaario** marketplace. The service must deliver three core capabilities:
1. **Personalized Recommendations**: Generating tailored product suggestions based on user browsing history, cart/wishlist additions, and category affinity, accompanied by transparent, human-readable explanations.
2. **Structured Product Comparisons**: Side-by-side comparative matrices, pros, cons, and persona/use-case recommendations (e.g., *"Best for daily cooking"*, *"Best budget pick"*).
3. **AI Customer Support Chatbot with Database Lookup Tools**: A conversational shopping assistant that securely queries live database context for order tracking/telemetry (scoped strictly to the authenticated user), product catalog inventory & pricing, and store policies (returns, refunds, escrow guarantees, delivery windows).
4. **Graceful Fallback & Fault Tolerance**: When `GEMINI_API_KEY` is missing from `.env` or `site_settings`, or when external API calls fail (4xx/5xx/network timeouts), the service must return high-quality mock/local data without throwing 500 errors.

---

## 2. Current Codebase State Analysis

### 2.1 Existing Service (`App\Services\GeminiService.php`)
- **File Location:** `c:\xampp\htdocs\bazaario\app\Services\GeminiService.php` (115 lines)
- **Current Behavior:** Contains a single `chat(string $prompt, array $history = [])` method. It checks `$settings['gemini_api_key'] ?? env('GEMINI_API_KEY')` and makes a basic HTTP POST to Google Gemini v1beta endpoint.
- **Deficiencies:**
  - Does NOT implement `App\Services\GeminiApiService` as specified by Requirement 3.
  - Lacks personalized product recommendations (`getPersonalizedRecommendations()`).
  - Lacks structured product comparisons (`compareProducts()`).
  - Lacks database tool integrations (no order tracking lookups, no scoped authenticated user checks, no catalog searches beyond simple string contains, no structured policy retrieval).
  - Does not persist conversation history to `chat_conversations` or `chat_messages` tables.

### 2.2 Configuration & Environment Keys
- **`config/services.php`:**
  - Currently has entries for Postmark, Resend, SES, and Slack.
  - **Missing:** `'gemini'` configuration key.
  - **Required Configuration:**
    ```php
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    ],
    ```
- **`.env` and `.env.example`:**
  - `GEMINI_API_KEY` is not defined in `.env` (or is empty).
  - Admin panel persists settings in `site_settings` table via `SiteSetting::set('gemini_api_key', ...)`, `SiteSetting::set('gemini_model', ...)`, etc.
  - **Resolution Hierarchy:**
    1. `SiteSetting::get('gemini_api_key')` (Admin runtime override)
    2. `config('services.gemini.api_key')` / `env('GEMINI_API_KEY')`
    3. If both empty or null -> Trigger Graceful Fallback Mode.

### 2.3 Existing Controllers & Routes
- **Chatbot Endpoint:**
  - Route: `Route::post('/ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');` in `routes/web.php` (line 59).
  - Controller: `app/Http/Controllers/AiAssistantController.php` (29 lines)
  - Accepts `{ prompt: string, history?: array }`.
  - Currently unauthenticated-only and does not pass user context or conversation identifiers.
- **Recommendations Route:**
  - Route: `Route::get('/user/recommendations', ...)->name('recommendations');` in `routes/web.php` (lines 187-194).
  - Currently returns 8 latest products directly via an inline closure.
  - View: `resources/views/user/account/recommendations.blade.php`.
- **Product Comparison Route:**
  - Route: `Route::get('/user/compare', fn () => view('user.account.compare', ...))->name('compare');` in `routes/web.php` (line 196).
  - View: `resources/views/user/account/compare.blade.php` (static placeholder: *"No products to compare"*).
- **Frontend Chat Widget:**
  - Located in `resources/views/components/footer.blade.php` (lines 294-436).
  - Docked floating widget *"✦ Ask Bazaario AI"* connects to `route('ai.chat')` via `fetch()`. Renders Markdown (`**bold**` and `[links](/path)`).

### 2.4 Existing Database Schema & Models
The database schema already contains dedicated tables designed for AI integration:
1. **`user_product_interactions` table (`App\Models\UserProductInteraction`):**
   - Columns: `id`, `user_id`, `product_id`, `interaction_type` (`view`, `search_click`, `cart`, `wishlist`, `purchase`, `compare`), `metadata` (json), `created_at`.
   - Indexed on `['user_id', 'interaction_type']` and `['product_id', 'interaction_type']`.
2. **`chat_conversations` table (`App\Models\ChatConversation`):**
   - Columns: `id`, `user_id`, `title`, `status` (`active`, `closed`), `timestamps`.
3. **`chat_messages` table (`App\Models\ChatMessage`):**
   - Columns: `id`, `conversation_id`, `role` (`user`, `assistant`, `system`), `message`, `tool_name`, `tool_data` (json), `created_at`.
4. **`orders` & `seller_orders` tables (`App\Models\Order`, `App\Models\SellerOrder`):**
   - Order fields: `id`, `order_number`, `user_id`, `order_status`, `payment_status`, `total_amount`, `placed_at`.
   - Seller order fields: `seller_order_number`, `courier_name`, `tracking_number`, `status`, `delivery_slot`, `shipped_at`, `delivered_at`.
5. **`products` table (`App\Models\Product`):**
   - Fields: `name`, `slug`, `price`, `unit_type`, `stock`, `status`, `average_rating`, `total_reviews`, `harvest_date`, `expiry_date`, `is_perishable`.
   - Relations: `seller.sellerProfile`, `category`, `primaryImage`, `images`, `reviews`.
6. **`site_settings` table (`App\Models\SiteSetting`):**
   - Keys: `gemini_api_key`, `gemini_model`, `temperature`, `auto_triage_enabled`, `recommendation_engine_enabled`.

---

## 3. Architecture Specification for `App\Services\GeminiApiService`

### 3.1 Class Blueprint
```php
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
    // Configuration resolution
    public function getApiKey(): ?string;
    public function getModel(): string;
    public function getTemperature(): float;
    public function isLiveApiConfigured(): bool;

    // Feature 1: Personalized Recommendations
    public function getPersonalizedRecommendations(?User $user = null, int $limit = 8): array;

    // Feature 2: Structured Product Comparison
    public function compareProducts(array $productIds): array;

    // Feature 3: Customer Support Chatbot with Database Lookup Tools
    public function chat(string $prompt, array $history = [], ?User $user = null, ?int $conversationId = null): array;

    // Database Lookup Tools
    public function lookupOrders(?User $user, string $prompt): ?array;
    public function searchCatalog(string $prompt): array;
    public function getStorePoliciesContext(string $prompt): string;
    public function getLiveAuctionsContext(): array;

    // API Gateway & Execution
    protected function callGemini(string $prompt, ?string $systemInstruction = null, bool $jsonMode = false): ?array;

    // Graceful Fallback Handlers
    protected function fallbackRecommendations(?User $user, int $limit): array;
    protected function fallbackComparison(Collection $products): array;
    protected function fallbackChat(string $prompt, ?User $user, array $toolsData): string;
}
```

---

## 4. Deep Dive: Core Capabilities & Tool Integrations

### 4.1 Feature 1: Personalized Recommendations Engine
#### Goal:
Return high-intent recommendations with transparent explanations (e.g. *"Recommended because you viewed organic groceries and artisanal teas"*).

#### Execution Flow:
1. **Gather Interaction Signals:**
   - If `$user` is provided:
     - Query recent interactions from `user_product_interactions` (viewed/carted/purchased products in last 30 days).
     - Query user's wishlist items (`$user->wishlist?->items`).
     - Query past orders (`$user->orders()->latest()->with('items.product')->take(3)->get()`).
     - Extract preferred category IDs and keywords.
   - If user has no interactions or is guest:
     - Query trending, highly-rated active products (`Product::where('status', 'active')->orderByDesc('average_rating')->take($limit)->get()`).
2. **When `GEMINI_API_KEY` is present:**
   - Prompt Gemini with candidate products, user interaction history, and category affinities.
   - Request structured JSON ranking products with explanation strings and affinity scores.
3. **When `GEMINI_API_KEY` is missing / fails (Fallback Mode):**
   - Query products matching user's top categories or overall top-rated active items.
   - Dynamically synthesize explanations:
     - If matched by category: *"Recommended based on your interest in {$category->name}."*
     - If top rated: *"Top-rated community favorite with {$rating}★ rating."*
     - If seasonal/organic: *"Fresh seasonal harvest sourced directly from verified farmers."*
   - Return structured array with zero 500 errors.

---

### 4.2 Feature 2: Structured Product Comparisons
#### Goal:
Provide side-by-side matrices, itemized pros & cons, persona use-cases, and an AI verdict for 2–4 products.

#### Execution Flow:
1. **Validate and Load Products:**
   - Accept array of product IDs: `[id1, id2, ...]`.
   - Fetch `Product::whereIn('id', $productIds)->with(['category', 'seller.sellerProfile', 'primaryImage', 'reviews'])->get()`.
   - If fewer than 2 products supplied, return validation notice or auto-suggest a competitor from the same category.
2. **When `GEMINI_API_KEY` is present:**
   - Build a structured prompt detailing Product 1 and Product 2: specifications, price, unit type, stock, rating, seller trust, harvest date.
   - Request structured JSON response adhering to schema:
     ```json
     {
       "summary": "...",
       "matrix": [
         {"attribute": "Price", "product_1": "₹150", "product_2": "₹220"},
         {"attribute": "Unit Type", "product_1": "1 kg", "product_2": "500 g"},
         {"attribute": "Rating", "product_1": "4.8★", "product_2": "4.5★"},
         {"attribute": "Perishable", "product_1": "Yes (48h)", "product_2": "No"}
       ],
       "pros_cons": {
         "product_1": {"pros": ["...", "..."], "cons": ["..."]},
         "product_2": {"pros": ["...", "..."], "cons": ["..."]}
       },
       "use_cases": {
         "product_1": "Best for daily family cooking",
         "product_2": "Best for premium gifting & special occasions"
       },
       "verdict": "..."
     }
     ```
3. **When `GEMINI_API_KEY` is missing / fails (Fallback Mode):**
   - Deterministic algorithmic comparison:
     - Compare price: identifies cheaper option (*"Budget-friendly value"*) vs premium option (*"Premium artisanal grade"*).
     - Compare rating & reviews: identifies community favorite.
     - Compare unit type & stock: highlights bulk vs single unit advantage.
     - Generate realistic pros & cons based on database attributes.
     - Generate persona use-cases based on price point and category.
     - Generate structured output conforming identically to the JSON schema. Zero 500 errors.

---

### 4.3 Feature 3: AI Customer Support Chatbot with Database Tools
#### Goal:
Provide an intelligent shopping and support assistant capable of inspecting database records securely.

#### Database Lookup Tools Specification:

#### Tool 1: Order Status & Tracking Telemetry
- **Security Guardrail:** All order queries MUST be scoped to `$user->id`. A user can never inspect orders of another customer. If unauthenticated, prompt the user to log in.
- **Trigger Detection:** Regex match for `order`, `tracking`, `#?ORD-[A-Za-z0-9\-]+`, or order digits in the user's prompt.
- **Execution:**
  - Search order by `order_number` or `id` where `user_id = $user->id`.
  - If no ID is specified, retrieve the user's latest 3 orders.
  - Return: Order number, status (`pending`, `processing`, `shipped`, `delivered`, `cancelled`), payment status, courier name, tracking number, delivery slot, items, and total amount.
  - Generates conversational response with direct links to `/user/orders/{id}`.

#### Tool 2: Product Catalog Inventory & Price Lookup
- **Trigger Detection:** Keywords matching product catalog items (e.g., mango, tea, spice, cotton, organic, vegetables, sweets).
- **Execution:**
  - Query `Product::where('status', 'active')->where(function($q) use ($keywords) { ... })->with(['category', 'seller.sellerProfile'])->take(4)->get()`.
  - Return: Product title, price in `₹`, unit type (`kg`, `dozen`, `bundle`, `litre`), stock availability, seller shop name, and link `/products/{slug}`.

#### Tool 3: Platform Store Policies Ground Truth
- **Authoritative Policy Knowledge Base:**
  - **Returns:**
    - Perishable & Farm Goods (fruits, vegetables, dairy, grains): **48 hours** from delivery.
    - Handicrafts & Non-perishables (textiles, pottery, leather): **7 days** from delivery.
    - Where to file: Customer Dashboard -> [Returns & Refunds](/user/returns).
  - **Refunds:**
    - Smart Escrow Guarantee: Payment held in escrow until inspection window elapses.
    - Online payments (UPI, Cards, Net Banking): Escrow refund released within **24–48 hours** to source account.
    - Cash on Delivery (COD): Instant credit voucher or direct bank/UPI transfer within **3 business days**.
  - **Delivery & Shipping:**
    - Hyperlocal standard and express delivery slots selectable during checkout.
    - Telemetry tracking per seller consignment.
  - **Seller Registration:**
    - Guided onboarding at `/seller/onboarding` requiring GSTIN/PAN and bank account verification.

#### Tool 4: Live Mandi Auctions Lookup
- **Trigger Detection:** Mentions of `auction`, `bid`, `mandi`, `wholesale lot`.
- **Execution:**
  - Query `Auction::where('status', 'live')->where('ends_at', '>', now())->with(['product', 'highestBid'])->take(3)->get()`.
  - Return: Active auction title, current highest bid, reserve status, time remaining, link `/auctions/{id}`.

---

## 5. Graceful Fallback Strategy (Zero-500 Architecture)

To ensure enterprise-grade reliability, the service follows a multi-tier resilience pipeline:

```
[User Request]
       │
       ▼
[Check API Key in site_settings / env]
       │
   ┌───┴───┐
   │       │
(No Key) (Key Present)
   │       │
   │       ▼
   │   [Call Gemini REST API (15s timeout)]
   │       │
   │   ┌───┴──────────┐
   │   │              │
   │ (Success)  (Error / Timeout / 4xx / 5xx)
   │   │              │
   │   ▼              ▼
   │ [Parse JSON]  [Log Warning]
   │   │              │
   └───┼──────────────┘
       │
       ▼
[Deterministic Local Engine]
  - Database Tool Query (Orders, Catalog, Policies, Auctions)
  - Algorithmic Synthesis (Pros/Cons, Reasons, Markdown)
       │
       ▼
[Return Clean Structured Output / HTTP 200]
```

### Fallback Quality Guarantees:
1. **Chatbot:** Never responds with generic *"An error occurred"*. Instead, it evaluates the exact database tools and answers with live order details, active catalog items with prices and links, or authoritative policy terms.
2. **Recommendations:** Always provides 4–8 active products with category badges, prices, and human-readable AI rationales.
3. **Comparisons:** Always produces valid, non-empty side-by-side matrices, pros, cons, and use-case verdicts based on the real product attributes.
4. **Error Masking:** All external exceptions (`GuzzleException`, `ConnectionException`, `JsonException`) are caught and logged without propagating 500 errors to the client.

---

## 6. Route and Controller Mapping

### 6.1 Routes to Wire in `routes/web.php`
1. **Chatbot Route (Public / Authenticated):**
   ```php
   Route::post('/ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');
   ```
2. **AI Recommendations Route (API & User Account):**
   ```php
   Route::get('/ai/recommendations', [AiAssistantController::class, 'recommendations'])->name('ai.recommendations');
   Route::get('/user/recommendations', [AiAssistantController::class, 'recommendationsView'])
       ->middleware(['auth:user', 'user'])
       ->name('user.recommendations');
   ```
3. **AI Comparison Route (API & User Account):**
   ```php
   Route::post('/ai/compare', [AiAssistantController::class, 'compare'])->name('ai.compare');
   Route::get('/user/compare', [AiAssistantController::class, 'compareView'])
       ->middleware(['auth:user', 'user'])
       ->name('user.compare');
   ```

### 6.2 View Enhancements
1. **`resources/views/user/account/compare.blade.php`:**
   - Enhance from current static empty placeholder to support dynamic side-by-side comparison when products are selected (via query params `?products=1,2` or session compare list).
   - Display:
     - Product cards with images, prices, seller badges.
     - Attribute matrix table (price, unit, stock, rating, origin, return window).
     - AI Pros & Cons cards per item.
     - Use-case badges (*"Best for Daily Cooking"*, *"Best Value"*).
     - Overall AI Verdict banner.
2. **`resources/views/user/account/recommendations.blade.php`:**
   - Bind dynamic AI reason badges (*"✦ AI Pick: Recommended based on your organic food views"*) directly to product cards.

---

## 7. Testing & Quality Gate Strategy

A dedicated test suite `tests/Feature/GeminiAiIntegrationTest.php` must be implemented to verify:
1. **Fallback Without API Key:**
   - Instantiation of `GeminiApiService` without `GEMINI_API_KEY`.
   - `chat()` returns valid markdown response with `source => 'local_fallback'` and HTTP 200.
   - `getPersonalizedRecommendations()` returns structured products and explanations.
   - `compareProducts([$p1->id, $p2->id])` returns structured matrix, pros, cons, use cases.
2. **Database Tool Authorization & Security:**
   - Chatbot querying orders for User A returns only User A's orders.
   - Chatbot querying orders for unauthenticated user prompts login and does NOT leak order details.
   - Chatbot catalog search returns active products and filters out inactive/hidden ones.
   - Chatbot policy query returns accurate 48h perishable and 7-day handicraft return windows.
3. **Simulated Live Gemini API via `Http::fake()`:**
   - When API key is present and Gemini returns valid JSON/markdown, service parses it with `source => 'gemini'`.
   - When Gemini returns 403 (invalid key), 429 (quota exceeded), 500 (internal error), or timeout, service catches error, logs warning, and gracefully returns fallback data without 500.
4. **Endpoint Verification:**
   - `POST /ai/chat` returns HTTP 200 with JSON structure.
   - `POST /ai/compare` returns HTTP 200 with structured comparison JSON.
   - `GET /ai/recommendations` returns HTTP 200 with structured recommendations JSON.
   - `GET /user/recommendations` renders HTTP 200 with AI insight badges.
   - `GET /user/compare` renders HTTP 200 with empty state or comparative view.

---

## 8. Summary Table of Implementation Files

| Component / Artifact | File Path | Action Required |
| :--- | :--- | :--- |
| **Primary AI Service** | `app/Services/GeminiApiService.php` | **Create new service** implementing recommendations, comparisons, chatbot tools, and fallbacks. |
| **Existing Service Adapter** | `app/Services/GeminiService.php` | **Maintain compatibility** by delegating to `GeminiApiService`. |
| **AI Controller** | `app/Http/Controllers/AiAssistantController.php` | **Extend methods** for `chat()`, `recommendations()`, `compare()`, `compareView()`, and `recommendationsView()`. |
| **Service Configuration** | `config/services.php` | **Add `'gemini'` configuration key** (`api_key`, `model`, `base_url`). |
| **Environment Variable** | `.env.example` | **Document `GEMINI_API_KEY`** and default `GEMINI_MODEL`. |
| **Routes Definition** | `routes/web.php` | **Register AI routes** (`ai.chat`, `ai.recommendations`, `ai.compare`, enhanced `user.compare`, enhanced `user.recommendations`). |
| **Comparison View** | `resources/views/user/account/compare.blade.php` | **Enhance view** to display comparative matrix, pros/cons, and AI use-cases when products are provided. |
| **Recommendations View** | `resources/views/user/account/recommendations.blade.php` | **Enhance view** to show personalized AI reason badges. |
| **Comprehensive Test Suite** | `tests/Feature/GeminiAiIntegrationTest.php` | **Create feature tests** covering all acceptance criteria, mock fallbacks, and security checks. |
