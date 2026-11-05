<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerSupportTest extends TestCase
{
    use RefreshDatabase;

    protected User $customerA;
    protected User $customerB;
    protected User $sellerA;
    protected User $sellerB;
    protected SellerProfile $profileA;
    protected SellerProfile $profileB;
    protected Order $orderA;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->customerA = User::create([
            'name' => 'Alice Customer',
            'email' => 'alice@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->customerB = User::create([
            'name' => 'Bob Customer',
            'email' => 'bob@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->sellerA = User::create([
            'name' => 'Merchant Alpha',
            'email' => 'merchant.alpha@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $this->profileA = SellerProfile::create([
            'user_id' => $this->sellerA->id,
            'shop_name' => 'Alpha Handlooms',
            'shop_slug' => 'alpha-handlooms',
            'seller_type' => 'artisan',
            'status' => 'approved',
            'commission_rate' => 8.5,
        ]);

        $this->sellerB = User::create([
            'name' => 'Merchant Beta',
            'email' => 'merchant.beta@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $this->profileB = SellerProfile::create([
            'user_id' => $this->sellerB->id,
            'shop_name' => 'Beta Pottery',
            'shop_slug' => 'beta-pottery',
            'seller_type' => 'artisan',
            'status' => 'approved',
            'commission_rate' => 8.5,
        ]);

        $this->orderA = Order::create([
            'order_number' => 'BZ-ORD-101',
            'user_id' => $this->customerA->id,
            'order_type' => 'cart',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 1500.0,
            'total_amount' => 1500.0,
            'delivery_full_name' => 'Alice Customer',
            'delivery_phone' => '9876543210',
            'delivery_address_line_1' => 'Park Circus 12',
            'delivery_postal_code' => '700017',
            'delivery_state' => 'West Bengal',
            'delivery_city' => 'Kolkata',
            'delivery_country' => 'India',
        ]);

        $sellerOrder = SellerOrder::create([
            'order_id' => $this->orderA->id,
            'seller_id' => $this->sellerA->id,
            'seller_order_number' => 'SOA-101',
            'subtotal' => 1500.0,
            'commission_rate' => 8.5,
            'commission_amount' => 127.5,
            'payout_amount' => 1372.5,
            'status' => 'delivered',
        ]);
    }

    public function test_guest_cannot_access_user_support_hub(): void
    {
        $response = $this->get(route('user.support.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_seller_support_desk(): void
    {
        $response = $this->get(route('seller.support.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_view_support_hub(): void
    {
        $response = $this->actingAs($this->customerA, 'user')->get(route('user.support.index'));

        $response->assertStatus(200);
        $response->assertSee('How can we assist you today?');
        $response->assertSee('Open Support Ticket');
        $response->assertSee('Start Live Chat');
    }

    public function test_customer_can_open_support_ticket_with_order_linking(): void
    {
        $fakeFile = UploadedFile::fake()->image('broken_item.jpg');

        $response = $this->actingAs($this->customerA, 'user')->post(route('user.support.tickets.store'), [
            'subject' => 'Damaged ceramic item in package',
            'category' => 'order_issue',
            'priority' => 'high',
            'order_id' => $this->orderA->id,
            'message' => 'The item was cracked during transit. Please check the attached photo.',
            'attachment' => $fakeFile,
        ]);

        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'order_id' => $this->orderA->id,
            'subject' => 'Damaged ceramic item in package',
            'priority' => 'high',
            'status' => 'open',
        ]);

        $ticket = SupportTicket::where('subject', 'Damaged ceramic item in package')->first();
        $this->assertNotNull($ticket);
        $this->assertStringStartsWith('TIC-', $ticket->ticket_number);

        $response->assertRedirect(route('user.support.tickets.show', $ticket));

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->customerA->id,
            'sender_type' => 'customer',
            'message' => 'The item was cracked during transit. Please check the attached photo.',
        ]);
    }

    public function test_customer_can_reply_to_ticket(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'category' => 'order_issue',
            'subject' => 'Missing package item',
            'priority' => 'medium',
            'status' => 'waiting_customer',
        ]);

        $response = $this->actingAs($this->customerA, 'user')->post(route('user.support.tickets.reply', $ticket), [
            'message' => 'I have verified with the courier driver as well.',
        ]);

        $response->assertRedirect(route('user.support.tickets.show', $ticket));

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->customerA->id,
            'sender_type' => 'customer',
            'message' => 'I have verified with the courier driver as well.',
        ]);

        $this->assertEquals('in_progress', $ticket->fresh()->status);
    }

    public function test_customer_can_escalate_ticket_to_dispute(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'category' => 'order_issue',
            'subject' => 'Unresponsive merchant',
            'priority' => 'high',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->customerA, 'user')->post(route('user.support.tickets.escalate', $ticket), [
            'escalation_reason' => 'Vendor refused to replace broken item after 3 days of waiting.',
        ]);

        $response->assertRedirect(route('user.support.tickets.show', $ticket));

        $freshTicket = $ticket->fresh();
        $this->assertEquals('escalated', $freshTicket->status);
        $this->assertTrue($freshTicket->is_escalated);
        $this->assertEquals('Vendor refused to replace broken item after 3 days of waiting.', $freshTicket->escalation_reason);

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_type' => 'system',
        ]);
    }

    public function test_customer_can_mark_ticket_as_resolved(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'category' => 'general',
            'subject' => 'Sizing question',
            'priority' => 'low',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->customerA, 'user')->post(route('user.support.tickets.close', $ticket));

        $response->assertRedirect(route('user.support.tickets.show', $ticket));
        $this->assertEquals('resolved', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);
    }

    public function test_customer_cannot_view_another_customers_ticket(): void
    {
        $ticketA = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'category' => 'order_issue',
            'subject' => 'Private inquiry from Alice',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        // Customer B tries to view Alice's ticket
        $response = $this->actingAs($this->customerB, 'user')->get(route('user.support.tickets.show', $ticketA));
        $response->assertStatus(403);
    }

    public function test_seller_can_view_inquiries_for_their_store(): void
    {
        $ticketA = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'order_id' => $this->orderA->id,
            'category' => 'order_issue',
            'subject' => 'Inquiry for Alpha Handlooms',
            'priority' => 'high',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->sellerA, 'seller')->get(route('seller.support.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer Inquiries & Support');
        $response->assertSee('Inquiry for Alpha Handlooms');
        $response->assertSee($ticketA->ticket_number);
    }

    public function test_seller_can_reply_to_buyer_inquiry(): void
    {
        $ticketA = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'category' => 'order_issue',
            'subject' => 'Order inquiry',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->sellerA, 'seller')->post(route('seller.support.tickets.reply', $ticketA), [
            'message' => 'Hello Alice, we have dispatched a fresh replacement consignment today!',
        ]);

        $response->assertRedirect(route('seller.support.tickets.show', $ticketA));

        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticketA->id,
            'user_id' => $this->sellerA->id,
            'sender_type' => 'seller',
            'message' => 'Hello Alice, we have dispatched a fresh replacement consignment today!',
        ]);

        $this->assertEquals('waiting_customer', $ticketA->fresh()->status);
    }

    public function test_seller_cannot_view_inquiry_for_different_store(): void
    {
        $ticketA = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->customerA->id,
            'seller_id' => $this->sellerA->id,
            'category' => 'order_issue',
            'subject' => 'Secret inquiry for Seller A',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        // Seller B attempts to view Seller A's customer inquiry
        $response = $this->actingAs($this->sellerB, 'seller')->get(route('seller.support.tickets.show', $ticketA));
        $response->assertStatus(403);
    }
}
