# ParkX - Commercial Vehicle Parking Management System
## Project Overview
ParkX is a comprehensive parking management solution designed to streamline parking operations, facilitate advance bookings, and monitor parking spaces dynamically. It supports three distinct participant roles: **Admin**, **Owner**, and **User**, each with dedicated modules and access rights.

## Core Roles & Capabilities

### 1. User (Customer) Module
The User module focuses on providing a seamless booking and parking experience.
- **Advance Booking System**: Users can book a parking slot for a specific date and time interval (`start_time` to `end_time`), specifying their vehicle category and duration.
- **QR Code Ticketing**: Upon successful booking, a unique `ticket_number` is generated. This acts as a digital pass, verifiable via QR scan at the parking facility.
- **Vehicle Video Verification**: Enhances security by allowing users to upload a video of their vehicle during the booking process.
- **Booking Dashboard**: Allows users to track upcoming and active bookings, and download entry proofs and payment receipts.

### 2. Space Owner Module
The Owner module provides tools for managing day-to-day operations at a specific parking site.
- **Operational Dashboard**: Visualizes live metrics including available slots, current active parked vehicles, expected arrivals, and revenue generated.
- **Vehicle Entry (QR Validation)**: Owners scan the user's QR ticket. The system employs time-based validation to strictly prevent early check-ins (e.g., >15 mins before start time) and rejects expired tickets.
- **Vehicle Exit & Billing**: Manages the check-out process. It calculates total parked time, automatically detects overstays (`extended_minutes`), applies corresponding penalty charges (`fine_amount`), and finalizes the user's session.

### 3. System Admin Module
The Admin module handles platform-wide configurations and oversight.
- **Global Dashboard**: Monitors total active parking spaces, overall registered owners, and platform-wide revenue streams.
- **Owner Management**: Approves, registers, and deactivates parking space owners.
- **Parking Space Allocation**: Creates new parking domains, defines total capacity, and allocates slots.
- **Dynamic Pricing Configuration**: Manages `vehicle_categories`, defining the `base_charge` and `hourly_rate` dynamically based on vehicle type (Bike, Car, Truck, etc.).
- **Financial Exports**: Generates and exports comprehensive financial reports.

### 4. Automated Operations (Cron Jobs)
Background jobs ensure the system remains optimized.
- **Expired Booking Cancellation**: Automatically frees up reserved slots if a booking remains unpaid or unoccupied 15 minutes after its creation.
- **No-Show Handling**: Automatically flags and penalizes bookings where the user fails to arrive.

---

## Database Architecture (Schema)

### 1. `users` Table
Stores authentication and role-based access for all actors.
- **Fields**: `id`, `name`, `email`, `password`, `role` (enum: 'admin', 'owner', 'user'), `status`, `last_login`, timestamps.

### 2. `parking_spaces` Table
Details physical parking locations.
- **Fields**: `id`, `name`, `location`, `capacity`, `available_slots`, `status` (active/inactive), `owner_id` (FK to users).

### 3. `vehicle_categories` Table
Manages the pricing models applied to different types of transport.
- **Fields**: `id`, `name` (e.g., Bike, Car, Heavy), `base_charge`, `hourly_rate`.

### 4. `parking_slots` Table
Tracks individual spots inside a larger parking space, ensuring physical mapping.
- **Fields**: `id`, `parking_space_id` (FK), `slot_number` (e.g., A1, B2), `slot_type`, `status` (enum: 'available', 'occupied', 'reserved').

### 5. `parking_space_owners` Table
Stores extensive contact profiles for owners managing spaces.
- **Fields**: `id`, `user_id` (FK), `contact`, `address`, `status`.

### 6. `vehicles` Table
Logs generic vehicle entries and exits, typically for on-the-spot parking without pre-bookings.
- **Fields**: `id`, `parking_space_id`, `category_id`, `user_id`, `vehicle_number`, `entry_time`, `exit_time`, `expected_exit_time`, `duration`, `charge`, `penalty`, `status`, `slot_id`.

### 7. `bookings` Table
The central transactional table for advance reservations and state changes.
- **Fields**: `id`, `ticket_number` (string for QR), `user_id`, `parking_space_id`, `slot_id`, `status` (pending/booked/cancelled), `expires_at`, `vehicle_category_id`, `duration_hours`, `amount`, `payment_status`, `vehicle_number`, `booking_date`, `start_time`, `end_time`, `vehicle_video`, `scanned_at`, `extended_minutes`, `fine_amount`, `additional_charges`.

---

## Technical Stack & Logic Use-Cases
- **Framework**: Laravel 11.x (PHP)
- **Database**: MySQL/MariaDB (via Eloquent ORM)
- **Analytics**: Handled via `AnalyticsService.php` to project metrics on dashboards.
- **Pricing Service**: Isolated logic in `PricingService.php` ensures consistency in rate calculations, base fees, and overstay fines upon vehicle checkout.
