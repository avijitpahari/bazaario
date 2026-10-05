# Phase 0 Codebase Survey: Modules R4, R5, R6 (Features 16–38)

**Date**: 2026-09-29  
**Agent**: explorer_survey_2  
**Target Repository**: `c:\xampp\htdocs\bazaario`  
**Scope**: 
- **Module R4**: Product Browsing & Filtering Module (Features 16–23)
- **Module R5**: Product Detail & Reputation Module (Features 24–33)
- **Module R6**: Cart & Multi-Seller Operations Module (Features 34–38)

---

## 1. Executive Summary

A comprehensive investigation of the Bazaario codebase reveals that while rich Blade view templates and initial database seeders exist for catalog browsing, product details, and the shopping cart, there are substantial architectural, schema, and functional gaps against the acceptance criteria defined in `ORIGINAL_REQUEST.md`.

### Core Observations
1. **Catalog & Product Routing**: There is currently **no dedicated `ProductController`**. Catalog browsing (`/products`), product detail (`/product/{slug?}`), and category filtering (`/category/{slug?}`) are implemented as raw inline closures in `routes/web.php`.
2. **Client-Side vs Server-Side Logic**: Catalog filtering, search, and pagination are almost entirely executed client-side via Alpine.js on a hard-capped set of 60 cached products (`->take(60)`). True server-side pagination, multi-page deep browsing, fuzzy search on descriptions, and min/max price bounding are missing or only partially realized.
3. **Database Schema Deficits**:
   - `seller_profiles` lacks a `seller_type` column (`enum('Farmer', 'Kirana Store', 'Dark Store', 'Individual')`).
   - `products` lacks a `unit_type` column (`enum('kg', 'dozen', 'bundle', 'litre')`).
   - Proximity coordinates (`latitude`/`longitude`) exist in `addresses`, but `seller_profiles` lacks spatial coordinates or radius metadata for distance filtering.
4. **Static / Mock Template Artifacts**:
   - `user/products/category.blade.php` renders hardcoded mockup products and completely ignores the database `$products` passed to it.
   - `user/products/show.blade.php` contains hardcoded Apple iPhone 15 Pro Max specs, A17 Pro benchmarks, and mock review cards ("Rahul V.", "Priya Sharma"), regardless of which product is selected. Real customer reviews from the database (`reviews` table) are never rendered.
   - There is **no review submission form** on the product detail page.
   - In `ReviewController@store`, validation expects `'body'` instead of the actual DB column and model fillable `'comment'`, leading to discarded review content.
5. **Cart Multi-Seller & Session Gaps**:
   - In `resources/views/user/cart/index.blade.php`, cart items are rendered in a **flat list**, completely omitting seller grouping (Feature 34) and seller-wise subtotals (Feature 37).
   - Cart actions (`POST /cart`, `PUT /cart/{id}`, `DELETE /cart/{id}`) are locked behind `auth:user` middleware; unauthenticated guest users cannot add to cart, and no session-based cart fallback exists.
   - Top nav bars (`nav.blade.php`, `nav-user.blade.php`) read `$cartCount = count(session('cart', []));` while the cart engine stores items in the `carts` database table, causing the navbar cart badge to always display 0.
   - Coupon application (`CartController@applyCoupon`) fails to validate `minimum_order_amount`, ignores `maximum_discount_amount`, ignores `starts_at`, and ignores `usage_limit`.

---

## 2. Feature-by-Feature Detailed Survey (Features 16–38)

---

### Module R4: Product Browsing & Filtering Module (Features 16–23)

#### Feature 16: View All Products page lists catalog items with pagination
- **Acceptance Criteria**: *View All Products page lists catalog items with pagination.*
- **Current Status**: **Partially Implemented (Client-side slicing on capped query)**
- **Code Locations**:
  - Route: `GET /products` (name: `products.index`), `routes/web.php:77-91` (inline closure)
  - Controller: None (inline closure in `routes/web.php`)
  - View: `resources/views/user/products/index.blade.php:1-1070`
  - Models: `App\Models\Product`, `App\Models\Category`
  - Cache Key: `marketplace_products_active` (TTL: 300s)
