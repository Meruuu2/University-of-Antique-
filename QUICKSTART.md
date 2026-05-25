# Quick Start Guide - UA Facility Management System

This guide will get you up and running in under 10 minutes.

## Prerequisites

- Python 3.8+ (for desktop launchers)
- PHP 8.1+ (for Laravel backend)
- Composer (PHP dependency manager)
- Node.js (optional, for frontend assets)

## Step 1: Set Up Laravel Backend

### 1.1 Copy Environment File
```bash
copy .env.example .env
```

### 1.2 Install PHP Dependencies
```bash
composer install
```

### 1.3 Generate Application Key
```bash
php artisan key:generate
```

### 1.4 Set Up Database (SQLite for development)
```bash
type nul > database/database.sqlite
php artisan migrate
```

### 1.5 Start Laravel Development Server
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Laravel is now running at: http://127.0.0.1:8000

## Step 2: Set Up Desktop Launchers

### 2.1 Install Python Dependencies
```bash
pip install pywebview
```

### 2.2 Test Launchers (Development Mode)

Open three separate terminals and run each launcher:

Terminal 1 - Admin Portal:
```bash
python launchers/admin_launcher.py
```
Access key: UA-ADMIN-2025

Terminal 2 - College Staff Portal:
```bash
python launchers/college_launcher.py
```
Access key: UA-COLLEGE-2025

Terminal 3 - Organization Staff Portal:
```bash
python launchers/org_launcher.py
```
Access key: UA-ORG-2025

Launchers are now running. Each will open a login window.

## Step 3: Create Test Users

See **`TEST_ACCOUNTS.md`** for full detail. Short version:

### 3.1 Run database seeder (facilities + generic user only)

```bash
php artisan db:seed
```

This seeds the facility list and one generic user (`test@example.com` / `password`, **no role**). It does **not** create admin, college, or org portal accounts.

### 3.2 Create role accounts via helper routes

With Laravel running (`php artisan serve`), open each URL once in a browser:

| Role | URL |
|------|-----|
| Admin | http://127.0.0.1:8000/make-admin |
| College staff | http://127.0.0.1:8000/make-college-staff |
| Org staff | http://127.0.0.1:8000/make-org-staff |

### 3.3 Portal login credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password123 |
| College staff | college@example.com | password123 |
| Org staff | org@example.com | password123 |

### 3.4 Test the login flow

1. Launcher opens — enter access key (e.g. `UA-ADMIN-2025`)
2. WebView loads — Laravel login page
3. Enter credentials from the table above
4. Redirected to the role dashboard

## Step 4: Build Standalone .exe Files (Optional)

To create portable launchers that don't need Python installed:

### 4.1 Install PyInstaller
```bash
pip install pyinstaller
```

### 4.2 Build All Launchers
```bash
dump\build_launchers.bat
```

Or build individually:
```bash
pyinstaller --onefile --name "UA-FMS-Admin-Portal" launchers/admin_launcher.py
pyinstaller --onefile --name "UA-FMS-College-Portal" launchers/college_launcher.py
pyinstaller --onefile --name "UA-FMS-Org-Portal" launchers/org_launcher.py
```

Find your .exe files in the dist/ folder.

## Complete Workflow

### Daily Development:
1. Start Laravel: php artisan serve --host=127.0.0.1 --port=8000
2. Run launchers: python launchers/admin_launcher.py (and others)
3. Make changes - Refresh WebView or restart launcher

### Testing Different Roles:
- Admin: admin launcher + `admin@example.com` / `password123`
- College staff: college launcher + `college@example.com` / `password123`
- Org staff: org launcher + `org@example.com` / `password123`

## Troubleshooting

### Laravel won't start:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php -v  # Should be 8.1 or higher
```

### Database errors:
```bash
del database\database.sqlite
type nul > database\database.sqlite
php artisan migrate
php artisan db:seed
```

### Launcher can't connect:
- Make sure Laravel is running on http://127.0.0.1:8000
- Check .env has: FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
- Verify access token matches: FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

### pywebview import errors:
```bash
pip uninstall pywebview
pip install pywebview
```

## Testing on LAN (Other Devices)

### 1. Find Your IP Address
```bash
ipconfig
```

### 2. Update .env
```
APP_URL=http://192.168.0.10:8000
FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
```

### 3. Start Laravel on Your IP
```bash
php artisan serve --host=192.168.0.10 --port=8000
```

### 4. Set Environment Variables on Other Machines
```bash
set FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
set FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
```

### 5. Run Launchers
They'll now connect to your machine instead of localhost.

## Next Steps

Once you're comfortable with the basics:

1. Read `DEPLOYMENT_GUIDE.md` — production deployment
2. Read `TEST_ACCOUNTS.md` — seeder vs `/make-*` user setup
3. Customize tokens — change default secrets in `.env`
4. Build production launchers — PyInstaller with production env vars
5. Android org app — `android/ua-fms-org/README.md`

## Quick Reference

### Important Files:
- .env - Configuration (URLs, tokens, database)
- launchers/ - Desktop app source code
- app/Http/Controllers/ - Backend logic
- resources/views/ - Frontend templates

### Important Commands:
```bash
# Laravel
php artisan serve              # Start development server
php artisan migrate            # Run database migrations
php artisan db:seed            # Facilities + generic test@example.com user
# Then visit /make-admin, /make-college-staff, /make-org-staff (see TEST_ACCOUNTS.md)
php artisan tinker             # Interactive PHP console

# Launchers
python launchers/admin_launcher.py      # Run admin portal
python launchers/college_launcher.py    # Run college portal
python launchers/org_launcher.py        # Run org portal
dump\build_launchers.bat                # Build all .exe files
```

### Important docs:
- `TEST_ACCOUNTS.md` — how seeder vs `/make-*` routes create users

----------------------------------------------------------------------

