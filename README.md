<div align="center">
  <h1>ParkX</h1>
  <p><b>City-Level Vehicle Parking Management System</b></p>

  <p>
    <img src="https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel" />
    <img src="https://img.shields.io/badge/PostgreSQL-316192?style=flat-square&logo=postgresql&logoColor=white" alt="PostgreSQL" />
    <img src="https://img.shields.io/badge/TailwindCSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black" alt="JavaScript" />
  </p>

  <p>
    A centralized web application designed to modernize urban parking infrastructure.<br />
    Developed as a <b>BSc Computer Science project</b> at <b>Rajagiri College of Social Sciences</b>.
  </p>
</div>

<hr />

## Table of Contents

- [Core Features](#core-features)
- [Technology Stack](#technology-stack)
- [Screenshots](#screenshots)
- [Getting Started](#getting-started)

<hr />

## Core Features

### Real-Time Parking Matrix
A responsive live grid that mirrors the actual parking layout on the Owner Dashboard. Parking slots automatically update their status (Available / Occupied) when a QR ticket is scanned.

### Concurrency Control
Utilizes database transactions (`DB::transaction`) during the booking phase to eliminate race conditions and prevent double booking.

### Live QR Ticket Scanning
Features an integrated webcam scanner using `html5-qrcode`, allowing operators to instantly verify and check-in vehicles upon arrival.

### Security Validation
Mandates vehicle condition video uploads during booking, utilizing both client-side interception and server-side MIME validation.

### Dynamic Time Extensions
Users can extend parking duration through a secure mock checkout system. A 10-minute grace period is provided prior to the application of late fines.

### Automated Slot Expiry
Implements Laravel Task Scheduling to automatically free up slots if payment is not completed within 15 minutes of booking initiation.

<hr />

## Technology Stack

| Category | Technologies |
| :--- | :--- |
| **Backend** | Laravel 12 (PHP 8.2) |
| **Database** | PostgreSQL |
| **Frontend** | Blade Templates, HTML5, Tailwind CSS, JavaScript (ES6) |
| **Integration** | Simple-QRCode (Generator), HTML5-QRCode (Scanner) |
| **Version Control** | Git, GitHub |

<hr />

## Screenshots

*(Ensure your images are uploaded to the `screenshots` directory in your repository root)*

### Owner Parking Matrix
<p align="center">
  <img src="screenshots/owner-dashboard.png" width="850" alt="Owner Dashboard Live Matrix">
</p>

### User Dashboard & QR Ticket
<p align="center">
  <img src="screenshots/qr-ticket.png" width="850" alt="User Dashboard and Mobile QR Ticket">
</p>

### Mock Payment Gateway
<p align="center">
  <img src="screenshots/payment.png" width="850" alt="Secure Mock Payment Gateway">
</p>

<hr />

## Getting Started

### Prerequisites
Ensure the following tools are installed on your local environment:
- PHP >= 8.2
- Composer
- PostgreSQL
- Node.js & NPM
- Git

### Local Installation

**1. Clone the repository**

git clone [https://github.com/Amalbiju27/ParkX.git](https://github.com/Amalbiju27/ParkX.git)
cd ParkX
2. Install dependencies

Bash
composer install
npm install
npm run build
3. Configure environment

Bash
cp .env.example .env
php artisan key:generate
Open .env and configure your PostgreSQL database credentials and set APP_TIMEZONE=Asia/Kolkata.

4. Run database migrations

Bash
php artisan migrate --seed
5. Link storage (for file uploads)

Bash
php artisan storage:link
6. Start the development server

Bash
php artisan serve
Access the application at http://127.0.0.1:8000.

7. Run the background scheduler

Bash
php artisan schedule:work
<hr />

<div align="center">
<p>Developed by <b>Amal Biju</b></p>
</div>
