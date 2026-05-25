# UA Facility Management System — Desktop Launchers

## Purpose

Desktop launchers give authorized staff a controlled way to reach the hidden portal login (`/fms-portal-entry`). Each role has its own launcher:

| Launcher | Source file | Role query param | Access key env var | Default key |
|----------|-------------|------------------|--------------------|-------------|
| Admin | `launchers/admin_launcher.py` | `admin` | `FMS_ADMIN_SECRET` | `UA-ADMIN-2025` |
| College staff | `launchers/college_launcher.py` | `college_staff` | `FMS_COLLEGE_SECRET` | `UA-COLLEGE-2025` |
| Organization staff | `launchers/org_launcher.py` | `org_staff` | `FMS_ORG_SECRET` | `UA-ORG-2025` |

Launchers use **pywebview** (embedded window, not the system browser).

## How it works

1. A small local gate window asks for the role-specific access key.
2. The key is checked against the launcher secret (from environment variables at build/run time).
3. On success, a second window opens the Laravel portal URL:
   `{FMS_LOGIN_URL}?access_token={FMS_ACCESS_TOKEN}&role={ROLE}`
4. The user signs in with normal Laravel credentials; role middleware enforces the correct dashboard.

Laravel must expose the same `FMS_ACCESS_TOKEN` and `FMS_LOGIN_URL` in `.env` (see `.env.example`).

## Prerequisites

- Python 3.8+ 
- Laravel backend running (default: `http://127.0.0.1:8000`)
- Dependencies: `pip install pywebview` (optional on Windows: `cefpython3` for the CEF backend used by the launchers)

## Development (run from source)

From the repository root, with Laravel serving on port 8000:

```bash
python launchers/admin_launcher.py
python launchers/college_launcher.py
python launchers/org_launcher.py
```

Optional overrides (PowerShell):

```powershell
$env:FMS_LOGIN_URL = "http://127.0.0.1:8000/fms-portal-entry"
$env:FMS_ACCESS_TOKEN = "UA-FMS-ACCESS-2025"
$env:FMS_ADMIN_SECRET = "UA-ADMIN-2025"
```

### Test users

`php artisan db:seed` only adds `test@example.com` (no role) and facilities. For portal login, create role users via helper routes while Laravel is running:

| Role | Create account (open in browser) | Email | Password |
|------|----------------------------------|-------|----------|
| Admin | http://127.0.0.1:8000/make-admin | admin@example.com | password123 |
| College staff | http://127.0.0.1:8000/make-college-staff | college@example.com | password123 |
| Org staff | http://127.0.0.1:8000/make-org-staff | org@example.com | password123 |

See `TEST_ACCOUNTS.md` for how seeding and `/make-*` routes differ.

## Environment variables

| Variable | Used by | Purpose |
|----------|---------|---------|
| `FMS_LOGIN_URL` | Laravel + all launchers | Base portal entry URL (no query string) |
| `FMS_ACCESS_TOKEN` | Laravel + all launchers | Token in `?access_token=` |
| `FMS_ADMIN_SECRET` | Admin launcher | Gate key |
| `FMS_COLLEGE_SECRET` | College launcher | Gate key |
| `FMS_ORG_SECRET` | Org launcher | Gate key |
| `FMS_LAUNCHER_SECRET` | Legacy/fallback | Not used by current role launchers |

Values must match `.env` on the server. Change defaults before production.

## Building portable executables (PyInstaller)

Set production values in the shell **before** building so they are baked into the `.exe`:

```powershell
$env:FMS_LOGIN_URL = "https://your-domain.edu.ph/fms-portal-entry"
$env:FMS_ACCESS_TOKEN = "your-production-access-token"
$env:FMS_ADMIN_SECRET = "your-admin-secret"
$env:FMS_COLLEGE_SECRET = "your-college-secret"
$env:FMS_ORG_SECRET = "your-org-secret"
```

From the `launchers` directory:

```bash
cd launchers
pip install pyinstaller pywebview
python -m PyInstaller --onefile --name "UA-FMS-Admin-Portal" admin_launcher.py
python -m PyInstaller --onefile --name "UA-FMS-College-Portal" college_launcher.py
python -m PyInstaller --onefile --name "UA-FMS-Org-Portal" org_launcher.py
```

Output: `launchers/dist/UA-FMS-*-Portal.exe`

Helper script (from repo root): `dump\build_launchers.bat`

## Distribution checklist

When giving launchers to staff:

- [ ] Distribute only the `.exe` for their role (Admin / College / Org).
- [ ] Share the matching access key out of band (not in email with the binary).
- [ ] Confirm production `FMS_LOGIN_URL` and tokens were set at build time (or document how to set env vars if you distribute scripts instead of `.exe`).
- [ ] Provide Laravel login instructions (university accounts, not launcher keys).
- [ ] Point support staff to `QUICKSTART.md` and `DEPLOYMENT_GUIDE.md`.

## Security notes

- Portal route `/fms-portal-entry` is not linked from the public site.
- Launcher key + URL token + Laravel login form three layers; RBAC is enforced server-side.
- Use HTTPS in production for `FMS_LOGIN_URL`.
- Rotate `FMS_ACCESS_TOKEN` and role secrets periodically; rebuild and redistribute launchers after rotation.

## Troubleshooting

**Launcher cannot reach the server**

- Confirm `php artisan serve` (or production web server) is up.
- Match `FMS_LOGIN_URL` and `FMS_ACCESS_TOKEN` in Laravel `.env` and in the launcher build environment.

**Invalid key at gate**

- Use the secret for that role (`FMS_ADMIN_SECRET`, etc.), not `FMS_ACCESS_TOKEN`.

**pywebview / WebView errors on Windows**

- Install `cefpython3` so the launcher can use the CEF backend (`webview.start(gui='cef')`).
- If CEF is unavailable, the launcher falls back to the default GUI (Edge/WebView2).
- Reinstall pywebview: `pip uninstall pywebview && pip install pywebview`

**Check variables (PowerShell)**

```powershell
$env:FMS_LOGIN_URL
$env:FMS_ACCESS_TOKEN
$env:FMS_ADMIN_SECRET
```

## Android (organization staff only)

`org_launcher.py` does not build to APK on Windows. Use the native org app:

- Source: `android/ua-fms-org/`
- Guide: `android/ua-fms-org/README.md`

Configure `FMS_LOGIN_URL`, `FMS_ACCESS_TOKEN`, and `FMS_ORG_SECRET` in `app/build.gradle.kts`, then build APK in Android Studio.

## Related documentation

- `DEPLOYMENT_GUIDE.md` — production Laravel and token setup
- `QUICKSTART.md` — local setup in under 10 minutes
- `.env.example` — all `FMS_*` settings
- `android/ua-fms-org/README.md` — org APK build steps
- `TEST_ACCOUNTS.md` — test user creation (seeder vs helper routes)
