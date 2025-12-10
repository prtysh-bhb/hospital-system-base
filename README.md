# Hospital Appointment Management System

A comprehensive web-based appointment booking and scheduling platform designed for hospitals and clinics to streamline patient appointments, staff management, and doctor availability.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [User Roles](#user-roles)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Future Enhancements](#future-enhancements)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 Overview

The Hospital Appointment Management System eliminates manual appointment workflows by providing:

- **Online booking** for patients through the hospital website
- **Administrative dashboard** for hospital staff to manage and verify appointments
- **Doctor portal** for physicians to control their availability and view schedules

This solution improves operational efficiency and enhances the patient experience across all touchpoints.

---

## ✨ Features

### Patient Portal
- Browse available doctors by specialty
- View real-time slot availability
- Book appointments with instant confirmation
- Receive unique Appointment ID
- Access confirmation details

### Hospital Admin Dashboard
- Overview of daily and upcoming appointments
- Advanced search functionality (by ID, patient name, or doctor)
- Patient arrival tracking
- Complete doctor profile management
- Calendar and availability control
- Block/unblock specific time slots or full days

### Doctor Mobile Portal
- Secure login access
- Daily appointment schedule view
- Upcoming bookings overview
- Filter by date and appointment status
- Personal availability management
- Flexible time blocking options

---

## 👥 User Roles

### 1. **Patient** (Public Access)
Access the booking interface through the hospital website to select doctors, choose time slots, and receive appointment confirmation.

### 2. **Hospital Admin / Reception** (Authenticated)
Manage the entire appointment ecosystem including doctor profiles, availability calendars, and patient check-ins.

### 3. **Doctor** (Authenticated Mobile)
View schedules, manage appointments, and control personal availability through a mobile-optimized interface.

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel (PHP) |
| **Frontend** | Blade Templates / Inertia.js |
| **Database** | MySQL |
| **Build Tools** | Composer, NPM |
| **Additional** | Laravel Migrations & Seeders |

---

## 🚀 Installation

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM (optional, for frontend assets)

### Step 1: Clone Repository
```bash
git clone <repository-url>
cd hospital-system
```

### Step 2: Install Dependencies
```bash
composer install
npm install  # Optional: if using frontend build tools
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_appointments
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Database Setup
```bash
php artisan migrate
php artisan db:seed  # Includes admin user, sample doctors, and demo data
```

### Step 5: Start Development Server
```bash
php artisan serve
npm run dev  # Optional: for hot-reloading frontend assets
```

Access the application at: **http://127.0.0.1:8000**

---

## 📖 Usage

### Patient Booking Flow
1. Navigate to `/book-appointment`
2. Browse and select a doctor
3. Choose available date and time slot
4. Fill in patient information
5. Confirm booking details
6. Receive Appointment ID and confirmation

### Admin Access
Default admin credentials (after seeding):
```
Email: admin@gmail.com
Password: admin@123
```

Dashboard features:
- View today's appointments
- Search and filter bookings
- Mark patient arrivals
- Manage doctor profiles and availability

### Doctor Access
Login credentials provided by admin. Mobile-optimized interface for:
- Viewing daily schedule
- Managing upcoming appointments
- Blocking unavailable time slots

---

## 📁 Project Structure

```
hospital-appointment-system/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   └── js/
├── routes/
│   ├── web.php
│   └── api.php
├── public/
└── tests/
```

---

## 🔮 Future Enhancements

- [ ] SMS/WhatsApp appointment reminders
- [ ] Patient appointment rescheduling
- [ ] Online payment integration
- [ ] Multi-clinic/branch support
- [ ] Telemedicine integration
- [ ] Patient medical history tracking
- [ ] Automated appointment confirmations
- [ ] Analytics and reporting dashboard

---


## 📞 Support

For issues, questions, or suggestions:
- Open an issue in the repository
- Contact: saels@brainerhub.com

---

**Built with ❤️ for healthcare providers**