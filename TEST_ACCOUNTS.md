# Test user accounts

Role-based login accounts for the current web app are now seeded by `database/seeders/SampleUsersSeeder.php`. The legacy desktop launcher flow used helper routes for quick test account creation, but those helper routes are development-only and should not be used in production.

## Summary

| Source | What it creates | Used for portal login? |
|--------|-----------------|------------------------|
| `DatabaseSeeder` | Sample admin, college, org users A/B/C + generic test user + facilities | Yes (admins/college/org staff) |
| Helper routes in `routes/web.php` | Legacy, development-only account creation | Yes (legacy only) |

For standard web deployment, use seeded users instead of the legacy `/make-*` helper routes.

---

## 1. Seeder-created sample users

`database/seeders/DatabaseSeeder.php` runs when you execute:

```bash
php artisan db:seed
```

It now creates the following accounts:

### Admins
- `adminA@example.com` / `password123`
- `adminB@example.com` / `password123`
- `adminC@example.com` / `password123`

### College staff
- `collegeA@example.com` / `password123` → `college_name = "College A"`
- `collegeB@example.com` / `password123` → `college_name = "College B"`
- `collegeC@example.com` / `password123` → `college_name = "College C"`

### Org staff
- `orgA@example.com` / `password123` → `organization_name = "Org A"`
- `orgB@example.com` / `password123` → `organization_name = "Org B"`
- `orgC@example.com` / `password123` → `organization_name = "Org C"`

It also still creates one generic test user via `User::factory()` and the official facilities list via `FacilitySeeder`.

---

## 2. Legacy helper routes

The `/make-*` helper routes in `routes/web.php` are development-only and supported only for the legacy desktop launcher flow. They should not be relied on in production.

If you are using the current web-only portal, use seeded users or your normal user management process instead.

---

## Typical local setup order

```bash
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

For standard deployment, you do not need to visit `/make-admin`, `/make-college-staff`, or `/make-org-staff`.

---

## Security note

Remove or protect the `/make-*` routes before production deployment. They are intended for development and testing only.

## Related documentation

- `QUICKSTART.md` — full local setup flow
- `LAUNCHER_README.md` — launcher keys and build steps

