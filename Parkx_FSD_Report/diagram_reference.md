# ParkX Diagram Creation Reference

This document provides all the technical details required to create the architectural and logical diagrams for the ParkX Smart Parking Management System report.

## 1. Technical Stack (Tools)
- **Programming Language**: PHP 8.2+
- **Framework**: Laravel 12 (MVC Architecture)
- **Database**: PostgreSQL (Relational)
- **Frontend**: Tailwind CSS (CDN), Vanilla JavaScript
- **Web Server**: Apache (XAMPP for development)
- **Version Control**: Git

---

## 2. System Actors & Roles
### **User (Driver)**
- **Objective**: Discover and reserve parking slots.
- **Key Actions**: 
  - Register/Login.
  - Search venues by name/location.
  - Filter slots by vehicle category (Two Wheeler, Four Wheeler, etc.).
  - Select specific slots via a visual grid.
  - Generate a secure Digital Ticket (QR + 6-Digit PIN).

### **Property Owner**
- **Objective**: Monetize and manage parking inventory.
- **Key Actions**:
  - Register venue (Requires Admin Approval).
  - Define slot geometry (Identifiers like A1, B2).
  - Scan Driver QR codes for check-in.
  - Perform Manual Ticket Entry (PIN Fallback) for offline arrivals.
  - Monitor real-time occupancy.

### **Super Admin**
- **Objective**: Govern the platform and manage global data.
- **Key Actions**:
  - Approve/Reject Owner registrations.
  - Manage global vehicle categories and pricing (Base Charge + Hourly Rate).
  - Access system-wide usage analytics.

---

## 3. Data Dictionary (PostgreSQL Schema)
### Table: `users`
- `id` (PK): BigSerial
- `name`: Varchar(255)
- `email`: Varchar(255), Unique
- `role`: Varchar(20) - (admin, owner, user)
- `password`: Varchar(255)

### Table: `vehicle_categories`
- `id` (PK): BigSerial
- `name`: Varchar(255) - (e.g., 'SUV', 'Two Wheeler')
- `base_charge`: Decimal(8,2)
- `hourly_rate`: Decimal(8,2)

### Table: `parking_spaces` (Venues)
- `id` (PK): BigSerial
- `owner_id` (FK): References `users.id`
- `name`: Varchar(255)
- `location`: Text

### Table: `parking_slots`
- `id` (PK): BigSerial
- `space_id` (FK): References `parking_spaces.id`
- `category_id` (FK): References `vehicle_categories.id`
- `identifier`: Varchar(50) - (e.g., 'Spot-01')
- `status`: Enum - (available, occupied, reserved)

### Table: `bookings`
- `id` (PK): BigSerial
- `user_id` (FK): References `users.id`
- `slot_id` (FK): References `parking_slots.id`
- `ticket_number`: Varchar(6), Unique (Manual PIN)
- `start_time` / `end_time`: Timestamp
- `scanned_at`: Timestamp
- `status`: Varchar(20) - (booked, occupied, completed)

---

## 4. Diagram Logic & Flow
### **Use Case Diagram**
- **Primary Line**: Admin approves Owner -> Owner creates Space -> Space contains Slots -> Slots belong to Category.
- **Interaction Line**: User searches Spaces -> Filters by Category -> Books Slot -> Booking creates Ticket.
- **Verification Line**: Owner scans Ticket -> Booking status changes to 'Occupied'.

### **Activity Diagram (User Booking Flow)**
1. **Start** -> Dashboard.
2. Search Venue -> **Decision**: Venue Found?
3. Select Venue -> Show Category Filter.
4. Select Category -> Show Visual Slot Grid.
5. **Decision**: Slot Available?
6. Select Slot -> Confirm Start/End Time.
7. **Action**: Create Booking & Generate 6-Digit PIN.
8. View Digital Ticket -> **End**.

### **Sequence Diagram (Slot Reservation & Locking)**
1. **User** -> requests `POST /bookings`.
2. **Controller** -> initiates Transaction.
3. **Database** -> performs `SELECT ... FOR UPDATE` on `parking_slots`.
4. **Database** -> checks if slot status is 'available'.
5. **Controller** -> calculates total charge.
6. **Database** -> inserts `bookings` record.
7. **Database** -> updates `parking_slots` status to 'reserved'.
8. **Controller** -> returns JSON response with Ticket PIN.

### **Class Diagram (Models)**
- `User` (1) has many `ParkingSpace` ( * ).
- `ParkingSpace` (1) has many `ParkingSlot` ( * ).
- `VehicleCategory` (1) classifies many `ParkingSlot` ( * ).
- `ParkingSlot` (1) has many `Booking` ( * ).
- `User` (1) makes many `Booking` ( * ).
