# 🏨 Hotel Management System

> **Official Task Division**
> Stack: `Laravel` • `Vue 3` • `Inertia` • `Tailwind CSS` — Developed as a collaborative project

---

## 📋 Project Overview

Hotel-System is a robust web application tailored for managing a hotel's day-to-to operations. It features complex role-based access control, allowing **Admins**, **Managers**, **Receptionists**, and **Clients** to interact with the system securely. The platform facilitates user and client management, room and floor allocations, reservations, and secure payment processing.

---

## 🛠 Tech Stack

| Layer     | Technology                                     |
| --------- | ---------------------------------------------- |
| Backend   | Laravel 11 (PHP 8.3)                           |
| Database  | MySQL (via Eloquent ORM)                       |
| Frontend  | Vue 3 + Inertia.js + Tailwind CSS + shadcn-vue |
| Role Mgmt | Spatie Laravel Permission                      |
| Other     | Stripe (Payments), Laravel Excel, Laravel Ban  |

---

## ⚙️ Prerequisites

Ensure the following dependencies are installed before running the project:

- **PHP 8.3+** — [php.net](https://www.php.net)
- **Composer** — [getcomposer.org](https://getcomposer.org)
- **Node.js** (v20+) & **npm** — [nodejs.org](https://nodejs.org)
- **MySQL** / XAMPP / Herd / Sail

---

## 🚀 Installation Steps

### Step 1 — Clone the repository

```powershell
git clone https://github.com/AhmedMohO/hotel-system.git
cd hotel-system
```

### Step 2 — Install Backend Dependencies

```powershell
composer install
```

### Step 3 — Install Frontend Dependencies

```powershell
npm install
```

### Step 4 — Environment Configuration

Create the environment file:

```powershell
copy .env.example .env
```

Open `.env` and fill in your database and stripe credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_system
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

Generate the application key:

```powershell
php artisan key:generate
```

### Step 5 — Database Setup and Seeding

Run the migrations and seed the database to get default roles, dummy users, floors, rooms, and reservations.

```powershell
php artisan migrate:fresh --seed
```

### Step 6 — Start the Development Servers

**Terminal :**

```powershell
composer run dev
```

### Step 7 — Access the Application

The application features two isolated dashboards, each protected by its own authentication guard and login page:

- **Admin & Staff Portal** (Admins, Managers, Receptionists):
  ```text
  http://localhost:8000/login
  ```

- **Client Booking Portal** (Hotel Guests):
  ```text
  http://localhost:8000/client/login
  ```

---

## 🔐 Default Login Credentials

After seeding, the following default accounts are available:

### Admin Account

| Field    | Value             |
| -------- | ----------------- |
| Email    | `admin@admin.com` |
| Password | `123456`          |
| Role     | `Admin`           |

### Other Roles

Managers, Receptionists, and Clients are generated randomly by the seeder. You can check the `users` and `clients` tables in the database to find their emails and use the default dummy password (`password`).

---

## 📁 Project Structure Highlights

```
hotel-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/     — Component specific controllers (Admin, Manager, etc.)
│   │   ├── Requests/        — Form requests for validation
│   ├── Models/              — Eloquent models (User, Client, Room, etc.)
├── database/
│   ├── migrations/          — DB schema definitions
│   ├── seeders/             — Initial data setup
│   └── factories/           — Mock data definitions
├── resources/
│   ├── js/
│   │   ├── Components/      — Reusable Vue/shadcn components
│   │   ├── Pages/           — Inertia Vue pages (Auth, Dashboard, Client)
│   │   └── Layouts/         — App structural layouts
├── routes/
│   └── web.php              — Role-protected Inertia routes
```

---

## 👥 Feature Breakdown

- **Authentication & Roles:** Safe, role-based login and redirection. Banning mechanism for specific roles.
- **Client Management:** Extensive CRM capabilities including approvals, profile management, avatar uploads, and toast notifications.
- **Room & Reservation Management:** Dynamic fetching of available rooms, checkout constraints, and reservations.
- **Payment Processing:** Integrated with Stripe for smooth checkout.
- **Real-Time Interactive UI:** Powered by Vue.js and motion animations for a polished, highly responsive UX.

---

## 👥 Meet the Team

- **Ahmed Wael**
- **Khaled**
- **Ahmed Mohammed Mostafa**
- **Reda**
- **Amira**

---

<div align="center">

**Hotel Management System** • Developed collaboratively by a 5-Member Team • Laravel + Vue 3

</div>
