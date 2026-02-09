# Pharmacy Panel Implementation

## Overview
A new Pharmacy module has been integrated into the Doctor Prescription System to facilitate the dispensing of prescriptions. This module allows pharmacy users to search for prescriptions sent by doctors, view their details, print them, and mark them as dispensed.

## Architecture
- **Authentication**: A separate `pharmacy` guard and `pharmacy_users` table/provider were added.
- **Guard**: `pharmacy`
- **Model**: `App\Models\PharmacyUser`
- **Routes**: Prefixed with `/pharmacy` and namespaced under `pharmacy.`.
- **Controllers**:
    - `PharmacyAuthController`: Handles login/logout for pharmacy users.
    - `PharmacyDashboardController`: Handles dashboard, prescription viewing, and dispensing.
    - `AdminPharmacyController`: Handles pharmacy user management for admins.

## Database Changes
- **`pharmacy_users` Table**: Contains `name`, `email`, `password`, `facility_id` (linked to `facilities`).
- **`prescriptions` Table Extensions**:
    - `status`: `draft` (default), `sent`, `dispensed`.
    - `sent_at`: Timestamp when the doctor sends the prescription.
    - `dispensed_at`: Timestamp when the pharmacy marks it as dispensed.
    - `dispensed_by`: ID of the pharmacy user who dispensed it.

## Key Features & Logic
1. **Doctor Actions**:
    - After saving a prescription, doctors can click "Send to Pharmacy".
    - This updates the status to `sent` and sets `sent_at`.
    - Once sent, the prescription is "locked" (the button disappears, and status prevents re-sending).

2. **Pharmacy Actions**:
    - Dashboard shows only prescriptions with status `sent` or `dispensed`.
    - Searching by prescription number is supported.
    - Printing uses the same layout as the doctor panel but is accessible within the pharmacy namespace.
    - "Dispense" button marks the prescription as `dispensed`, sets `dispensed_at` and `dispensed_by`.

3. **Admin Actions**:
    - Manage pharmacy users (Create, Edit, Delete).
    - Monitor prescription statuses (visible in dashboard and prescription lists).
    - View dispensing details (who dispensed and when).

4. **Logging**:
    - All sending and dispensing actions are logged using Laravel's `Log` facade with descriptive messages.

## Aesthetics
- **Pharmacy Panel**: Uses a Teal (`#0d9488`) theme to distinguish it from the Doctor Panel (Purple/Blue).
- **Badges**: Standardized badges for statuses across all panels (`secondary` for Draft, `warning` for Sent, `success` for Dispensed).