- **Current Behavior**:
  - The route fetches `\App\Models\Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])->where('status', 'active')->latest()->take(60)->get()`.
  - In `resources/views/user/products/index.blade.php`, products are transformed and JSON-encoded into Alpine.js (`products: {!! json_encode($finalProducts) !!}`).
  - Alpine.js slices the client-side array (`filteredProducts.slice(0, displayLimit)`) starting with `displayLimit = 12`.
  - Clicking "Load More Products" increments `displayLimit += 12`.
- **Gaps & Discrepancies**:
  - No server-side pagination (`LengthAwarePaginator` / `Product::paginate(12)`).
  - Products beyond the first 60 active records can never be reached.
  - No standard URL page query parameter (`?page=2`).
  - No standard numbered pagination links.

---

#### Feature 17: Filter by Category updates catalog results
- **Acceptance Criteria**: *Filter by Category updates catalog results.*
- **Current Status**: **Partially Implemented**
- **Code Locations**:
  - Route 1: `GET /products` (name: `products.index`), `routes/web.php:77-91`
  - Route 2: `GET /category/{slug?}` (name: `category.show`), `routes/web.php:120-143`
  - Views: `resources/views/user/products/index.blade.php:376-386`, `resources/views/user/products/category.blade.php:1-936`
- **Current Behavior**:
  - On `/products`, category pills and filter drawer buttons update Alpine state `selectedCategory`. Alpine computes `filteredProducts` matching `p.category === this.selectedCategory`.
  - Category counts are displayed using `getCategoryCount(cat)`.
  - A separate route `/category/{slug?}` exists in `routes/web.php:120-143` that queries products by `category_id`.
- **Gaps & Discrepancies**:
  - `user/products/category.blade.php` is a static mockup page. It contains hardcoded Electronics cards (MacBook Pro M3, Sony WH-1000XM5, iPhone 15 Pro Max) and **completely ignores** `$products` and `$category` passed from the controller/route!
  - On `/products`, visiting with a URL parameter like `/products?category=artisan-craft` does NOT initialize `selectedCategory` (only `?search=` is read in `initCatalog()`).

---

#### Feature 18: Filter by Price Range filters items within min/max bounds
- **Acceptance Criteria**: *Filter by Price Range filters items within min/max bounds.*
- **Current Status**: **Partially Implemented (Max bound only)**
- **Code Locations**:
  - View: `resources/views/user/products/index.blade.php:656-672` (Filter Drawer)
  - Alpine State: `maxPriceFilter: 1000000`
- **Current Behavior**:
  - Drawer contains a single range slider: `<input type="range" min="500" max="1000000" step="2500" x-model="maxPriceFilter">`.
  - Alpine filter logic checks: `if (p.price > Number(this.maxPriceFilter)) return false;`.
- **Gaps & Discrepancies**:
  - There is **no minimum price bound** (no min price slider, input, or Alpine state).
  - No query string support for `?min_price=X&max_price=Y`.
  - Purely client-side on the 60 pre-loaded items.

---

#### Feature 19: Filter by Seller Rating filters products by minimum rating stars
- **Acceptance Criteria**: *Filter by Seller Rating filters products by minimum rating stars.*
- **Current Status**: **Partially Implemented**
- **Code Locations**:
  - View: `resources/views/user/products/index.blade.php:675-692`
  - Alpine State: `minRating: 0`
- **Current Behavior**:
  - Drawer has filter buttons for `4.0+`, `3.0+`, `2.0+`, and `All Ratings`.
  - Alpine checks: `if (this.minRating > 0 && p.rating < this.minRating) return false;`.
- **Gaps & Discrepancies**:
  - In `resources/views/user/products/index.blade.php:184`, unrated products are artificially given a fallback rating of `4.8` (`(float)($p->average_rating ?: 4.8)`), distorting actual star ratings and preventing filtering of unrated products.
  - No URL query param support (`?min_rating=4`).
  - Purely client-side on the pre-loaded items.

---

#### Feature 20: Filter by Distance/Radius filters items from nearby sellers
- **Acceptance Criteria**: *Filter by Distance/Radius filters items from nearby sellers.*
- **Current Status**: **Missing**
- **Code Locations**:
  - Schema: `database/migrations/2026_09_11_000003_create_seller_profiles_table.php`, `database/migrations/2026_09_11_000006_create_addresses_table.php`
