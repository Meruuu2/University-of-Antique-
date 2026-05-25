# Views & Routes Documentation — UA Facility Management System

---

## Table of Contents

1. [Routes](#routes)
2. [Layout Views](#layout-views)
3. [Public Views](#public-views)
4. [Auth Views](#auth-views)
5. [Admin Views](#admin-views)
6. [College Staff Views](#college-staff-views)
7. [Organization Staff Views](#organization-staff-views)
8. [GSU PDF Form Views](#gsu-pdf-form-views)
9. [Notification Views](#notification-views)

---

## Routes

**File:** `routes/web.php`

All routes are defined in the web route file and use session-based authentication with role middleware.

### Public Routes (No Authentication)

| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/` | `home` | `PublicCalendarController@index` | Public facility schedule calendar (homepage) |
| GET | `/login` | `login` | `AuthController@showLogin` | Show login form |
| POST | `/login` | `login.post` | `AuthController@login` | Process login |
| POST | `/logout` | `logout` | `AuthController@logout` | Logout user |
| GET | `/healthz` | — | Closure | JSON health check endpoint |

### Authenticated Routes (All Roles)

| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/notifications` | `notifications.index` | `NotificationController@index` | View all notifications |
| POST | `/notifications/{notification}/read` | `notifications.read` | `NotificationController@markAsRead` | Mark a notification as read |

### Admin Routes (`auth` + `role:admin`)

#### Dashboard
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | Admin dashboard with quick links |

#### Facilities Management
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/admin/facilities` | `admin.facilities.index` | `Admin\FacilityController@index` | List all facilities |
| GET | `/admin/facilities/create` | `admin.facilities.create` | `Admin\FacilityController@create` | Show create facility form |
| POST | `/admin/facilities` | `admin.facilities.store` | `Admin\FacilityController@store` | Store new facility |
| GET | `/admin/facilities/{facility}/edit` | `admin.facilities.edit` | `Admin\FacilityController@edit` | Show edit facility form |
| PUT | `/admin/facilities/{facility}` | `admin.facilities.update` | `Admin\FacilityController@update` | Update facility |
| DELETE | `/admin/facilities/{facility}` | `admin.facilities.destroy` | `Admin\FacilityController@destroy` | Delete facility |

#### GSU PDF Forms
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/forms/facilities` | `forms.facilities.show` | `GsuFormController@showFacilities` | Show facilities utilization form (fillable HTML) |
| POST | `/forms/facilities/download` | `forms.facilities.download` | `GsuFormController@downloadFacilities` | Generate & download PDF |
| GET | `/forms/repair` | `forms.repair.show` | `GsuFormController@showRepair` | Show repair & maintenance form (fillable HTML) |
| POST | `/forms/repair/download` | `forms.repair.download` | `GsuFormController@downloadRepair` | Generate & download PDF |

#### Form Submission Review
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/admin/forms/facilities` | `admin.forms.facilities.index` | `Admin\FormSubmissionController@index` | List all utilization requests |
| GET | `/admin/forms/facilities/{submission}` | `admin.forms.facilities.show` | `Admin\FormSubmissionController@show` | View a single request |
| POST | `/admin/forms/facilities/{submission}/approve` | `admin.forms.facilities.approve` | `Admin\FormSubmissionController@approve` | Approve request |
| POST | `/admin/forms/facilities/{submission}/disapprove` | `admin.forms.facilities.disapprove` | `Admin\FormSubmissionController@disapprove` | Disapprove request |
| POST | `/admin/forms/facilities/{submission}/set-booking` | `admin.forms.facilities.set-booking` | `Admin\FormSubmissionController@setBooking` | Convert approved request to booking |
| GET | `/admin/forms/facilities/{submission}/pdf` | `admin.forms.facilities.pdf` | `Admin\FacilitiesFormPdfController@generate` | Generate PDF for approved request |

#### User Management
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/admin/users` | `admin.users.index` | `Admin\UserController@index` | List all users |
| GET | `/admin/users/create` | `admin.users.create` | `Admin\UserController@create` | Show create user form |
| POST | `/admin/users` | `admin.users.store` | `Admin\UserController@store` | Store new user |

#### Booking Management
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/admin/bookings` | `admin.bookings.index` | `Admin\BookingController@index` | List all bookings |
| GET | `/admin/bookings/{booking}/edit` | `admin.bookings.edit` | `Admin\BookingController@edit` | View/edit booking |
| POST | `/admin/bookings/{booking}` | `admin.bookings.update` | `Admin\BookingController@update` | Update booking (reschedule) |
| POST | `/admin/bookings/{booking}/cancel` | `admin.bookings.cancel` | `Admin\BookingController@cancel` | Cancel booking |
| GET | `/admin/calendar` | `admin.calendar` | `Admin\BookingController@calendar` | Monthly calendar view |
| GET | `/admin/overview` | `admin.overview` | `Admin\BookingController@overview` | Monthly overview chart |

### College Staff Routes (`auth` + `role:college_staff`)

#### Dashboard & Calendar
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/college/dashboard` | `college.dashboard` | `College\DashboardController@index` | College staff dashboard |
| GET | `/college/calendar` | `college.calendar` | `College\BookingController@calendar` | Read-only booking calendar |

#### Facilities
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/college/facilities` | `college.facilities.index` | `College\FacilityController@index` | List college facilities |
| GET | `/college/facilities/create` | `college.facilities.create` | `College\FacilityController@create` | Show create facility form |
| POST | `/college/facilities` | `college.facilities.store` | `College\FacilityController@store` | Store new college facility |
| GET | `/college/facilities/{facility}/edit` | `college.facilities.edit` | `College\FacilityController@edit` | Show edit facility form |
| PUT | `/college/facilities/{facility}` | `college.facilities.update` | `College\FacilityController@update` | Update college facility |
| DELETE | `/college/facilities/{facility}` | `college.facilities.destroy` | `College\FacilityController@destroy` | Delete college facility |

#### Bookings
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/college/bookings` | `college.bookings.index` | Closure (inline) | Booking requests placeholder page |

#### Facilities Utilization Requests
| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/college/requests` | `college.requests.index` | `College\FormController@indexFacilities` | List own utilization requests |
| GET | `/college/requests/facilities` | `college.requests.facilities.create` | `College\FormController@createFacilities` | Show new request form |
| POST | `/college/requests/facilities` | `college.requests.facilities.store` | `College\FormController@storeFacilities` | Submit new request |
| GET | `/college/requests/facilities/{submission}` | `college.requests.facilities.show` | `College\FormController@showFacilities` | View a single request |

### Organization Staff Routes (`auth` + `role:org_staff`)

| Method | URI | Route Name | Controller | Description |
|---|---|---|---|---|
| GET | `/org/dashboard` | `org.dashboard` | `Org\DashboardController@index` | Organization staff dashboard |
| GET | `/org/bookings` | `org.bookings.index` | `Org\BookingController@calendar` | Read-only booking calendar |
| GET | `/org/requests/facilities` | `org.requests.facilities.create` | `Org\FormController@createFacilities` | Show new request form |
| POST | `/org/requests/facilities` | `org.requests.facilities.store` | `Org\FormController@storeFacilities` | Submit new request |
| GET | `/org/requests/facilities/index` | `org.requests.facilities.index` | `Org\FormController@indexFacilities` | List own utilization requests |

### Development/Testing Routes (Temporary)

| Method | URI | Description |
|---|---|---|
| GET | `/make-admin` | Create/update admin test user (`admin@example.com`) |
| GET | `/make-college-staff` | Create/update college staff test user (`college@example.com`) |
| GET | `/make-org-staff` | Create/update org staff test user (`org@example.com`) |
| GET | `/make-user` | Create/update viewer test user (`user@example.com`) |

---

## Layout Views

### `layouts/app.blade.php`
**Path:** `resources/views/layouts/app.blade.php`

The root layout for the entire application. All other layouts extend this.

**Structure:**
- `<head>`: meta tags, page title (`@yield('title')`), Google Fonts (Google Sans Flex), Vite CSS/JS
- `<header>`: top navigation bar with:
  - Logo link ("UA Facility Management") — dynamically links to the user's dashboard based on role
  - Notification link with unread badge count
  - User name and role display
  - Logout button (POST form)
- `<main>`: flash message display (`status` and `error`), navigation buttons (Back + Refresh), content area (`@yield('content')`)
- `<footer>`: university branding text

**Sections:**
- `@yield('title')` — page title (defaults to app name)
- `@yield('main-class')` — custom main container CSS classes
- `@yield('content')` — page body content

---

### `layouts/admin.blade.php`
**Path:** `resources/views/layouts/admin.blade.php`

Extends `layouts.app`. Provides the admin portal shell with sidebar navigation.

**Sidebar Sections:**
| Section | Links |
|---|---|
| Overview | Dashboard |
| Facilities | All facilities, Add facility |
| Requests | Utilization requests, Bookings, Booking calendar, Monthly overview |
| GSU forms (PDF) | Facilities utilization form, Repair & maintenance form |
| Administration | Users, Create user |

**Content Section:** `@yield('admin-content')`

---

### `layouts/college.blade.php`
**Path:** `resources/views/layouts/college.blade.php`

Extends `layouts.app`. Provides the college staff portal shell with sidebar navigation.

**Sidebar Header:** "College Staff" + college name from user profile

**Sidebar Sections:**
| Section | Links |
|---|---|
| Overview | Dashboard, Booking calendar |
| Facilities | My facilities, Add facility |
| Bookings | Bookings |
| Requests | My requests, New request |

**Content Section:** `@yield('college-content')`

**Footer:** Displays signed-in user name

---

### `layouts/org.blade.php`
**Path:** `resources/views/layouts/org.blade.php`

Extends `layouts.app`. Provides the organization staff portal shell with sidebar navigation.

**Sidebar Header:** "Organization Staff" + organization name from user profile

**Sidebar Sections:**
| Section | Links |
|---|---|
| Overview | Dashboard |
| Bookings | Booking calendar |
| Requests | My requests, New request |

**Content Section:** `@yield('org-content')`

**Footer:** Displays organization name

---

### `layouts/partials/sidebar.blade.php`
**Path:** `resources/views/layouts/partials/sidebar.blade.php`

Reusable sidebar partial included by all role-specific layouts. Renders navigation dynamically from a `$sections` array.

**Parameters:**
| Parameter | Type | Description |
|---|---|---|
| `$portalTitle` | string | Sidebar header title (e.g., "GSU Admin") |
| `$portalSubtitle` | string/null | Subtitle (e.g., user name or college name) |
| `$sections` | array | Array of nav groups, each with `heading` and `links` |
| `$footer` | string/null | Optional HTML footer content |

**Active Link Detection:** Automatically highlights the current route by comparing `request()->routeIs()` against the link's `route` or `routes` pattern.

---

## Public Views

### `welcome.blade.php`
**Path:** `resources/views/welcome.blade.php`
**Route:** — (legacy placeholder; the actual homepage is `public/calendar.blade.php`)
**Layout:** `layouts.app`

A simple placeholder card with "Facility Schedule" heading and text stating this is a public page. Not actively used — superseded by the public calendar.

---

### `public/calendar.blade.php`
**Path:** `resources/views/public/calendar.blade.php`
**Route:** `GET /` → `home`
**Layout:** `layouts.app`

The actual public homepage. Shows a read-only monthly calendar of confirmed facility bookings.

**Features:**
- Month navigation (Prev / Next links)
- 7-column calendar grid (Mon–Sun) displaying bookings per day
- Each booking card shows: facility name, time range, purpose (truncated to 60 chars)
- Only approved and rescheduled bookings are shown
- For authenticated users: shows signed-in name and logout link
- For guests: shows a "Sign in" link

**Data Required:** `$currentMonth` (Carbon), `$days` (array keyed by date string → bookings)

---

## Auth Views

### `auth/login.blade.php`
**Path:** `resources/views/auth/login.blade.php`
**Route:** `GET /login` → `login`
**Layout:** `layouts.app`

Simple login form with email and password fields.

**Form Details:**
- Method: POST to `login.post` route
- Fields: email (required, autofocus), password (required)
- Error display: shows first validation error
- Submit button: "Login"

---

## Admin Views

### `admin/dashboard.blade.php`
**Path:** `resources/views/admin/dashboard.blade.php`
**Route:** `GET /admin/dashboard` → `admin.dashboard`
**Layout:** `layouts.admin`

Admin welcome dashboard with quick-access card links.

**Quick Access Cards:**
| Card | Links To | Description |
|---|---|---|
| Facilities | `admin.facilities.index` | Manage campus facilities and equipment |
| Utilization requests | `admin.forms.facilities.index` | Review and approve facility utilization submissions |
| Bookings | `admin.bookings.index` | View and adjust confirmed bookings |
| User management | `admin.users.index` | Create and manage portal accounts |

**GSU Forms Cards:**
| Card | Links To | Description |
|---|---|---|
| Facilities & utilization form | `forms.facilities.show` | Generate PDF for facility utilization requests |
| Repair & maintenance form | `forms.repair.show` | Generate PDF for repair and maintenance requests |

---

### `admin/facilities/index.blade.php`
**Path:** `resources/views/admin/facilities/index.blade.php`
**Route:** `GET /admin/facilities` → `admin.facilities.index`
**Layout:** `layouts.admin`

Paginated table listing all facilities with CRUD actions.

**Table Columns:** Name (with description), Location, Owner (GSU/College/Organization badge + college name), Availability (badge), Actions (Edit, Delete)

**Features:**
- "Add New Facility" button linking to create page
- Delete confirmation dialog
- Pagination links
- Empty state with CTA

---

### `admin/facilities/create.blade.php`
**Path:** `resources/views/admin/facilities/create.blade.php`
**Route:** `GET /admin/facilities/create` → `admin.facilities.create`
**Layout:** `layouts.admin`

Form to create a new facility.

**Form Fields:**
| Field | Type | Required | Notes |
|---|---|---|---|
| Facility Name | text | Yes | |
| Location | text | Yes | |
| Owner Type | select | Yes | Options: GSU, College, Organization |
| College Name | text | Conditional | Shown via JS when owner_type = "college" |
| Description | textarea | No | |
| Active | checkbox | — | Checked by default |
| Availability Status | select | — | Options: Available, Unavailable, Maintenance |

**Submits to:** POST `admin.facilities.store`

---

### `admin/facilities/edit.blade.php`
**Path:** `resources/views/admin/facilities/edit.blade.php`
**Route:** `GET /admin/facilities/{facility}/edit` → `admin.facilities.edit`
**Layout:** `layouts.admin`

Edit form for an existing facility. Pre-fills all fields from `$facility`. Same fields as create form.

**Additional Logic:**
- For college-owned facilities, the availability status field is read-only (controlled by the college).
- Uses PUT method via `@method('PUT')`

**Submits to:** PUT `admin.facilities.update`

**JavaScript:** Toggles college name field visibility based on owner_type selection.

---

### `admin/bookings/index.blade.php`
**Path:** `resources/views/admin/bookings/index.blade.php`
**Route:** `GET /admin/bookings` → `admin.bookings.index`
**Layout:** `layouts.admin`

Paginated table of all bookings in the system.

**Table Columns:** Code (booking_code), Requester (name + type + unit), Facility, When (date range), Status (badge), Action link ("View / Modify")

---

### `admin/bookings/edit.blade.php`
**Path:** `resources/views/admin/bookings/edit.blade.php`
**Route:** `GET /admin/bookings/{booking}/edit` → `admin.bookings.edit`
**Layout:** `layouts.admin`

Detailed view and edit form for a single booking.

**Displays:** Requester info (name, type, unit), current booking details (facility, times, status)

**Editable Fields:**
| Field | Type | Description |
|---|---|---|
| Facility | select | All facilities listed |
| Date of activity | date | Booking date |
| Start time | time | Start time |
| End time | time | End time |
| Purpose | textarea | Booking purpose |
| Equipment quantities | number inputs | Monobloc Chair, Table, Electric Fan, Rostrum, Flag, Sound, LED Wall |

**Actions:**
- Reschedule (submit form) → POST `admin.bookings.update`
- Cancel booking → POST `admin.bookings.cancel`

---

### `admin/calendar/index.blade.php`
**Path:** `resources/views/admin/calendar/index.blade.php`
**Route:** `GET /admin/calendar` → `admin.calendar`
**Layout:** `layouts.admin`

Full monthly calendar view of all approved bookings.

**Features:**
- Month navigation (Prev / Next)
- 7-column calendar grid (Mon–Sun)
- Each booking card shows: facility name, time range, purpose
- Below the calendar: "Facility booking overview" table showing each facility with its owner, availability status, and booking count for the month

---

### `admin/overview/index.blade.php`
**Path:** `resources/views/admin/overview/index.blade.php`
**Route:** `GET /admin/overview` → `admin.overview`
**Layout:** `layouts.admin`

Monthly booking analytics chart.

**Features:**
- Month navigation (Prev / Next)
- Canvas-based line chart (drawn with vanilla JS, no chart library)
- Shows bookings per day for approved and rescheduled bookings
- X-axis: day numbers, Y-axis: booking count
- Blue line with dot markers

---

### `admin/forms/facilities_index.blade.php`
**Path:** `resources/views/admin/forms/facilities_index.blade.php`
**Route:** `GET /admin/forms/facilities` → `admin.forms.facilities.index`
**Layout:** `layouts.admin`

Paginated table of all facilities utilization requests (from college and org staff).

**Table Columns:** ID, Requester (name), Unit, Status (color-coded badge: yellow=pending, green=approved, red=disapproved), Submitted (date), Actions ("View" link)

---

### `admin/forms/facilities_show.blade.php`
**Path:** `resources/views/admin/forms/facilities_show.blade.php`
**Route:** `GET /admin/forms/facilities/{submission}` → `admin.forms.facilities.show`
**Layout:** `layouts.admin`

Detailed view of a single utilization request with action buttons.

**Displays:** Requester info, status, date of request, date of activity, time range, facility, purpose, equipment table (item + quantity for non-zero items)

**Actions (conditional):**
| Condition | Actions |
|---|---|
| Status = `pending` | Approve button, Disapprove button |
| Status = `approved` | Generate PDF button, "Convert to Booking" button |

---

### `admin/forms/index.blade.php`
**Path:** `resources/views/admin/forms/index.blade.php`
**Route:** — (legacy/alternate form submissions review page)
**Layout:** `layouts.app`

Tabbed view of form submissions organized by status (Pending, Approved, Converted, Disapproved). Uses Bootstrap-style tabs and includes `admin.forms._table` partial for each tab.

---

### `admin/forms/show.blade.php`
**Path:** `resources/views/admin/forms/show.blade.php`
**Route:** — (legacy/alternate single submission review page)
**Layout:** `layouts.app`

Detailed view of a form submission with Bootstrap card styling. Shows all payload fields, status badge, equipment list, disapproval reason (if applicable), and action buttons.

---

### `admin/forms/_table.blade.php`
**Path:** `resources/views/admin/forms/_table.blade.php`

Reusable partial for rendering a table of form submissions. Used by `admin/forms/index.blade.php`.

**Table Columns:** ID, Requester (name + type + unit), Type, Date Activity, Facility (resolved from `facility_id`), Actions ("View" link)

---

### `admin/users/index.blade.php`
**Path:** `resources/views/admin/users/index.blade.php`
**Route:** `GET /admin/users` → `admin.users.index`
**Layout:** `layouts.admin`

Paginated table of all system users.

**Table Columns:** Name, Email, Role (color-coded badge: purple=admin, blue=college_staff, green=org_staff, gray=viewer), Unit (college or organization name), Created date

---

### `admin/users/create.blade.php`
**Path:** `resources/views/admin/users/create.blade.php`
**Route:** `GET /admin/users/create` → `admin.users.create`
**Layout:** `layouts.admin`

Form to create a new user.

**Form Fields:**
| Field | Type | Required | Notes |
|---|---|---|---|
| Name | text | Yes | |
| Email | email | Yes | |
| Password | password | Yes | Min 8 characters |
| Confirm Password | password | Yes | Must match password |
| Role | select | Yes | Options: Admin, College Staff, Organization Staff |
| College Name | text | Conditional | Shown when role = "college_staff" |
| Organization Name | text | Conditional | Shown when role = "org_staff" |

**JavaScript:** Toggles college/org name fields based on selected role.

**Submits to:** POST `admin.users.store`

---

## College Staff Views

### `college/dashboard.blade.php`
**Path:** `resources/views/college/dashboard.blade.php`
**Route:** `GET /college/dashboard` → `college.dashboard`
**Layout:** `layouts.college`

College staff welcome dashboard with quick-access cards.

**Quick Access Cards:**
| Card | Links To | Description |
|---|---|---|
| My facilities | `college.facilities.index` | View and manage facilities owned by your college |
| Bookings | `college.bookings.index` | Review booking requests for your facilities |
| Utilization request | `college.requests.facilities.create` | Submit a facilities utilization request to GSU |

---

### `college/facilities/index.blade.php`
**Path:** `resources/views/college/facilities/index.blade.php`
**Route:** `GET /college/facilities` → `college.facilities.index`
**Layout:** `layouts.college`

Paginated table of facilities belonging to the user's college.

**Table Columns:** Name (+ description), Location, Availability (color-coded: green=Available, yellow=Maintenance, red=Unavailable + "Pending GSU verification" if inactive), Actions (Edit, Delete)

---

### `college/facilities/create.blade.php`
**Path:** `resources/views/college/facilities/create.blade.php`
**Route:** `GET /college/facilities/create` → `college.facilities.create`
**Layout:** `layouts.college`

Form to add a new facility for the college. Shows "Adding facility for: [college name]".

**Form Fields:** Facility Name, Location, Description, Active (checkbox), Availability Status (select)

**Note:** Owner type is automatically set to `college` and `owner_college` is set to the user's college — these fields are not shown.

**Submits to:** POST `college.facilities.store`

---

### `college/facilities/edit.blade.php`
**Path:** `resources/views/college/facilities/edit.blade.php`
**Route:** `GET /college/facilities/{facility}/edit` → `college.facilities.edit`
**Layout:** `layouts.college`

Edit form for a college facility. Same fields as create. Pre-fills values from `$facility`.

**Submits to:** PUT `college.facilities.update`

---

### `college/bookings/index.blade.php`
**Path:** `resources/views/college/bookings/index.blade.php`
**Route:** `GET /college/bookings` → `college.bookings.index`
**Layout:** `layouts.college`

Placeholder page. Displays a message that booking management will be implemented here.

---

### `college/bookings/calendar.blade.php`
**Path:** `resources/views/college/bookings/calendar.blade.php`
**Route:** `GET /college/calendar` → `college.calendar`
**Layout:** `layouts.college`

Read-only monthly calendar showing bookings for the college's facilities.

**Features:**
- Month navigation (Prev / Next)
- 7-column calendar grid (Mon–Sun)
- Each booking card shows: facility name, time range, purpose
- "Facility booking overview" table below: facility name, availability status, booking count for the month

**Data Required:** `$currentMonth`, `$days`, `$collegeName`, `$facilityCounts`

---

### `college/requests/facilities_create.blade.php`
**Path:** `resources/views/college/requests/facilities_create.blade.php`
**Route:** `GET /college/requests/facilities` → `college.requests.facilities.create`
**Layout:** `layouts.college`

Form to submit a facilities utilization request to GSU.

**Form Fields:**
| Field | Type | Required | Notes |
|---|---|---|---|
| Date of Activity | date | Yes | |
| Start Time | time | Yes | |
| End Time | time | Yes | |
| Facility | select | Yes | Grouped: GSU Facilities, My College Facilities |
| Purpose | textarea | Yes | |
| Equipment (7 items) | number with ±1/±10 buttons | No | Monobloc Chair, Table, Electric Fan, Rostrum, Flag & School Color, Sound, LED Wall |

**JavaScript:** `adjustQty()` function for incrementing/decrementing equipment quantities with buttons.

**Submits to:** POST `college.requests.facilities.store`

---

### `college/requests/facilities_index.blade.php`
**Path:** `resources/views/college/requests/facilities_index.blade.php`
**Route:** `GET /college/requests` → `college.requests.index`
**Layout:** `layouts.college`

Paginated table of the college staff's own utilization requests.

**Table Columns:** Date Requested, Activity Date, Time, Purpose (truncated 50 chars), Status (badge), Actions ("View" link)

---

### `college/requests/facilities_show.blade.php`
**Path:** `resources/views/college/requests/facilities_show.blade.php`
**Route:** `GET /college/requests/facilities/{submission}` → `college.requests.facilities.show`
**Layout:** `layouts.college`

Read-only detail view of a single utilization request.

**Displays:** Requested on date, Status, Date of Activity, Time range, Facility (name + location), Purpose, Equipment table (non-zero quantities only)

---

### `college/forms/facilities.blade.php`
**Path:** `resources/views/college/forms/facilities.blade.php`
**Route:** — (legacy form submission page)
**Layout:** `layouts.app`

Older version of the facility utilization form using Bootstrap card/form styling. Includes facility dropdown, "Other" facility text field, equipment quantity inputs, and JavaScript for show/hide logic.

**Submits to:** POST `college.forms.store`

---

### `college/forms/index.blade.php`
**Path:** `resources/views/college/forms/index.blade.php`
**Route:** — (legacy submissions list)
**Layout:** `layouts.app`

Bootstrap-styled table listing the user's form submissions with control number, dates, facility, status badge, and view link.

---

### `college/forms/show.blade.php`
**Path:** `resources/views/college/forms/show.blade.php`
**Route:** — (legacy submission detail)
**Layout:** `layouts.app`

Bootstrap card showing detailed submission info. Includes approval/disapproval alerts.

---

## Organization Staff Views

### `org/dashboard.blade.php`
**Path:** `resources/views/org/dashboard.blade.php`
**Route:** `GET /org/dashboard` → `org.dashboard`
**Layout:** `layouts.org`

Simple organization staff welcome dashboard with placeholder text.

---

### `org/bookings/calendar.blade.php`
**Path:** `resources/views/org/bookings/calendar.blade.php`
**Route:** `GET /org/bookings` → `org.bookings.index`
**Layout:** `layouts.org`

Read-only monthly calendar showing bookings requested by the organization.

**Features:**
- Month navigation (Prev / Next)
- 7-column calendar grid (Mon–Sun)
- Each booking card: facility name, time range, purpose

**Data Required:** `$currentMonth`, `$days`

---

### `org/requests/facilities_index.blade.php`
**Path:** `resources/views/org/requests/facilities_index.blade.php`
**Route:** `GET /org/requests/facilities/index` → `org.requests.facilities.index`
**Layout:** `layouts.org`

Paginated table of the org staff's own utilization requests. Same structure as the college version.

**Table Columns:** Date Requested, Activity Date, Time, Purpose, Status (badge), Actions ("View" link)

---

## GSU PDF Form Views

These are standalone HTML pages (not using the app layout) designed to replicate the official University of Antique GSU paper forms. They are styled to look like printed documents with official university headers, and can be filled in the browser then downloaded as PDF.

### `forms/facilities.blade.php`
**Path:** `resources/views/forms/facilities.blade.php`
**Route:** `GET /forms/facilities` → `forms.facilities.show`
**Layout:** Standalone (no layout inheritance)

Full-page HTML replica of the **"Facilities and Utilization Form"** (GSU document).

**Sections:**
1. **Header:** Republic of the Philippines, University of Antique letterhead, address
2. **Control No. & Date Request:** fields at top-right
3. **Info Table:** Requesting Party, College/Org, Date of Activity, Time (From/To), Contact Person, Purpose
4. **Venue checkboxes:** 11 UA facilities as checkboxes (BUSALAN HALL, AVR-USA HALL, E-HUB, etc.) + "Others" text field
5. **Facilities/Equipment table:** Items (Monobloc Chair, Table, Electric Fan, Rostrum, Flag & School Color, Sound, LED Display) with quantity inputs
6. **Signature grid:** Three columns — Requested by, Noted by (Immediate Supervisor), Approved by (VP/Admin Director)
7. **Download button:** Submits POST to `forms.facilities.download`

**Styling:** Custom inline CSS using EB Garamond (serif) and Source Sans 3 (sans-serif). Navy blue (#1a3a6b) accent colors. Designed to print at letter size.

---

### `forms/repair.blade.php`
**Path:** `resources/views/forms/repair.blade.php`
**Route:** `GET /forms/repair` → `forms.repair.show`
**Layout:** Standalone (no layout inheritance)

Full-page HTML replica of the **"Repair and Maintenance Requisition Form"** (GSU document).

**Sections:**
1. **Header:** Same UA letterhead as facilities form
2. **Control No.:** auto-generated or manual
3. **Request Details:** Requesting Party, College/Org, Date, Tel No.
4. **Submission mode:** Radio buttons (In-person, Letter/phone call, Email, System-generated)
5. **Request Type:** Checkboxes (Electrical, Plumbing, Masonry, Carpentry, Welding, Others)
6. **Description:** Textarea for issue description
7. **Approval section:** Approved/Not Approved checkboxes, Reason field, Date
8. **GSU Personnel:** Staff name, preferred time fields
9. **Work Done table:** Multi-row table with Date, Work done, Remarks, Staff signature columns
10. **Completion section:** Date completed, inspected/confirmed by
11. **Download button:** Submits POST to `forms.repair.download`

**Styling:** Same styling approach as the facilities form — printed document replica.

---

## Notification Views

### `notifications/index.blade.php`
**Path:** `resources/views/notifications/index.blade.php`
**Route:** `GET /notifications` → `notifications.index`
**Layout:** `layouts.app`

Displays all notifications for the authenticated user, grouped into four categories.

**Notification Categories:**
| Category | Header | Notification Types |
|---|---|---|
| New / pending requests | "New / pending requests" | `form_pending`, `form_pending_admin` |
| Decisions on your requests | "Decisions on your requests" | `form_approved`, `form_disapproved` |
| Booking updates | "Booking updates" | `booking_created`, `booking_rescheduled`, `booking_cancelled` |
| Other | "Other notifications" | Any type not matching above |

**Each Category Table Columns:** Status (Read/New badge), Title, Message, When (date), Action ("Mark as read" button for unread items)

**Mark as Read:** POST form to `notifications.read` route

---

## View Hierarchy Summary

```
layouts/
├── app.blade.php               ← Root layout (header, nav, footer)
├── admin.blade.php             ← Extends app, adds admin sidebar
├── college.blade.php           ← Extends app, adds college sidebar
├── org.blade.php               ← Extends app, adds org sidebar
└── partials/
    └── sidebar.blade.php       ← Reusable sidebar partial

welcome.blade.php               ← Legacy homepage placeholder
public/calendar.blade.php       ← Public booking calendar (actual homepage)
auth/login.blade.php            ← Login page

admin/
├── dashboard.blade.php         ← Admin dashboard
├── facilities/
│   ├── index.blade.php         ← Facilities list
│   ├── create.blade.php        ← Add facility form
│   └── edit.blade.php          ← Edit facility form
├── bookings/
│   ├── index.blade.php         ← Bookings list
│   └── edit.blade.php          ← View/edit booking
├── calendar/
│   └── index.blade.php         ← Admin calendar view
├── overview/
│   └── index.blade.php         ← Monthly overview chart
├── forms/
│   ├── facilities_index.blade.php  ← Utilization requests list
│   ├── facilities_show.blade.php   ← Request detail + actions
│   ├── index.blade.php         ← Legacy tabbed submissions view
│   ├── show.blade.php          ← Legacy submission detail
│   └── _table.blade.php        ← Reusable submission table partial
└── users/
    ├── index.blade.php         ← Users list
    └── create.blade.php        ← Add user form

college/
├── dashboard.blade.php         ← College dashboard
├── facilities/
│   ├── index.blade.php         ← College facilities list
│   ├── create.blade.php        ← Add college facility
│   └── edit.blade.php          ← Edit college facility
├── bookings/
│   ├── index.blade.php         ← Placeholder page
│   └── calendar.blade.php      ← Read-only booking calendar
├── requests/
│   ├── facilities_create.blade.php  ← New utilization request form
│   ├── facilities_index.blade.php   ← Own requests list
│   └── facilities_show.blade.php    ← Request detail view
└── forms/                      ← Legacy form pages
    ├── facilities.blade.php
    ├── index.blade.php
    └── show.blade.php

org/
├── dashboard.blade.php         ← Org dashboard
├── bookings/
│   └── calendar.blade.php      ← Read-only booking calendar
└── requests/
    └── facilities_index.blade.php  ← Own requests list

forms/
├── facilities.blade.php        ← GSU Facilities Utilization PDF form
└── repair.blade.php            ← GSU Repair & Maintenance PDF form

notifications/
└── index.blade.php             ← Notifications page (all roles)
```
