<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportAgentPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guest_cannot_access_support_agent_panel(): void
    {
        $response = $this->get('/agent');
        $response->assertRedirect('/login');
    }

    public function test_regular_customer_cannot_access_support_agent_panel(): void
    {
        $customer = User::create([
            'name' => 'Regular Customer',
            'email' => 'customer.test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer, 'user')->get('/agent');
        $response->assertRedirect('/user/dashboard');
    }

    public function test_guest_cannot_access_admin_support_agent_management(): void
    {
        $response = $this->get('/admin/support/agents');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_and_create_support_agent(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin.test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 1. Admin views index
        $resIndex = $this->actingAs($admin, 'admin')->get('/admin/support/agents');
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Support Specialists &amp; Agents', false);

        // 2. Admin views create form
        $resCreate = $this->actingAs($admin, 'admin')->get('/admin/support/agents/create');
        $resCreate->assertStatus(200);

        // 3. Admin provisions new support agent
        $testEmail = 'agent.test.' . time() . '@bazaario.com';
        $resStore = $this->actingAs($admin, 'admin')->post('/admin/support/agents', [
            'name' => 'Kavita Specialist',
            'email' => $testEmail,
            'phone' => '+91 9123456789',
            'department' => 'Order Inquiries & Disputes',
            'staff_id' => 'BZ-AGT-T' . rand(100, 999),
            'password' => 'secretPassword123!',
            'password_confirmation' => 'secretPassword123!',
            'status' => 'active',
            'bio' => 'Dedicated dispute specialist.',
        ]);

        $resStore->assertRedirect('/admin/support/agents');

        // Verify agent exists in DB
        $createdAgent = User::where('email', $testEmail)->first();
        $this->assertNotNull($createdAgent);
        $this->assertEquals('support_agent', $createdAgent->role);
        $this->assertTrue($createdAgent->isSupportAgent());
    }

    public function test_support_agent_can_login_via_normal_login_and_redirects_to_agent_dashboard(): void
    {
        $testEmail = 'agent.login.' . time() . '@bazaario.com';
        $agent = User::create([
            'name' => 'Suresh Desk',
            'email' => $testEmail,
            'password' => Hash::make('agentPass123!'),
            'role' => 'support_agent',
            'staff_id' => 'BZ-AGT-L' . rand(100, 999),
            'department' => 'Returns & Escrow',
            'status' => 'active',
        ]);

        // Normal Login via POST /login
        $response = $this->post('/login', [
            'email' => $testEmail,
            'password' => 'agentPass123!',
        ]);

        // Must redirect directly to agent workstation!
        $response->assertRedirect(route('agent.dashboard'));

        $this->assertAuthenticatedAs($agent, 'user');
        $agent->refresh();
        $this->assertTrue($agent->is_online);
    }

    public function test_support_agent_workstation_ticket_operations(): void
    {
        $agent = User::create([
            'name' => 'Workstation Agent',
            'email' => 'agent.ops.' . time() . '@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'support_agent',
            'staff_id' => 'BZ-AGT-O' . rand(100, 999),
            'department' => 'Order Inquiries',
            'status' => 'active',
        ]);

        $customer = User::create([
            'name' => 'Customer Joe',
            'email' => 'joe.test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create an unassigned ticket
        $ticket = SupportTicket::create([
            'ticket_number' => 'TIC-TEST-' . rand(1000, 9999),
            'user_id' => $customer->id,
            'subject' => 'Package tracking delayed test',
            'category' => 'delivery_delay',
            'priority' => 'high',
            'status' => 'open',
        ]);

        // 1. Agent views dashboard
        $resDash = $this->actingAs($agent, 'user')->get('/agent');
        $resDash->assertStatus(200);
        $resDash->assertSee('Workstation Dashboard');

        // 2. Agent views ticket queue
        $resQueue = $this->actingAs($agent, 'user')->get('/agent/tickets?view=unassigned');
        $resQueue->assertStatus(200);
        $resQueue->assertSee($ticket->ticket_number);

        // 3. Agent claims ticket
        $resClaim = $this->actingAs($agent, 'user')->post("/agent/tickets/{$ticket->id}/claim");
        $resClaim->assertStatus(302);
        $ticket->refresh();
        $this->assertEquals($agent->id, $ticket->assigned_agent_id);

        // 4. Agent views ticket details
        $resShow = $this->actingAs($agent, 'user')->get("/agent/tickets/{$ticket->id}");
        $resShow->assertStatus(200);
        $resShow->assertSee($ticket->subject);

        // 5. Agent posts a reply
        $resReply = $this->actingAs($agent, 'user')->post("/agent/tickets/{$ticket->id}/reply", [
            'message' => 'Hello, I am tracking your shipment with the logistics desk right now.',
            'status' => 'waiting_customer',
        ]);
        $resReply->assertStatus(302);
        $ticket->refresh();
        $this->assertEquals('waiting_customer', $ticket->status);
        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'sender_type' => 'agent',
        ]);

        // 6. Agent updates status
        $resStatus = $this->actingAs($agent, 'user')->post("/agent/tickets/{$ticket->id}/status", [
            'status' => 'in_progress',
            'priority' => 'urgent',
        ]);
        $resStatus->assertStatus(302);
        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertEquals('urgent', $ticket->priority);

        // 7. Agent escalates ticket
        $resEsc = $this->actingAs($agent, 'user')->post("/agent/tickets/{$ticket->id}/escalate", [
            'escalation_reason' => 'Package lost in transit, requesting escrow refund authorization.',
        ]);
        $resEsc->assertStatus(302);
        $ticket->refresh();
        $this->assertTrue((bool)$ticket->is_escalated);
    }

    public function test_support_agent_status_toggle(): void
    {
        $agent = User::create([
            'name' => 'Toggle Agent',
            'email' => 'agent.toggle.' . time() . '@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'support_agent',
            'status' => 'active',
            'is_online' => false,
        ]);

        $res = $this->actingAs($agent, 'user')->post('/agent/status/toggle');
        $res->assertStatus(302);
        $agent->refresh();
        $this->assertTrue($agent->is_online);

        $res2 = $this->actingAs($agent, 'user')->post('/agent/status/toggle');
        $res2->assertStatus(302);
        $agent->refresh();
        $this->assertFalse($agent->is_online);
    }

    public function test_support_agent_live_chat_operations(): void
    {
        $agent = User::create([
            'name' => 'Chat Desk Agent',
            'email' => 'agent.chat.' . time() . '@bazaario.com',
            'password' => Hash::make('password123'),
            'role' => 'support_agent',
            'status' => 'active',
            'is_online' => true,
        ]);

        $customer = User::create([
            'name' => 'Live Buyer',
            'email' => 'buyer.live.' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $conversation = ChatConversation::create([
            'user_id' => $customer->id,
            'title' => 'Product Inquiry',
            'status' => 'open',
            'channel' => 'live_chat',
            'last_message_at' => now(),
        ]);

        $msg1 = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'sender_type' => 'user',
            'sender_name' => $customer->name,
            'message' => 'Hello, is this product in stock?',
            'created_at' => now(),
        ]);

        // 1. Agent visits /agent/chats
        $resChats = $this->actingAs($agent, 'user')->get('/agent/chats');
        $resChats->assertStatus(200);
        $resChats->assertSee('Live Support Desk');
        $resChats->assertSee($customer->name);
        $resChats->assertSee('Hello, is this product in stock?');

        // 2. Agent replies to chat
        $resReply = $this->actingAs($agent, 'user')->post("/agent/chats/{$conversation->id}/reply", [
            'message' => 'Yes! We have fresh stock ready for dispatch.',
        ]);
        $resReply->assertStatus(302);

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'message' => 'Yes! We have fresh stock ready for dispatch.',
        ]);

        // 3. Agent polls for messages
        $resPoll = $this->actingAs($agent, 'user')->getJson("/agent/chats/{$conversation->id}/poll?after_id={$msg1->id}");
        $resPoll->assertStatus(200);
        $resPoll->assertJsonPath('success', true);
        $this->assertCount(1, $resPoll->json('messages'));

        // 4. Agent resolves session
        $resStatus = $this->actingAs($agent, 'user')->post("/agent/chats/{$conversation->id}/status", [
            'status' => 'resolved',
        ]);
        $resStatus->assertStatus(302);
        $conversation->refresh();
        $this->assertEquals('resolved', $conversation->status);
    }
}