- **Current Behavior**:
  - None. No radius/distance filter control exists anywhere in `resources/views/user/products/index.blade.php`.
- **Gaps & Discrepancies**:
  - `seller_profiles` table has `city`, `state`, `country`, but **NO `latitude` or `longitude`** columns.
  - No spatial calculation (Haversine formula or bounding box query) exists in backend or frontend.
  - No distance slider or radius selector in the catalog interface.

---

#### Feature 21: Search by Keyword performs live fuzzy search on names and descriptions
- **Acceptance Criteria**: *Search by Keyword performs live fuzzy search on names and descriptions.*
- **Current Status**: **Partially Implemented (Substring match on name/seller/cat; descriptions missing)**
- **Code Locations**:
  - View: `resources/views/user/products/index.blade.php:308-317`, `948-967`
  - Alpine State: `searchQuery: ''`
- **Current Behavior**:
  - Input field is bound to `x-model="searchQuery"`.
  - Alpine evaluates:
    ```javascript
    const q = this.searchQuery.toLowerCase();
    const matchTitle = p.name.toLowerCase().includes(q);
    const matchSeller = p.seller.toLowerCase().includes(q);
    const matchCat = p.category.toLowerCase().includes(q);
    if (!matchTitle && !matchSeller && !matchCat) return false;
    ```
- **Gaps & Discrepancies**:
  - **Does NOT search `p.description`** at all.
  - Does NOT perform fuzzy search (only strict exact substring `includes()`; no typo tolerance, Levenshtein, or ngram matching).
  - Only searches within the 60 pre-loaded items in memory; backend database is not queried for keyword search results.

---

#### Feature 22: View Search Results displays matching items with match counts
- **Acceptance Criteria**: *View Search Results displays matching items with match counts.*
- **Current Status**: **Partially Implemented**
- **Code Locations**:
  - View: `resources/views/user/products/index.blade.php:286`, `391-400`, `587-589`, `719`
- **Current Behavior**:
  - Displays `filteredProducts.length + ' items'` in top bar.
  - Displays `Math.min(displayLimit, filteredProducts.length) + ' of ' + filteredProducts.length + ' curated items'` at bottom.
  - Shows "No products match your criteria" with a "Clear All Filters" button when `filteredProducts.length === 0`.
- **Gaps & Discrepancies**:
  - Does not state the searched keyword in search results heading (e.g. "Showing 5 results for 'keyboard'").
  - Counts are limited to client-side filtered subset of the 60 cached records.

---

#### Feature 23: Sort Products orders items by price (asc/desc), rating, or newest
- **Acceptance Criteria**: *Sort Products orders items by price (asc/desc), rating, or newest.*
- **Current Status**: **Partially Implemented (Missing 'Newest' and 'Popularity')**
- **Code Locations**:
  - View: `resources/views/user/products/index.blade.php:333-358`, `962-967`
  - Alpine State: `sortBy: 'featured'`
- **Current Behavior**:
  - Dropdown options: "Featured", "Price: Low to High", "Price: High to Low", "Top Customer Rating".
  - Sorting logic:
    ```javascript
    if (this.sortBy === 'price_low') return a.price - b.price;
    if (this.sortBy === 'price_high') return b.price - a.price;
    if (this.sortBy === 'rating') return b.rating - a.rating;
    ```
- **Gaps & Discrepancies**:
  - **Missing "Newest" option** required by Feature 23 acceptance criteria.
  - Missing "Popularity" option mentioned in Requirement R4.
  - `featured` does not apply any ranking sort (returns 0).

---

### Module R5: Product Detail & Reputation Module (Features 24–33)

#### Feature 24: Product detail page renders full image gallery
- **Acceptance Criteria**: *Product detail page renders full image gallery.*
- **Current Status**: **Partially Implemented (Dual conflicting JS scripts & mock fallbacks)**
- **Code Locations**:
  - Route: `GET /product/{slug?}` (name: `products.show`), `routes/web.php:102-119`
  - View: `resources/views/user/products/show.blade.php:10-43`, `137-190`, `828-858`, `971-998`
  - Models: `Product`, `ProductImage`
