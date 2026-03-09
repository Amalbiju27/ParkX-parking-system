<div align="center">
  <h1>🚗 ParkX</h1>
  <p><b>City-Level Vehicle Parking Management System</b></p>

  ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
  ![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
  ![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
  ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
  
  <br />
  <p>
    A centralized, robust web application engineered to modernize urban parking infrastructure. Developed as a Bachelor of Computer Science academic project at Rajagiri College of Social Sciences.
  </p>
</div>

---

## 📑 Table of Contents
- [Core Features](#-core-features)
- [Technology Stack](#-technology-stack)
- [Screenshots](#-screenshots)
- [Getting Started](#-getting-started)
- [System Architecture](#-system-architecture)

---

## ✨ Core Features

* **Real-Time Visual Matrix:** A live, responsive grid on the Owner Dashboard dynamically reflecting the physical parking lot layout. Slots automatically update their status (Available/Occupied) the exact moment a ticket is scanned.
* **Concurrency Control:** Utilizes strict database transactions (`DB::transaction`) during the booking phase to completely eliminate race conditions and prevent double-booking.
* **Live QR Ticket Scanning:** Features an integrated web-camera scanner (`html5-qrcode`) allowing lot owners to instantly check-in vehicles by scanning the driver's dynamic mobile ticket.
* **Strict Security Validation:** Mandates a vehicle condition video upload during booking, utilizing both client-side interception and server-side MIME-type/size validation.
* **Dynamic Time Extensions:** Includes robust time management allowing parked users to extend their duration via a secure mock checkout, and providing a 10-minute fine-based "Grace Period" for users running late.
* **Automated Expiry Engine:** Implements Laravel Task Scheduling to automatically free up slots if a user fails to pay within 15 minutes of initiating a booking.

---

## 🛠️ Technology Stack

| Category | Technologies |
| --- | --- |
| **Backend** | Laravel 12 (PHP 8.2) |
| **Database** | PostgreSQL |
| **Frontend** | Blade Templates, HTML5, CSS3, JavaScript (ES6), Bootstrap 5 |
| **Integration** | Simple-QRCode (Generator), HTML5-QRCode (Webcam Scanner) |
| **Version Control**| Git, GitHub |

---

## 📸 Screenshots
*(Add your project screenshots here by dragging and dropping images from your `SCREENSHOTS` folder directly into the GitHub web editor)*

- **User Dashboard & QR Ticket:**
- **Owner Live Matrix:**
- **Mock Payment Gateway:**

---

## 🚀 Getting Started

### Prerequisites
Ensure you have the following installed on your local machine:
* PHP >= 8.2
* Composer
* PostgreSQL
* Git

### Local Installation

**1. Clone the repository**
```bash
git clone [https://github.com/YOUR-USERNAME/ParkX.git](https://github.com/YOUR-USERNAME/ParkX.git)
cd ParkX
