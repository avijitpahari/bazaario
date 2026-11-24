# CONTAI COLLEGE OF LEARNING & MANAGEMENT SCIENCE
### Department of Computer Applications
**Project Title:** Multi-Vendor Marketplace (Bazaario)  
**Name:** AVIJIT PAHARI &nbsp;&nbsp;|&nbsp;&nbsp; **Roll No:** 34042724041  
**Module Evaluation Extension:** Support System (System UI & Admin Panel UI)

---

## 1. System UI (Customer & Seller Support Module)

| SL NO. | ROLE | MODULE NAME | FUNCTIONALITY | FACULTY SIGN |
| :---: | :--- | :--- | :--- | :---: |
| **55** | Customer | Customer Support Module | Floating Live Support Chat Widget | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |
| **56** | Customer | Customer Support Module | Interactive FAQ Quick-Reply Actions | |
| **57** | Customer | Customer Support Module | Create Support Ticket | |
| **58** | Customer | Customer Support Module | Link Order & Product to Support Ticket | |
| **59** | Customer | Customer Support Module | Upload Document / Image Proof (JPG, PNG, PDF up to 10MB) | |
| **60** | Customer | Customer Support Module | Ticket Lifecycle Timeline Tracking (`Open` → `In Progress` → `Escalated` → `Resolved`) | |
| **61** | Customer | Customer Support Module | Threaded Conversation & Ticket Reply Form | |
| **62** | Customer | Customer Support Module | Reopen Resolved Ticket or Mark Closed | |
| **63** | Customer | Customer Support Module | Escalate Ticket to Formal Admin Dispute | |
| **28** | Seller | Seller Support Module | Store Customer Inquiries & Care Desk | |
| **29** | Seller | Seller Support Module | Raise Merchant Assistance Ticket to Admin | |
| **30** | Seller | Seller Support Module | Link Seller Order to Inquiry for Escrow / Payout Tracing | |
| **31** | Seller | Seller Support Module | Upload Merchant Invoice / Shipping Proof Attachment | |
| **32** | Seller | Seller Support Module | Escalate Dispute Defense Against Buyer Claims | |
| **33** | Seller | Seller Support Module | Update Ticket Status (`Open`, `In Progress`, `Waiting on Customer`, `Resolved`) | |

---

## 2. Admin Panel UI (Support Desk & Dispute Console)

| SL NO. | ROLE | MODULE NAME | FUNCTIONALITY | FACULTY SIGN |
| :---: | :--- | :--- | :--- | :---: |
| **28** | Admin | Admin Support Desk | Live Support Desk Sidebar Navigation & Unread Counter | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |
| **29** | Admin | Admin Support Desk | Unified 3-Column Support Console (Live Chats & Tickets) | |
| **30** | Admin | Admin Support Desk | Filter Tickets by Status (`Open`, `In Progress`, `Resolved`, `Escalated`) | |
| **31** | Admin | Admin Support Desk | Filter Tickets by Priority (`Low`, `Medium`, `High`, `Urgent`) | |
| **32** | Admin | Admin Support Desk | Real-Time Live Chat Operator Console with Polling | |
| **33** | Admin | Admin Support Desk | Canned / Quick Automated Support Responses | |
| **34** | Admin | Admin Support Desk | Multi-Party Ticket Arbitration Desk (Buyer vs Seller Mediation) | |
| **35** | Admin | Admin Support Desk | Arbitrate & Enforce Escrow Settlement Decisions (`Approve Refund`, `Release Escrow`, `Reject Refund`) | |
| **36** | Admin | Admin Support Desk | Assign Ticket to Support Agent & Adjust Priority Level | |
| **37** | Admin | Admin Support Desk | Record System Audit Feed & Real-time Support Event Alerts | |

---

## System Architecture & Code Locations

1. **Customer System UI:**
   - Floating Live Chat: [`resources/views/components/support-chat-widget.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/components/support-chat-widget.blade.php)
   - Support Hub & Ticket Index: [`resources/views/user/support/index.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/user/support/index.blade.php)
   - Ticket Creation Form: [`resources/views/user/support/create.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/user/support/create.blade.php)
   - Ticket Conversation & Tracker: [`resources/views/user/support/show.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/user/support/show.blade.php)

2. **Seller System UI:**
   - Inquiries & Support Desk: [`resources/views/seller/support/index.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/seller/support/index.blade.php)
   - Merchant Ticket Creation Form: [`resources/views/seller/support/create.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/seller/support/create.blade.php)
   - Dispute Defense & Response: [`resources/views/seller/support/show.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/seller/support/show.blade.php)

3. **Admin Panel UI:**
   - Sidebar Navigation Link: [`resources/views/layouts/admin.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/layouts/admin.blade.php)
   - Dashboard Quick Action Button: [`resources/views/admin/dashboard.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/admin/dashboard.blade.php)
   - Unified Support Console: [`resources/views/admin/support/index.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/admin/support/index.blade.php)
   - Ticket Arbitration Desk: [`resources/views/admin/support/ticket-show.blade.php`](file:///c:/xampp/htdocs/bazaario/resources/views/admin/support/ticket-show.blade.php)