- **Current Behavior**:
  - In PHP, images are collected from `$prod->images` and `$prod->main_image_url`, supplemented with `$fallbackImagesPool` to guarantee 4 thumbnails.
  - Renders main image showcase with next/prev cycling arrows and thumbnail buttons.
- **Gaps & Discrepancies**:
  - **Script conflict**: There are two separate implementations of gallery navigation in the same file. In `show.blade.php:828`, a hardcoded `galleryList` array containing Google User Content URLs is used by `cycleGallery()`. Meanwhile, at line 971, `galleryImages` from PHP is passed to a second `selectThumb()` function. This causes arrow navigation to revert to hardcoded external images rather than the product's actual uploaded images.

---

#### Feature 25: Renders complete product description & specifications
- **Acceptance Criteria**: *Renders complete product description & specifications.*
- **Current Status**: **Partially Implemented (Description dynamic; Specifications completely hardcoded mock data)**
- **Code Locations**:
  - View: `resources/views/user/products/show.blade.php:471-564`
  - DB Table: `products` (`description`, `short_description`, `sku`, `weight`, `length`, `width`, `height`, `processing_time_days`, `sale_type`)
- **Current Behavior**:
  - Renders `$prodDescription` (`$prod->description ?: $prod->short_description`).
- **Gaps & Discrepancies**:
  - The "Key Features" (lines 483-506) and "Product Specifications" table (lines 510-564) are **100% hardcoded for an Apple iPhone 15 Pro Max** ("Brand: Apple", "Model: iPhone 15 Pro Max", "Storage: 256 GB", "RAM: 8 GB Unified", "Processor: Apple A17 Pro (3nm)", "Operating System: iOS 17").
  - Whether viewing a handcrafted leather bag, spices, or keyboard, every product displays iPhone specifications.
  - Database fields `sku`, `weight`, `length`, `width`, `height`, `processing_time_days`, `sale_type` are completely omitted from the specifications table.

---

