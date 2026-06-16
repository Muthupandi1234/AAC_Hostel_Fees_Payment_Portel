# Arul Anandar Hostel – Online Fees Payment Portal

A complete online hostel fees payment portal with student admission and admin management system.

## Features

### Student (User) Features
- ✅ Student Registration & Login
- ✅ Forgot Password with Security Question
- ✅ Online Admission Form (Complete with all required fields)
- ✅ Online Fees Payment (3 Instalments: ₹15,000, ₹13,000, ₹10,000)
- ✅ Razorpay Payment Gateway Integration
- ✅ Payment Receipt Download (PDF)
- ✅ Fees Payment History
- ✅ Secure Dashboard

### Admin Features
- ✅ Admin Login
- ✅ Admin Dashboard with Analytics
  - Total Students
  - Total Collected Fees
  - Pending Payments
  - Today's Payments
- ✅ Student List View
- ✅ Payment History View
- ✅ Receipt View/Download

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: Bootstrap 5, JavaScript
- **Payment Gateway**: Razorpay PHP SDK
- **Security**: Password Hashing, SQL Injection Prevention, Session Protection

## Installation

### 1. Database Setup

1. Import the database schema:
   ```sql
   mysql -u root -p < db.sql
   ```
   Or manually execute the SQL file in phpMyAdmin.

2. Default admin credentials:
   - Username: `admin`
   - Password: `admin123`
   (Change this immediately after first login!)

### 2. Configuration

1. Update `includes/config.php` with your database credentials:
   ```php
   $dbhost = "localhost";
   $dbuser = "root";
   $dbpass = "";
   $dbname = "hostel_fees";
   ```

2. Update Razorpay API keys in `includes/config.php`:
   ```php
   define('RAZORPAY_KEY_ID', 'your_key_id');
   define('RAZORPAY_KEY_SECRET', 'your_key_secret');
   ```

### 3. File Structure

```
fees/
├── admin/              # Admin panel files
│   ├── index.php      # Admin login
│   ├── dashboard.php   # Admin dashboard
│   ├── students.php    # Student list
│   ├── payments.php    # Payment history
│   ├── receipts.php    # Receipt management
│   └── includes/
│       └── checklogin.php
├── includes/           # Shared files
│   ├── config.php     # Database & config
│   └── checklogin.php  # User authentication
├── assets/             # CSS, JS, images
├── home.php            # Home page
├── index.php           # User login
├── create-account.php  # User registration
├── forgot-password.php # Password recovery
├── dashboard.php       # User dashboard
├── admission.php       # Admission form
├── fees-payment.php    # Payment page
├── fees-history.php    # Payment history
├── download-receipt.php # Receipt download
├── create_order.php    # Razorpay order creation
├── verify_payment.php  # Payment verification
└── db.sql             # Database schema
```

## Usage

### For Students

1. **Registration**: Visit `create-account.php` to create an account
2. **Login**: Use `index.php` to login
3. **Admission**: Complete admission form with all required details
4. **Payment**: Pay fees in 3 instalments (₹15,000, ₹13,000, ₹10,000)
5. **History**: View payment history and download receipts

### For Admin

1. **Login**: Visit `admin/index.php`
2. **Dashboard**: View analytics and recent transactions
3. **Students**: View all student admissions
4. **Payments**: View all payment transactions
5. **Receipts**: View and download student receipts

## Security Features

- ✅ Password hashing using `password_hash()` and `password_verify()`
- ✅ Prepared statements to prevent SQL injection
- ✅ Session-based authentication
- ✅ Razorpay signature verification
- ✅ Access control (users cannot access admin pages, vice versa)

## Payment Instalments

- **1st Term**: ₹15,000
- **2nd Term**: ₹13,000
- **3rd Term**: ₹10,000

## Database Schema

### Tables
- `users` - User accounts
- `admission` - Student admission details
- `payments` - Payment transactions
- `receipts` - Payment receipts
- `admin` - Admin accounts

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Razorpay account (for payment gateway)

## Notes

- All passwords are securely hashed
- Payment verification includes Razorpay signature verification
- Receipts are generated on-demand
- The system is fully responsive and mobile-friendly

## Support

For issues or questions, please contact the administration.

---

© 2025 Arul Anandar Hostel – Online Fees Payment Portal
