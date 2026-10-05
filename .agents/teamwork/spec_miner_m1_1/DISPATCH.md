## 2026-09-30T04:59:46Z
Your working directory is: c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m1_1

MANDATORY FIRST STEP: Read the user's authoritative request at:
c:\xampp\htdocs\bazaario\.agents\teamwork\ORIGINAL_REQUEST.md

Also read the project architecture at:
c:\xampp\htdocs\bazaario\PROJECT.md
and prior survey findings at:
c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_survey_1\handoff.md

Your role is Onboarding UI & Wizard Spec Miner for Milestone 1 (Seller Onboarding & Access Control - R1).
Your task is to analyze the UI structure and interactions needed for:
1. `resources/views/layouts/seller-onboarding.blade.php`: Distraction-free Warm Modernist container layout with top brand bar, help modal, Space Grotesk / Inter / JetBrains Mono typography, Material Symbols Outlined, and Tailwind theme tokens.
2. `resources/views/layouts/seller.blade.php`: Full seller workspace layout with w-72 left sidebar, top header with search & notification, profile pill, and flash toast alerts.
3. `resources/views/seller/onboarding/wizard.blade.php`: The 5-step guided wizard integrating `stitch_bazaario_seller_onboarding_portal/bazaario_seller_onboarding_approval/code.html`:
   - Step 1: Account info (Name, Email, Phone, Password).
   - Step 2: Seller Type selection (Farmer, Kirana Store, Dark Store, Individual) with custom badges & interactive cards.
   - Step 3: Shop/Farm details, dynamic produce/hours fields, storefront image upload dropzone with preview.
   - Step 4: Geolocation address fields, browser HTML5 GPS Lat/Lng detector button, manual coordinate inputs, and radar map preview.
   - Step 5: Review & Submit card with 1-click step edit jump buttons.
4. `resources/views/seller/pending.blade.php`: Verify alignment with the 5-stage approval waiting terminal in the template.

Recommend an exact, component-level implementation strategy for the worker. Do NOT implement the code yourself.
Write your report to:
c:\xampp\htdocs\bazaario\.agents\teamwork\spec_miner_m1_1\handoff.md
and send a completion message back to the orchestrator.
