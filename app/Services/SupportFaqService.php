<?php

namespace App\Services;

class SupportFaqService
{
    /**
     * Get all categorized Quick FAQ definitions with auto-replies.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 'track_order',
                'category' => 'orders',
                'category_label' => 'Orders & Tracking',
                'icon' => 'local_shipping',
                'question' => 'How do I track my active order and delivery slot in real time?',
                'summary' => 'Every order features live Hyperlocal carrier telemetry and status updates from sealing to delivery.',
                'answer' => "📦 **Real-Time Order & Delivery Telemetry**\n\n• **Milestone Progression**: Track Pending Placed, Order Confirmed, Packed & Sealed, Ready for Pickup, Courier Handover, and Delivered to Customer.\n• **Hyperlocal Scheduling**: Farm-direct shipments have scheduled time slots (e.g. Morning 8 AM – 12 PM) with vehicle assignment.\n• **Direct Access**: Inspect real-time GPS telemetry and timestamps anytime in your [Orders Portal](" . route('user.orders.index') . ").",
                'action_url' => route('user.orders.index'),
                'action_label' => 'Go to Orders Portal',
                'chat_action' => 'track_order',
                'tags' => ['track', 'order', 'delivery', 'status', 'shipping', 'courier', 'gps', 'where is my order', 'slot'],
            ],
            [
                'id' => 'delivery_delay',
                'category' => 'orders',
                'category_label' => 'Orders & Tracking',
                'icon' => 'schedule',
                'question' => 'What happens if my delivery is delayed beyond the estimated slot?',
                'summary' => 'Our automated carrier monitoring alerts dispatch desks for instant carrier check & coordination.',
                'answer' => "⏱️ **Delivery Delay & SLA Resolution**\n\n• **Automatic Escalation**: If a consignment exceeds its scheduled delivery window, our dispatch telemetry alerts the regional carrier coordinator.\n• **Courier Telemetry**: Hyperlocal drivers coordinate directly through the merchant dispatch rail.\n• **Priority Support**: You can [Open a Ticket](" . route('user.support.tickets.create', ['category' => 'delivery_delay']) . ") or ask our Live Chat bot for an immediate delivery status verification.",
                'action_url' => route('user.support.tickets.create', ['category' => 'delivery_delay']),
                'action_label' => 'Report Delay via Ticket',
                'chat_action' => 'delivery_delay',
                'tags' => ['delay', 'late', 'time', 'transit', 'slow', 'package delayed', 'missing package'],
            ],
            [
                'id' => 'faq_returns',
                'category' => 'returns',
                'category_label' => 'Returns & Refunds',
                'icon' => 'keyboard_return',
                'question' => 'How do returns, doorstep inspections, and escrow refunds work?',
                'summary' => '7-day returns on artisanal & packaged goods, with 100% escrow refunds credited within 24 hours.',
                'answer' => "🔄 **Bazaario 7-Day Return & Escrow Refund Policy**\n\n• **Standard Goods**: Artisanal crafts, handlooms, and packaged pantry items have a full **7-day return window**.\n• **Doorstep Pickup**: Free return pickup is scheduled directly from your delivery address.\n• **Escrow Safeguard**: Your payment is safely locked in Escrow until delivery and inspection are finalized.\n• **Instant 24h Refund**: Approved refunds transfer back to UPI, Bank Account, or Card within 24 hours.\n\n👉 [Submit a Return Request](" . route('user.returns') . ") or [Create a Ticket](" . route('user.support.tickets.create', ['category' => 'return_refund']) . ").",
                'action_url' => route('user.returns'),
                'action_label' => 'Returns & Refunds Portal',
                'chat_action' => 'faq_returns',
                'tags' => ['return', 'refund', 'money back', 'cancel', 'damaged', 'exchange', 'policy', 'reimbursement'],
            ],
            [
                'id' => 'faq_perishables',
                'category' => 'returns',
                'category_label' => 'Returns & Refunds',
                'icon' => 'eco',
                'question' => 'Can I return fresh farm produce or perishable items?',
                'summary' => 'Protected by our 48-Hour Freshness Guarantee with instant photo-based reimbursement.',
                'answer' => "🌿 **48-Hour Fresh Produce Freshness Guarantee**\n\n• **Direct Farm Harvest**: Perishables (mangoes, apples, fresh vegetables) are harvested direct from orchards.\n• **Freshness Coverage**: If any produce arrives damaged, overripe, or bruised during transit, report within 48 hours.\n• **Quick Photo Proof**: Attach an unboxing photo in a [Support Ticket](" . route('user.support.tickets.create', ['category' => 'return_refund']) . ") for immediate escrow credit without requiring physical return of spoiled produce.",
                'action_url' => route('user.support.tickets.create', ['category' => 'return_refund']),
                'action_label' => 'Report Damaged Produce',
                'chat_action' => 'faq_perishables',
                'tags' => ['produce', 'fruit', 'vegetable', 'fresh', 'spoiled', 'rotten', 'freshness', 'perishable'],
            ],
            [
                'id' => 'faq_escrow',
                'category' => 'payments',
                'category_label' => 'Escrow & Payments',
                'icon' => 'verified_user',
                'question' => 'How does 100% Escrow Buyer Protection safeguard my funds?',
                'summary' => 'Payments are held securely in escrow and never released to sellers until delivery is approved.',
                'answer' => "🛡️ **100% APMC-Grade Escrow Buyer Vault**\n\n• **Zero Merchant Lock-in**: When you pay, funds enter an escrow vault managed by Bazaario.\n• **Inspection Period**: Merchants are not paid until your package is marked as Delivered and verified.\n• **Dispute Safeguard**: If an item is missing or contested, funds remain frozen in escrow until resolved.\n• **100% Protection**: Buyers never bear the risk of merchant non-fulfillment.",
                'action_url' => route('user.support.index'),
                'action_label' => 'Learn About Escrow',
                'chat_action' => 'faq_escrow',
                'tags' => ['escrow', 'protection', 'safe', 'security', 'vault', 'buyer protection', 'trust'],
            ],
            [
                'id' => 'faq_payments',
                'category' => 'payments',
                'category_label' => 'Escrow & Payments',
                'icon' => 'credit_card',
                'question' => 'What payment methods are supported and are there any extra fees?',
                'summary' => 'UPI, Debit/Credit Cards, NetBanking, and COD with 0% extra surcharges.',
                'answer' => "💳 **Accepted Payment Methods**\n\n• **Instant UPI**: Google Pay, PhonePe, Paytm, BHIM, Cred, and all UPI apps.\n• **Cards & NetBanking**: Visa, MasterCard, RuPay, and 50+ Indian banks.\n• **Cash on Delivery (COD)**: Available on select regional pin codes.\n• **Zero Surcharges**: Bazaario charges ₹0 platform fee or payment gateway surcharge to shoppers.",
                'action_url' => route('user.orders.index'),
                'action_label' => 'View Payment History',
                'chat_action' => 'faq_payments',
                'tags' => ['payment', 'upi', 'card', 'cod', 'cash on delivery', 'fees', 'methods', 'charge'],
            ],
            [
                'id' => 'faq_auctions',
                'category' => 'auctions',
                'category_label' => 'Wholesale Auctions',
                'icon' => 'gavel',
                'question' => 'How do Live Wholesale Spot Floor Auctions work?',
                'summary' => 'Live bidding on bulk agricultural & artisan lots direct from verified producers.',
                'answer' => "🔨 **Wholesale Spot Floor Auctions**\n\n• **Producer Bidding**: Farm syndicates list harvest lots with transparent floor prices and APMC quality grading.\n• **Bidding Mechanism**: Real-time websocket live bidding with dynamic bid increments.\n• **Reserve Protection**: A secret reserve price ensures ethical minimum compensation for farmers.\n• **Winning & Checkout**: When the hammer drops, the winner has 24 hours to checkout via escrow.\n\n👉 [Enter Live Auctions Floor](" . route('auctions.index') . ").",
                'action_url' => route('auctions.index'),
                'action_label' => 'Browse Live Auctions',
                'chat_action' => 'faq_auctions',
                'tags' => ['auction', 'bid', 'bidding', 'wholesale', 'floor', 'reserve', 'hammer', 'apmc'],
            ],
            [
                'id' => 'faq_anti_snipe',
                'category' => 'auctions',
                'category_label' => 'Wholesale Auctions',
                'icon' => 'timer',
                'question' => 'What is the Anti-Snipe Guard on live auctions?',
                'summary' => 'Any bid in the final 2 minutes triggers a +2 minute soft-close extension.',
                'answer' => "⚡ **Anti-Snipe Soft Close Extension**\n\n• **Fair Play Guarantee**: To prevent automated bot sniping in the last seconds of an auction lot, Bazaario enforces an automated **Anti-Snipe Guard**.\n• **Extension Window**: If any bid is received within 2 minutes of the scheduled closing time, the clock resets by **+2 minutes**.\n• **Final Hammer**: The auction only concludes when 2 uninterrupted minutes pass without any higher bid.",
                'action_url' => route('auctions.index'),
                'action_label' => 'See Active Floors',
                'chat_action' => 'faq_anti_snipe',
                'tags' => ['snipe', 'anti-snipe', 'timer', 'extension', 'soft close', 'last minute bid'],
            ],
            [
                'id' => 'create_ticket',
                'category' => 'support',
                'category_label' => 'Support & Disputes',
                'icon' => 'confirmation_number',
                'question' => 'When should I create a Support Ticket vs using Live Chat?',
                'summary' => 'Live Chat is for instant queries; Support Tickets are for formal order disputes and returns.',
                'answer' => "🎫 **Support Tickets vs. Live Chat**\n\n• **Floating Live Chat Widget**: Best for instant auto-replies, FAQs, order status lookups, and short questions.\n• **Formal Support Ticket**: Best for order damages, formal dispute escalation, linking specific orders, or attaching inspection photos.\n• **Merchant Collaboration**: Tickets directly bridge communication between you, the merchant, and Bazaario support specialists.\n\n👉 [Create a New Ticket](" . route('user.support.tickets.create') . ").",
                'action_url' => route('user.support.tickets.create'),
                'action_label' => 'Open Support Ticket',
                'chat_action' => 'create_ticket',
                'tags' => ['ticket', 'chat', 'difference', 'dispute', 'complaint', 'help', 'contact agent'],
            ],
            [
                'id' => 'faq_arbitration',
                'category' => 'support',
                'category_label' => 'Support & Disputes',
                'icon' => 'balance',
                'question' => 'How does Executive Admin Dispute Arbitration work if a seller does not cooperate?',
                'summary' => 'Unresolved seller issues are reviewed by our dispute council with guaranteed escrow resolution.',
                'answer' => "⚖️ **Executive Admin Dispute Arbitration**\n\n• **Merchant SLA**: Merchants have 48 hours to address active inquiry tickets.\n• **1-Click Escalation**: If you cannot reach an agreement or the merchant rejects a legitimate claim, click **\"Escalate Dispute\"** in your ticket.\n• **Arbitration Council**: Bazaario's Executive Dispute Council reviews transaction logs, courier GPS data, and uploaded unboxing photos.\n• **Final Ruling**: The council has authority to override merchant decisions and release full escrow refunds.",
                'action_url' => route('user.support.index'),
                'action_label' => 'Review My Inquiries',
                'chat_action' => 'faq_arbitration',
                'tags' => ['arbitration', 'escalate', 'admin', 'dispute', 'seller refusal', 'override'],
            ],
        ];
    }

    /**
     * Search FAQs by keyword or category.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function search(string $query = '', string $category = ''): array
    {
        $faqs = self::all();
        $query = strtolower(trim($query));

        return array_values(array_filter($faqs, function ($faq) use ($query, $category) {
            if (!empty($category) && $category !== 'all' && $faq['category'] !== $category) {
                return false;
            }

            if (empty($query)) {
                return true;
            }

            if (str_contains(strtolower($faq['question']), $query) || str_contains(strtolower($faq['summary']), $query) || str_contains(strtolower($faq['answer']), $query)) {
                return true;
            }

            foreach ($faq['tags'] as $tag) {
                if (str_contains(strtolower($tag), $query)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * Get a specific FAQ by ID.
     *
     * @return array<string, mixed>|null
     */
    public static function find(string $id): ?array
    {
        foreach (self::all() as $faq) {
            if ($faq['id'] === $id) {
                return $faq;
            }
        }
        return null;
    }
}
