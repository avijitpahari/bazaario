<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NvidiaApiService
{
    /**
     * Resolve the NVIDIA API Key following configuration hierarchy:
     * 1. SiteSetting::get('nvidia_api_key')
     * 2. config('services.nvidia.api_key')
     * 3. env('NVIDIA_API_KEY')
     * 4. Hardcoded provisioned key
     */
    public function resolveApiKey(): string
    {
        $dbKey = SiteSetting::get('nvidia_api_key');
        if (!empty($dbKey) && is_string($dbKey) && trim($dbKey) !== '') {
            return trim($dbKey);
        }

        $configKey = config('services.nvidia.api_key');
        if (!empty($configKey) && is_string($configKey) && trim($configKey) !== '') {
            return trim($configKey);
        }

        $envKey = env('NVIDIA_API_KEY');
        if (!empty($envKey) && is_string($envKey) && trim($envKey) !== '') {
            return trim($envKey);
        }

        return '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';
    }

    /**
     * Resolve active NVIDIA Model.
     */
    public function resolveModel(): string
    {
        $dbModel = SiteSetting::get('nvidia_model');
        if (!empty($dbModel) && is_string($dbModel) && trim($dbModel) !== '') {
            return trim($dbModel);
        }

        return config('services.nvidia.model') ?: env('NVIDIA_MODEL', 'nvidia/nemotron-3.5-lightning-30b-a3b');
    }

    /**
     * Resolve API Base URL.
     */
    public function resolveBaseUrl(): string
    {
        return config('services.nvidia.base_url') ?: env('NVIDIA_BASE_URL', 'https://integrate.api.nvidia.com/v1');
    }

    /**
     * Primary chat auto-reply execution with real-time context injection.
     *
     * @param string $prompt
     * @param array<int, array<string, string>> $history
     * @param User|null $user
     * @param int|null $conversationId
     * @return array{success: bool, reply: string, source: string, model: string}
     */
    public function chat(string $prompt, array $history = [], ?User $user = null, ?int $conversationId = null): array
    {
        $apiKey = $this->resolveApiKey();
        $model = $this->resolveModel();
        $baseUrl = $this->resolveBaseUrl();

        $toolsData = $this->gatherContext($prompt, $user);

        try {
            $systemInstruction = "You are 'Bazaario AI Support', the official intelligent customer care assistant for Bazaario Marketplace.
You help shoppers and merchants with live order tracking, 7-day returns, fresh farm produce guarantees, 100% Escrow Buyer Protection, wholesale spot floor bidding, and store inquiries.
Bazaario Core Policies:
- Perishables (farm produce, fruits): 48-Hour Freshness Guarantee with instant photo-based escrow refund.
- Standard items (crafts, pantry goods): 7-Day return window with free doorstep courier inspection & pickup.
- Escrow Vault: Buyer funds remain 100% protected in an APMC-grade escrow vault until delivery is inspected and verified. Merchants are never paid prematurely.
- Wholesale Auctions: Feature opening floor prices, secret reserve prices, and an automated Anti-Snipe Guard (+2 minute soft-close extension).
- Order Tracking: Hyperlocal delivery slots (e.g. Morning 8 AM – 12 PM) with real-time courier milestone telemetry.
Formatting Guidelines:
- Keep answers polite, warm, concise, and structured in clean markdown (use **bold** and [links](/path)).
- Use the provided DATABASE_CONTEXT as authoritative real-time truth.";

            $messages = [
                ['role' => 'system', 'content' => $systemInstruction . "\n\nDATABASE_CONTEXT:\n" . json_encode($toolsData, JSON_PRETTY_PRINT)],
            ];

            // Add previous conversation history
            if (!empty($history)) {
                foreach (array_slice($history, -4) as $h) {
                    $role = ($h['role'] === 'user') ? 'user' : 'assistant';
                    if (!empty($h['content'])) {
                        $messages[] = ['role' => $role, 'content' => $h['content']];
                    }
                }
            }

            // Current user prompt
            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(18)->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.6,
                'max_tokens' => 1024,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? null;

                // For reasoning models, if content is empty, extract from reasoning_content
                if (empty($reply) && !empty($data['choices'][0]['message']['reasoning_content'])) {
                    $reply = $this->extractCleanAnswerFromReasoning($data['choices'][0]['message']['reasoning_content']);
                }

                if (!empty($reply) && is_string($reply)) {
                    $cleanReply = trim($reply);
                    return [
                        'success' => true,
                        'reply' => $cleanReply,
                        'source' => 'nvidia',
                        'model' => $model,
                    ];
                }
            } else {
                Log::warning('NVIDIA API Error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::warning('NVIDIA API Exception: ' . $e->getMessage());
        }

        // Fallback to intelligent deterministic FAQ auto-reply engine
        $fallbackReply = $this->buildFallbackReply($prompt, $toolsData, $user);

        return [
            'success' => true,
            'reply' => $fallbackReply,
            'source' => 'local_fallback',
            'model' => 'deterministic_fallback',
        ];
    }

    /**
     * Gather real-time database context based on prompt keywords.
     */
    protected function gatherContext(string $prompt, ?User $user): array
    {
        $context = [];
        $lower = strtolower($prompt);

        // 1. Order tracking context
        if (str_contains($lower, 'order') || str_contains($lower, 'track') || str_contains($lower, 'delivery') || str_contains($lower, 'status') || preg_match('/ord-\w+/i', $prompt)) {
            if ($user) {
                $recentOrder = Order::where('user_id', $user->id)->latest()->first();
                if ($recentOrder) {
                    $context['user_recent_order'] = [
                        'order_number' => $recentOrder->order_number,
                        'status' => $recentOrder->order_status,
                        'total' => '₹' . number_format($recentOrder->total_amount, 2),
                        'payment_status' => $recentOrder->payment_status,
                        'delivery_slot' => 'Morning 8 AM - 12 PM',
                    ];
                }
            }
        }

        // 2. Returns context
        if (str_contains($lower, 'return') || str_contains($lower, 'refund') || str_contains($lower, 'cancel') || str_contains($lower, 'damaged')) {
            $context['policies'] = [
                'general_goods_return_window' => '7 days with free courier doorstep pickup',
                'fresh_produce_guarantee' => '48-hour freshness guarantee with photo-based escrow refund',
                'escrow_refund_time' => 'Processed within 24 hours back to original payment method',
            ];
        }

        // 3. Auctions context
        if (str_contains($lower, 'auction') || str_contains($lower, 'bid') || str_contains($lower, 'hammer')) {
            $liveCount = Auction::where('status', 'active')->count();
            $context['auctions'] = [
                'active_lots' => $liveCount,
                'anti_snipe_rule' => 'Bids in last 2 minutes add +2 minutes extension',
                'escrow_payment_window' => '24 hours post hammer fall',
            ];
        }

        return $context;
    }

    /**
     * Fallback reply when network or quota limits are reached.
     */
    protected function buildFallbackReply(string $prompt, array $toolsData, ?User $user): string
    {
        $lower = strtolower($prompt);

        if (str_contains($lower, 'return') || str_contains($lower, 'refund')) {
            return "🔄 **Bazaario 7-Day Return & Escrow Refund Policy**\n\n• **General Goods**: 7-day return window with doorstep courier pickup.\n• **Perishable Farm Harvest**: 48-Hour Freshness Guarantee with instant photo-based reimbursement.\n• **Escrow Refund**: Credited within 24 hours to your original payment method.\n\nYou can initiate returns in the [Returns Portal](" . route('user.returns') . ") or [Open a Support Ticket](" . route('user.support.tickets.create') . ").";
        }

        if (str_contains($lower, 'track') || str_contains($lower, 'order') || str_contains($lower, 'delivery')) {
            if (!empty($toolsData['user_recent_order'])) {
                $ord = $toolsData['user_recent_order'];
                return "📦 **Order Telemetry for #{$ord['order_number']}**:\n\n• **Status**: " . ucfirst($ord['status']) . "\n• **Total**: {$ord['total']}\n• **Delivery Slot**: {$ord['delivery_slot']}\n\nTrack full courier milestones in your [Orders Portal](" . route('user.orders.index') . ").";
            }
            return "📦 **Order Tracking**: You can view real-time courier milestone telemetry and delivery slots in your [Orders Portal](" . route('user.orders.index') . ").";
        }

        if (str_contains($lower, 'escrow') || str_contains($lower, 'payment') || str_contains($lower, 'safe')) {
            return "🛡️ **100% Escrow Buyer Protection**: All payments are safely held in an APMC-grade Escrow Vault. Merchants are never paid until you receive and verify your consignment. Zero hidden fees.";
        }

        if (str_contains($lower, 'auction') || str_contains($lower, 'bid')) {
            return "🔨 **Wholesale Spot Auctions**: Farmers list harvest lots on live bidding floors. All lots feature secret reserve prices and Anti-Snipe (+2m soft close). Check [Live Auctions](" . route('auctions.index') . ").";
        }

        return "Namaskar! I am Bazaario AI Support. How can I assist you with order tracking, 7-day returns, fresh produce guarantees, escrow buyer protection, or live wholesale auctions today?";
    }

    /**
     * Extract clean assistant reply from reasoning text if needed.
     */
    protected function extractCleanAnswerFromReasoning(string $reasoning): string
    {
        // Check if there is an explicit final answer section
        if (preg_match('/(?:Final Answer|Response|Output):\s*(.+)$/is', $reasoning, $m)) {
            return trim($m[1]);
        }

        // Return a condensed summary
        return "Thanks for contacting Bazaario Support! We have processed your inquiry. Please let us know if you need further details on orders, returns, or escrow protection.";
    }
}
