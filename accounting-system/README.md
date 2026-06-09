# سیستەمی ژمێریاری ژوانی گەشتیاری
## Jwani Travel Agency — Accounting System

A complete Kurdish-language accounting system built with Laravel 10 + MySQL.

---

## ✅ Features

- 👥 **Client Management** — Full CRUD for clients (customers, suppliers, individuals)
- 💰 **Transaction Ledger** — Sales, Purchases, Debits, Credits
- 🔒 **Immutable Exchange Rates** — Rate is locked inside each transaction at creation time; changing the global rate never alters historical records
- 🌍 **Multi-Currency** — USD + IQD with automatic cross-conversion
- 🖨️ **Thermal Receipt Printing** — Optimized 80mm receipt view
- 📊 **Excel Export** — Full ledger and per-client reports via `.xlsx`
- 🌑 **Dark Teal + Royal Blue + Gold** UI — RTL, Arabic/Kurdish font (Noto Kufi Arabic)
- 📱 **Fully Responsive**

---

## 🚀 Deployment to cPanel (Shared Hosting)

### Step 1 — Upload Files

Upload the entire project folder (everything **except** `public/`) to your hosting **outside** `public_html`, for example:

```
/home/yourusername/accounting/        ← all Laravel files here
/home/yourusername/public_html/       ← only public/ contents here
```

**Or** use a subdirectory:
```
/home/yourusername/accounting/        ← all Laravel files
/home/yourusername/public_html/app/   ← copy contents of public/ here
```

### Step 2 — Point the public folder

Copy everything **inside** `public/` into `public_html` (or your subdirectory).

Then edit `public_html/index.php` — update these two lines to point to your app location:

```php
require __DIR__.'/../accounting/vendor/autoload.php';
$app = require_once __DIR__.'/../accounting/bootstrap/app.php';
```

### Step 3 — Set up the database

1. In cPanel → **MySQL Databases**, create a new database and user.
2. Assign the user to the database with **All Privileges**.
3. Note down: database name, username, password.

### Step 4 — Configure .env

```bash
cp .env.example .env
```

Edit `.env`:
```env
APP_NAME="سیستەمی ژمێریاری"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_dbname
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=your_password
```

### Step 5 — Install Dependencies (via SSH or cPanel Terminal)

```bash
cd /home/yourusername/accounting
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6 — Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 644 .env
```

### Step 7 — Default Login Credentials

After seeding, log in with:
- **Email:** `admin@jwani.com`
- **Password:** `password`

⚠️ **Change the password immediately after first login!**

---

## 📁 Project Structure

```
accounting/
├── app/
│   ├── Exports/          # Excel export classes
│   ├── Http/Controllers/ # All controllers
│   ├── Models/           # Eloquent models
│   └── Providers/
├── config/               # Laravel config files
├── database/
│   ├── migrations/       # DB schema
│   └── seeders/          # Initial data (admin user, sample clients)
├── resources/views/
│   ├── auth/             # Login page
│   ├── dashboard/        # Main dashboard
│   ├── clients/          # Client CRUD views
│   ├── transactions/     # Transaction views
│   ├── exchange-rates/   # Exchange rate management
│   ├── receipts/         # Printable receipt (thermal/A5)
│   ├── reports/          # Report & Excel export views
│   └── layouts/          # Master layout (RTL, dark theme)
├── routes/
│   └── web.php           # All routes
└── public/               # Web root → goes into public_html
    └── .htaccess
```

---

## 🔒 Exchange Rate Immutability

This is a critical financial accuracy feature.

When a transaction is saved:
1. The **current** exchange rate is read from the `exchange_rates` table
2. It is **copied and stored** inside the `transactions.exchange_rate_usd_to_iqd` column
3. Both `amount_usd` and `amount_iqd` are computed and stored permanently

When you later change the global exchange rate:
- Only **future** transactions use the new rate
- **All historical records remain unchanged** — their locked rate is preserved forever

This ensures financial reports are always accurate, regardless of market fluctuations.

---

## 💡 Tips

- Use cPanel's **File Manager** to edit `.env` directly if SSH is unavailable
- For **Excel exports**, the `php-zip` and `php-xml` extensions must be enabled (usually on by default on cPanel)
- For best font rendering, ensure the server has internet access to load Google Fonts (or download and serve locally)
- **Session driver** is set to `file` — works on all shared hosting without Redis

---

## 🛠️ Tech Stack

| Component | Technology |
|-----------|-----------|
| Framework | Laravel 10 |
| Database  | MySQL (via PDO) |
| UI/CSS    | Tailwind CSS (CDN) |
| Font      | Noto Kufi Arabic (Google Fonts) |
| Excel     | Maatwebsite/Laravel-Excel |
| PDF/Print | Browser native print (optimized CSS) |
| ORM       | Eloquent |
| Direction | RTL (Arabic/Kurdish) |
