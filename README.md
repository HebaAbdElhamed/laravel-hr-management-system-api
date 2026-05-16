# 🚀 Enterprise HR Management System API

A professional and scalable HR Management System RESTful API built with Laravel 13.

# ✨ Features

## 🔐 Authentication & Authorization
- Secure authentication using Laravel Sanctum
- Login & Register APIs
- Protected routes with middleware
- Role-based access control (Admin / Employee)
- Secure logout functionality

---

## 👨‍💼 Employee Management
- Create employees
- Update employee data
- Delete employees
- View employee details
- Employee profile management
- Department assignment
- Employee codes support

---

## 🏢 Department Management
- Create departments
- Update departments
- Delete departments
- View all departments
- Assign employees to departments

---

## ⏰ Attendance System
- Employee Check-In
- Employee Check-Out
- Attendance history
- Attendance tracking dashboard
- Admin attendance monitoring

---

## 🌴 Leave Management

- Apply for leave requests
- Cancel leave requests
- Admin approval/rejection
- Leave balance tracking
- Employee leave history

---

## 💰 Payroll System

- Payroll generation
- Salary tracking
- Payment status management
- Employee payroll history
- Admin payroll controls

---

# 🛠️ Tech Stack

- Laravel 13
- PHP 8+
- MySQL
- Laravel Sanctum
- RESTful API
- Eloquent ORM
- MVC Architecture
- Service Layer Pattern

---

# 📂 Project Structure

```bash
app/
 ├── Models/
 ├── Services/
 ├── Http/
 └── Providers/

routes/
 └── api.php

database/
 └── migrations/


 🔥 API Modules
Authentication
Register
Login
Logout
Current User
Departments
CRUD Operations
Employees
CRUD Operations
Attendance
Check In
Check Out
Attendance History
Leaves
Apply Leave
Leave Decision
Leave Balance
Payrolls
Generate Payroll
Mark as Paid
Payroll History

⚙️ Installation & Setup
1️⃣ Clone Repository
git clone https://github.com/HebaAbdElhamed/laravel-hr-management-system-api.git

2️⃣ Enter Project Folder
cd laravel-hr-management-system-api
3️⃣ Install Dependencies
composer install
4️⃣ Create Environment File
cp .env.example .env
5️⃣ Generate Application Key
php artisan key:generate
6️⃣ Configure Database

Update your .env file:

DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
7️⃣ Run Migrations
php artisan migrate
8️⃣ Start Development Server
php artisan serve

Server will run on:

http://127.0.0.1:8000
🔑 API Authentication

This project uses:

Laravel Sanctum

Protected routes require Bearer Token authentication.

📬 API Testing

You can test APIs using:

Postman
Insomnia
Thunder Client
🌐 Frontend Repository

The frontend application for this project is available here:

👉 Frontend Repository:

https://github.com/HebaAbdElhamed

If you don't have a frontend yet, you can use the frontend repository from my GitHub profile.

🧱 Architecture & Best Practices

This project follows:

RESTful API standards
Clean code principles
MVC Architecture
Service Layer Pattern
Reusable business logic
Scalable folder structure
Middleware-based authorization
📌 Future Improvements
Docker support
API documentation using Swagger
Notifications system
Advanced reporting dashboard
Email integrations
Multi-role permissions
Unit & Feature testing
👩‍💻 Author
Heba Elgohary
LinkedIn: https://www.linkedin.com/in/heba-elgohary-a13074167/
GitHub: https://github.com/HebaAbdElhamed
Email: hebaabdelhamede@gmail.com
⭐ Support

If you like this project:

Star the repository ⭐
Fork the project 🍴
Share it with others 🚀
```
