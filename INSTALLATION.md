# Installation Guide

## Quick Setup Steps

### 1. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE hostel_fees;"

# Import schema
mysql -u root -p hostel_fees < db.sql
```

Or use phpMyAdmin:
1. Create database `hostel_fees`
2. Import `db.sql` file

### 2. Install Razorpay SDK

#### Method A: Using Composer (Recommended)

```bash
# Install Composer (if not installed)
# Download from https://getcomposer.org/download/

# Install Razorpay SDK
composer install
```

#### Method B: Manual Installation

1. Download Razorpay PHP SDK:
   ```bash
   git clone https://github.com/razorpay/razorpay-php.git
   ```
   
2. Move the folder to your project root:
   ```bash
   mv razorpay-php /path/to/your/project/
   ```

### 3. Configure Application

Edit `includes/config.php`:

```php
// Database
$dbhost = "localhost";
$dbuser = "root";           // Your MySQL username
$dbpass = "";               // Your MySQL password
$dbname = "hostel_fees";

// Razorpay (Get from https://dashboard.razorpay.com/app/keys)
define('RAZORPAY_KEY_ID', 'rzp_test_xxxxxxxxxxxxx');
define('RAZORPAY_KEY_SECRET', 'your_secret_key_here');
```

### 4. Get Razorpay Keys

1. Sign up at https://razorpay.com/
2. Go to Dashboard → Settings → API Keys
3. Copy Key ID and Key Secret
4. Paste in `includes/config.php`

**Test Mode Keys** (for development):
- Start with `rzp_test_`
- Use test cards from Razorpay docs

**Live Mode Keys** (for production):
- Start with `rzp_live_`
- Use real payment methods

### 5. Test the Application

1. Start your web server (XAMPP/WAMP)
2. Navigate to: `http://localhost/fees/`
3. Create a test account
4. Complete admission form
5. Try making a test payment

## Troubleshooting

### "Razorpay SDK not found" Error

- Ensure Razorpay SDK is installed (Composer or manual)
- Check file paths in `create_order.php` and `verify_payment.php`
- Verify `vendor/autoload.php` exists (if using Composer)

### Database Connection Error

- Check MySQL service is running
- Verify credentials in `includes/config.php`
- Ensure database `hostel_fees` exists

### Payment Not Working

- Verify Razorpay keys are correct
- Check browser console for JavaScript errors
- Ensure amount is in paise (e.g. ₹100 = 10000 paise)
- Test with Razorpay test cards

### Session Issues

- Check `session_start()` on all pages
- Clear browser cookies
- Check PHP session configuration

## Test Cards (Razorpay Test Mode)

Use these cards for testing:

### New Admission & PDF Receipt (extra steps)

After the recent changes you must run the following to enable the "New Admission" table and automatic PDF receipts:

1. Import the migration SQL to add `new_admission` and `is_confirmed` column:

```bash
mysql -u root -p hostel_fees < sql/new_admission_migration.sql
```

2. Install the PDF generator dependency (Dompdf) via Composer:

```bash
composer require dompdf/dompdf
```

3. Ensure the `receipts/pdfs` directory exists and is writable by the web server (it's included in the repo as `receipts/pdfs/.gitkeep`).

4. After a successful admission fee payment, a PDF receipt will be generated and automatically downloaded by the browser.

Note: If you do not run `composer install` or `composer require dompdf/dompdf`, the system will still record payments but will not auto-generate PDF receipts.

## Test Cards (Razorpay Test Mode)

Use these cards for testing:

**Success:**
- Card: 4111 1111 1111 1111
- CVV: Any 3 digits
- Expiry: Any future date

**Failure:**
- Card: 4000 0000 0000 0002
- CVV: Any 3 digits
- Expiry: Any future date

## Production Checklist

- [ ] Change to Live Mode Razorpay keys
- [ ] Update database credentials
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Disable PHP error display
- [ ] Use strong database passwords
- [ ] Set up regular backups
- [ ] Test all payment flows