#### Feature 26: Displays Seller Type badge (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`)
- **Acceptance Criteria**: *Displays Seller Type badge (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`).*
- **Current Status**: **Missing**
- **Code Locations**:
  - Migration: `database/migrations/2026_09_11_000003_create_seller_profiles_table.php`
  - Model: `app/Models/SellerProfile.php`
  - View: `resources/views/user/products/show.blade.php:380-394`
- **Current Behavior**:
  - Displays a static hardcoded card: "TechWorld Store", "Trusted Seller ✓".
- **Gaps & Discrepancies**:
  - `seller_profiles` table has **no `seller_type` column**.
  - `SellerProfile` model does not contain `seller_type` in `$fillable` or casts.
  - No badge rendering `Farmer`, `Kirana Store`, `Dark Store`, or `Individual` is present in `show.blade.php`.

---

#### Feature 27: Displays dynamic Price & real-time Stock availability
- **Acceptance Criteria**: *Displays dynamic Price & real-time Stock availability.*
- **Current Status**: **Partially Implemented (Price overwritten by mock variant scripts; out-of-stock not guarded)**
- **Code Locations**:
  - View: `resources/views/user/products/show.blade.php:192-294`
- **Current Behavior**:
  - Initial load renders `$prodPrice` (`$prod->price`) and `$prodStock` (`Only {{ $prodStock }} units left in stock!`).
- **Gaps & Discrepancies**:
  - Lines 241-294 contain hardcoded iPhone storage options ("256 GB", "512 GB", "1 TB") with click handlers (`setStorage()`) that overwrite the displayed price with `₹1,19,999`, `₹1,39,999`, or `₹1,59,999`.
  - When `stock === 0`, the UI does not display an "Out of Stock" badge or disable the "Add to Cart" and "Buy Now" buttons.

---

#### Feature 28: Displays Unit Type (`kg`, `dozen`, `bundle`, `litre`)
- **Acceptance Criteria**: *Displays Unit Type (`kg`, `dozen`, `bundle`, `litre`).*
- **Current Status**: **Missing**
- **Code Locations**:
  - Migration: `database/migrations/2026_09_11_000004_create_products_table.php`
  - Model: `app/Models/Product.php`
  - View: `resources/views/user/products/show.blade.php`
- **Current Behavior**:
  - None.
- **Gaps & Discrepancies**:
  - `products` table has **no `unit_type` column**.
  - `show.blade.php` does not render unit types alongside prices (e.g. `/ kg`, `/ dozen`, `/ bundle`, `/ litre`).

---

#### Feature 29: Displays Seller Trust Score badge
- **Acceptance Criteria**: *Displays Seller Trust Score badge.*
- **Current Status**: **Partially Implemented (Static text in mock card; not dynamic)**
- **Code Locations**:
  - Migration: `database/migrations/2026_09_11_000003_create_seller_profiles_table.php:22` (`trust_score` decimal 5,2)
  - View: `resources/views/user/products/show.blade.php:392`
- **Current Behavior**:
  - Hardcoded string `99.8% Trust Score` inside static "TechWorld Store" card.
- **Gaps & Discrepancies**:
  - Does NOT read dynamic `$prod->seller->sellerProfile->trust_score`.
  - No visual Trust Score badge component (color-coded rating badge, verified badge, or score indicator).

---

#### Feature 30: 'Add to Cart' adds product to session/database cart
- **Acceptance Criteria**: *'Add to Cart' adds product to session/database cart.*
- **Current Status**: **Partially Implemented (Database cart works for authenticated users only; session cart missing)**
- **Code Locations**:
  - Route: `POST /cart` (name: `cart.store`), `routes/web.php:150`
  - Controller: `app/Http/Controllers/User/CartController.php:45-74`
  - View: `resources/views/user/products/show.blade.php:898-936`
- **Current Behavior**:
  - In `show.blade.php`, `submitCartForm(false)` posts `product_id` and `quantity` to `route('cart.store')`.
  - In `CartController@store`, creates or finds `Cart` for `Auth::user()` and increments/creates `CartItem`.
- **Gaps & Discrepancies**:
  - `POST /cart` is guarded by `middleware(['auth:user', 'user'])`. Guest buyers are redirected to login or error, instead of storing items in a session-based cart.
  - On `resources/views/user/products/index.blade.php:1009-1015`, `addToCart()` is merely a fake `setTimeout` toast and does NOT submit to `cart.store`.

---

#### Feature 31: 'Buy Now' adds item and immediately navigates to checkout
- **Acceptance Criteria**: *'Buy Now' adds item and immediately navigates to checkout.*
- **Current Status**: **Partially Implemented (Authenticated users only)**
- **Code Locations**:
  - Route: `POST /cart` (with `buy_now=1`)
  - Controller: `CartController.php:69-71`
  - View: `show.blade.php:898-936`, `index.blade.php:1017-1048`
- **Current Behavior**:
  - Submits form with `buy_now=1` to `cart.store`. If authenticated, `CartController@store` redirects to `route('checkout.index')`.
- **Gaps & Discrepancies**:
  - Guest buyers cannot use "Buy Now" because both `cart.store` and `checkout.index` are protected by `auth:user` middleware without a guest checkout flow.

---

#### Feature 32: View Reviews renders existing customer reviews and ratings
- **Acceptance Criteria**: *View Reviews renders existing customer reviews and ratings.*
- **Current Status**: **Partially Implemented (100% Mock / Hardcoded UI)**
- **Code Locations**:
  - Route: `routes/web.php:104` (eager loads `reviews.user`)
  - View: `resources/views/user/products/show.blade.php:566-700`
  - DB Table: `reviews` (`product_id`, `user_id`, `rating`, `title`, `comment`, `status`)
- **Current Behavior**:
  - The view renders static cards for "Rahul V." and "Priya Sharma", with hardcoded rating distribution bars (5★: 190, 4★: 38, etc.).
- **Gaps & Discrepancies**:
  - `$prod->reviews` is **never iterated over in Blade**.
  - Actual database reviews in `reviews` table are invisible on the product detail page.
  - Star distribution bars and total review count (248) are hardcoded.

---

#### Feature 33: Add Review & Rating form allows authenticated buyers to submit feedback
- **Acceptance Criteria**: *Add Review & Rating form allows authenticated buyers to submit feedback.*
- **Current Status**: **Partially Implemented / Broken**
- **Code Locations**:
  - Route: `POST /user/reviews` (name: `user.reviews.store`), `routes/web.php:216`
  - Controller: `app/Http/Controllers/User/ReviewController.php:20-39`
  - View: `resources/views/user/products/show.blade.php`
- **Current Behavior**:
  - `ReviewController@store` exists and handles review insertion.
- **Gaps & Discrepancies**:
  - **No review submission form exists** on `resources/views/user/products/show.blade.php`.
  - **Field Mismatch**: `ReviewController.php:27` validates `'body' => 'nullable|string|max:2000'`, but the database column in `reviews` table and model fillable is `'comment'`. Any submitted review body is silently ignored by Eloquent!
  - `ReviewController@store` does NOT update the product's `average_rating` or `total_reviews` columns after submission.

---

### Module R6: Cart & Multi-Seller Operations Module (Features 34–38)

#### Feature 34: Cart items are clearly grouped by Seller
- **Acceptance Criteria**: *Cart items are clearly grouped by Seller.*
- **Current Status**: **Missing**
- **Code Locations**:
  - Route: `GET /cart` (name: `cart.index`), `routes/web.php:147`
  - Controller: `app/Http/Controllers/User/CartController.php:15-43`
  - View: `resources/views/user/cart/index.blade.php:25-70`
- **Current Behavior**:
  - `CartController@index` fetches `$cart = $user->cart()->with(['items.product.images'])->first()`.
  - `user/cart/index.blade.php` runs `@foreach($cart->items as $item)` in a single flat list.
- **Gaps & Discrepancies**:
  - Cart items are NOT grouped by seller.
  - No merchant header, seller shop name, seller trust rating, or seller card container.
  - Controller does not eager-load `items.product.seller.sellerProfile` or group items by `product.seller_id`.

---

#### Feature 35: Update Item Quantity updates quantity and subtotals dynamically
- **Acceptance Criteria**: *Update Item Quantity updates quantity and subtotals dynamically.*
- **Current Status**: **Implemented (Page-refresh submit)**
- **Code Locations**:
  - Route: `PUT /cart/{cartItem}` (name: `cart.update`), `routes/web.php:151`
  - Controller: `app/Http/Controllers/User/CartController.php:76-85`
  - View: `resources/views/user/cart/index.blade.php:46-55`
- **Current Behavior**:
  - Form with `-` and `+` buttons submits `PUT /cart/{cartItem}` with `quantity`.
  - `CartController@update` validates quantity (`integer|min:1|max:99`), authorizes user, updates `cart_items.quantity`, and redirects back to `cart.index`.
- **Gaps & Discrepancies**:
  - Does NOT validate quantity against `product.stock` (a user can request 99 items even if stock is only 2).
  - Page reloads rather than dynamically updating via AJAX (functional, but could be enhanced).

---

#### Feature 36: Remove Item removes specific product from cart
- **Acceptance Criteria**: *Remove Item removes specific product from cart.*
- **Current Status**: **Implemented**
- **Code Locations**:
  - Route: `DELETE /cart/{cartItem}` (name: `cart.destroy`), `routes/web.php:152`
  - Controller: `app/Http/Controllers/User/CartController.php:87-93`
  - View: `resources/views/user/cart/index.blade.php:61-66`
- **Current Behavior**:
  - "Remove" button sends `DELETE` request with CSRF token.
  - `CartController@destroy` authorizes user ownership (`$cartItem->cart->user_id === Auth::id()`), executes `$cartItem->delete()`, and redirects with a flash message.
- **Gaps & Discrepancies**:
  - Fully implemented for authenticated users.

---

#### Feature 37: Renders Seller-wise Subtotal breakdown per merchant block
- **Acceptance Criteria**: *Renders Seller-wise Subtotal breakdown per merchant block.*
- **Current Status**: **Missing**
- **Code Locations**:
  - Controller: `app/Http/Controllers/User/CartController.php:15-43`
  - View: `resources/views/user/cart/index.blade.php:72-137`
- **Current Behavior**:
  - The view only contains a single global "Order Summary" card on the right column with global Subtotal, Shipping (₹99), Discount, and Total.
- **Gaps & Discrepancies**:
  - No seller-wise subtotals calculated in `CartController`.
  - No merchant-level breakdown in `user/cart/index.blade.php`.

---

#### Feature 38: Apply Coupon Code validates promo code and deducts discount
- **Acceptance Criteria**: *Apply Coupon Code validates promo code and deducts discount.*
- **Current Status**: **Partially Implemented (Critical coupon validation rules missing)**
- **Code Locations**:
  - Routes: `POST /cart/coupon` (`cart.coupon`), `DELETE /cart/coupon/remove` (`cart.coupon.remove`), `routes/web.php:153-154`
  - Controller: `app/Http/Controllers/User/CartController.php:95-119`, `29-37`
  - Model: `app/Models/Coupon.php`
  - DB Table: `coupons` (`code`, `discount_type`, `discount_value`, `minimum_order_amount`, `maximum_discount_amount`, `usage_limit`, `used_count`, `starts_at`, `expires_at`, `status`)
  - View: `resources/views/user/cart/index.blade.php:99-124`
- **Current Behavior**:
  - Form allows submitting `coupon_code`.
  - `CartController@applyCoupon` validates code presence, checks `status == 'active'` and `expires_at > now()`, and stores `['code' => $coupon->code, 'id' => $coupon->id]` in `session('coupon')`.
  - In `CartController@index`, calculates percentage or fixed discount, capping at subtotal.
  - Coupon removal deletes `session('coupon')`.
- **Gaps & Discrepancies**:
  - **Does NOT check `minimum_order_amount`**: E.g., `BAZAARIO10` requires ₹1500 minimum spend, but is successfully applied to a ₹200 cart!
  - **Does NOT enforce `maximum_discount_amount`**: E.g., `BAZAARIO10` has a max discount of ₹1000. If cart is ₹50,000, it gives ₹5,000 discount instead of ₹1000!
  - **Does NOT check `starts_at`**: Future-dated coupons can be redeemed prematurely.
  - **Does NOT check `usage_limit` vs `used_count`**: Exhausted coupons can still be applied.
  - Wrapped in `@auth` in Blade; guest users cannot test or apply promo codes.

---

## 3. Cross-Cutting Architecture, Discrepancies & Missing Elements

| Domain | Issue / Gap | Source Location | Impact |
|---|---|---|---|
| **Routing / Architecture** | Missing `ProductController` | `routes/web.php:77-143` | Business logic for browsing, detail, and categories is placed directly in route closures; impossible to cleanly unit-test or reuse. |
| **Catalog Pagination** | Query hard-capped at 60 items with client slicing | `routes/web.php:82`, `user/products/index.blade.php:921` | Deep catalog navigation impossible; no standard `?page=X` support. |
| **Category View** | `category.blade.php` is static mock | `resources/views/user/products/category.blade.php` | Category routes `/category/{slug}` render static iPhone/MacBook mockup regardless of category clicked. |
| **Search Engine** | Search excludes descriptions; no fuzzy matching | `resources/views/user/products/index.blade.php:950-956` | Only checks titles/categories/sellers in memory; fails acceptance criteria requiring description & fuzzy search. |
| **Distance Filter** | Missing radius filter & missing coordinates | `seller_profiles` table, `user/products/index.blade.php` | Proximity filtering (Feature 20) is completely absent. `seller_profiles` needs lat/long or distance relation. |
| **Seller Types** | Missing `seller_type` column | `seller_profiles` table, `app/Models/SellerProfile.php` | Cannot display Seller Type badges (`Farmer`, `Kirana Store`, `Dark Store`, `Individual`). |
| **Unit Types** | Missing `unit_type` column | `products` table, `app/Models/Product.php` | Cannot display Unit Type (`kg`, `dozen`, `bundle`, `litre`). |
| **Product Detail Specs** | Hardcoded Apple iPhone specs | `resources/views/user/products/show.blade.php:510-564` | Every product in the marketplace displays Apple A17 Pro specs. Real DB specs (`weight`, `dimensions`, `sku`) are ignored. |
| **Reviews Display** | Hardcoded mock reviews; DB reviews ignored | `resources/views/user/products/show.blade.php:566-700` | Real customer feedback from DB is never rendered on the product detail page. |
| **Reviews Submission** | Missing form on detail page & attribute mismatch | `show.blade.php`, `ReviewController.php:27` | No form exists on product page. `ReviewController` validates `'body'` while DB column is `'comment'`, dropping feedback text. |
| **Cart Seller Grouping** | Flat cart list; no seller headers | `resources/views/user/cart/index.blade.php:25-70` | Acceptance criteria Feature 34 and 37 are not met. |
| **Cart Navbar Count** | Badge reads `session('cart')` while items are in DB table | `components/nav.blade.php:21`, `components/nav-user.blade.php:21` | Navbar cart badge permanently displays 0 even when cart has items. |
| **Coupon Validation** | Missing min order, max discount, and usage limit checks | `CartController.php:95-119`, `29-37` | Financial vulnerability: coupons can be exploited below minimum spend and above maximum discount thresholds. |

---

## 4. Summary Matrix (Features 16–38)

| Feature # | Feature Title | Acceptance Criteria Met? | Status | Priority Remediation |
|---|---|---|---|---|
| **16** | View All Products Page | Partial | Needs server-side pagination | Replace `take(60)` closure with `ProductController@index` using `paginate(12)` |
| **17** | Filter by Category | Partial | Works on index; broken on category page | Make `user/products/category.blade.php` dynamic; parse `?category=` on index |
| **18** | Filter by Price Range | Partial | Max slider only; min bound missing | Add min price input/slider and backend query binding |
| **19** | Filter by Seller Rating | Partial | Filter buttons present; unrated fallback skewed | Bind real `average_rating` without 4.8 artificial fallback; add query support |
| **20** | Filter by Distance / Radius | No | Missing entirely | Add distance slider; compute proximity via seller location/coordinates |
| **21** | Search by Keyword | Partial | Substring name search only; description missing | Include `description` in search; support server-side or fuzzy matching |
| **22** | View Search Results | Partial | Match count present; query label missing | Add search query feedback text and match count banner |
| **23** | Sort Products | Partial | Missing Newest and Popularity | Add `newest` (latest) and `popularity` sorting options to dropdown and query |
| **24** | Multi-Image Gallery | Partial | Dual conflicting JS; mock fallback | Unify gallery script to use dynamic product image URLs |
| **25** | Description & Specs | Partial | Specs are hardcoded iPhone data | Render dynamic specifications (`sku`, `weight`, `dimensions`, `processing_time`) |
| **26** | Seller Type Badge | No | Missing column & badge | Add `seller_type` column to `seller_profiles` & render badge on product page |
| **27** | Dynamic Price & Stock | Partial | Mock variant buttons overwrite price | Remove hardcoded iPhone storage options; disable CTA on stock = 0 |
| **28** | Unit Type Display | No | Missing column & badge | Add `unit_type` column to `products` & display on detail page |
| **29** | Seller Trust Score Badge | Partial | Static text in mock card | Read dynamically from `sellerProfile.trust_score` and display badge |
| **30** | Add to Cart | Partial | Auth database cart only; guest fails | Enable session cart fallback or graceful auth prompt; fix index card CTA |
| **31** | Buy Now | Partial | Auth only | Ensure direct redirect to checkout for authenticated buyers |
| **32** | View Reviews | Partial | Hardcoded mock reviews | Render dynamic reviews from `$product->reviews` |
| **33** | Add Review & Rating Form | No / Broken | No form on page; `'body'` vs `'comment'` bug | Add review form to product page; fix `ReviewController` to use `'comment'` and update ratings |
| **34** | Seller-Grouped Cart | No | Flat list in cart view | Group cart items by seller in controller and template |
| **35** | Update Item Quantity | Yes | Works via PUT redirect | Add stock boundary check |
| **36** | Remove Item | Yes | Fully functional | None |
| **37** | Seller-wise Subtotals | No | Missing entirely | Calculate and render merchant block subtotals in cart |
| **38** | Apply Promo Coupon Code | Partial | Ignores min spend, max discount, usage | Enforce `minimum_order_amount`, `maximum_discount_amount`, and `usage_limit` |

---
*Report prepared for orchestrator handoff by explorer_survey_2.*
