# Controller Method Documentation — UA Facility Management System

---

## 1. `AuthController`
**File:** `app/Http/Controllers/AuthController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `showLogin()` | GET | `/login` | If already authenticated, redirects to the role-appropriate dashboard (`admin.dashboard`, `college.dashboard`, `org.dashboard`, or `home`). Otherwise renders the login view. |
| `login(Request $request)` | POST | `/login` | Rate-limited login (max 5 attempts per IP per minute). Validates email + password, attempts authentication via `Auth::attempt`. On success: clears rate-limit cache, regenerates session, redirects by role. On failure: increments attempt counter, returns error. |
| `logout(Request $request)` | POST | `/logout` | Logs out the user, invalidates the session, regenerates the CSRF token, and redirects to the home page. |

---

## 2. `Admin\DashboardController`
**File:** `app/Http/Controllers/Admin/DashboardController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/admin/dashboard` | Renders the admin dashboard view (`admin.dashboard`). |

---

## 3. `Admin\FacilityController`
**File:** `app/Http/Controllers/Admin/FacilityController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/admin/facilities` | Lists all facilities ordered by name, paginated (10 per page). |
| `create()` | GET | `/admin/facilities/create` | Renders the facility creation form. |
| `store(Request $request)` | POST | `/admin/facilities` | Validates and creates a new facility. Fields: `name`, `location`, `owner_type` (gsu/college/org), `owner_college`, `description`, `is_active`, `availability_status` (available/unavailable/maintenance). Redirects with success message. |
| `edit(Facility $facility)` | GET | `/admin/facilities/{facility}/edit` | Renders the edit form for a specific facility. |
| `update(Request $request, Facility $facility)` | PUT | `/admin/facilities/{facility}` | Validates and updates a facility. Protects core seeded facilities from having their `owner_type`/`owner_college` changed. Prevents admin from changing `availability_status` on college-owned facilities. |
| `destroy(Facility $facility)` | DELETE | `/admin/facilities/{facility}` | Deletes the specified facility. |

---

## 4. `Admin\BookingController`
**File:** `app/Http/Controllers/Admin/BookingController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/admin/bookings` | Lists all bookings (with requester and facility), ordered by most recent `start_time`, paginated (20 per page). |
| `edit(Booking $booking)` | GET | `/admin/bookings/{booking}/edit` | Renders the booking edit form, loading all active facilities for the dropdown. |
| `calendar(Request $request)` | GET | `/admin/calendar` | Month calendar view. Accepts optional `?month=YYYY-MM` query param. Groups bookings by day and counts bookings per facility for the month (only approved/rescheduled). Renders `admin.calendar.index`. |
| `overview(Request $request)` | GET | `/admin/overview` | Monthly overview chart data. Returns a day-by-day count of approved/rescheduled bookings for the month. Used to render a line chart in `admin.overview.index`. |
| `update(Request $request, Booking $booking)` | PUT | `/admin/bookings/{booking}` | Admin reschedules/modifies an existing booking. Validates facility, date, start/end time, purpose, reason, and optional equipment quantities (monobloc, table, fan, rostrum, flag, sound, LED). Merges equipment into `additional_details` JSON, sets status to `rescheduled`. Sends a notification to the requester and to other admin accounts for audit. |
| `cancel(Request $request, Booking $booking)` | POST | `/admin/bookings/{booking}/cancel` | Cancels a booking. Requires a `reason`. Sets status to `cancelled`, stores cancel reason/by/timestamp in `additional_details`. Notifies the requester. |

---

## 5. `Admin\UserController`
**File:** `app/Http/Controllers/Admin/UserController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/admin/users` | Lists all users ordered by name, paginated (20 per page). |
| `create()` | GET | `/admin/users/create` | Renders the user creation form. |
| `store(Request $request)` | POST | `/admin/users` | Creates a new user account. Validates: `name`, `email` (unique), `password` (min 8, confirmed), `role` (admin/college_staff/org_staff), and optionally `college_name` or `organization_name` depending on role. Hashes password before storage. |

---

## 6. `Admin\FormSubmissionController`
**File:** `app/Http/Controllers/Admin/FormSubmissionController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/admin/forms/facilities` | Lists all `facilities_utilization` form submissions (with requester), ordered by newest first, paginated (15 per page). |
| `show(FormSubmission $submission)` | GET | `/admin/forms/facilities/{submission}` | Shows a single facilities utilization submission. Aborts 404 if type mismatch. Loads the associated facility from the payload's `facility_id`. |
| `approve(FormSubmission $submission)` | POST | `/admin/forms/facilities/{submission}/approve` | Sets submission status to `approved`. Notifies the requester to proceed to the GSU office to sign and finalize. |
| `disapprove(FormSubmission $submission)` | POST | `/admin/forms/facilities/{submission}/disapprove` | Sets submission status to `disapproved`. Notifies the requester. |
| `setBooking(FormSubmission $submission)` | POST | `/admin/forms/facilities/{submission}/set-booking` | Converts an approved submission into a `Booking`. Enforces: only approved submissions, at least 7 days lead time, facility not unavailable/maintenance, no overlapping approved bookings. Creates a booking with code `BKG-YYYYMMDDHHiiss-{facility_id}`, marks submission as `converted`, and notifies the requester. |

---

## 7. `Admin\FormReviewController`
**File:** `app/Http/Controllers/Admin/FormReviewController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | — | Lists all form submissions ordered by newest first (no pagination). Renders `admin.forms.index`. |
| `show($id)` | GET | — | Shows a single form submission by ID. |
| `approve(Request $request, $id)` | POST | — | Approves a form submission. Auto-generates a control number (`GSU-000001` format) and merges it into the payload. |
| `disapprove(Request $request, $id)` | POST | — | Disapproves a form submission. Stores the disapproval reason in the payload. |
| `setBooking($id)` | POST | — | Converts an approved form into a Booking. Checks for time-slot conflicts and facility availability. Creates the booking with equipment and control number in `additional_details`. Marks submission as `converted`. |
| `cancel($id)` | POST | — | Sets the submission status to `cancelled`. |

---

## 8. `Admin\FacilitiesFormPdfController`
**File:** `app/Http/Controllers/Admin/FacilitiesFormPdfController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `generate(FormSubmission $submission)` | GET | `/admin/forms/facilities/{submission}/pdf` | Generates a PDF (via DOCX template + LibreOffice) for an approved `facilities_utilization` submission. Maps the submission's JSON payload (control number, requester, date, time, purpose, venue, equipment quantities) onto a DOCX template using `PhpWord\TemplateProcessor`. Checks venue against a predefined facility-to-checkbox-key map to mark the correct venue checkbox. Falls back to returning the DOCX if LibreOffice PDF conversion fails. |
| `convertAndDownload(string $docxPath, string $baseName)` | — (private) | — | Converts a filled DOCX file to PDF using LibreOffice headless. Returns the PDF download response, or falls back to DOCX if conversion fails. Cleans up temp files. |

---

## 9. `GsuFormController`
**File:** `app/Http/Controllers/GsuFormController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `showFacilities()` | GET | `/forms/facilities` | Renders the Facilities and Utilization Form UI (for manual/admin filling). |
| `downloadFacilities(Request $request)` | POST | `/forms/facilities/download` | Validates form inputs (date, requester, contact, activity date/time, purpose). Maps selected venue checkboxes to labels. Loads the DOCX template, fills all placeholders (control number, requester info, venues, equipment quantities, signature blocks), saves to temp DOCX, and converts to PDF for download. Auto-generates a control number if not provided. |
| `showRepair()` | GET | `/forms/repair` | Renders the Repair and Maintenance Form UI. |
| `downloadRepair(Request $request)` | POST | `/forms/repair/download` | Validates repair form inputs (date, requester, department, contact, location, problem description). Fills the Repair and Maintenance DOCX template with checkboxes for submission method (in-person/phone) and service type (maintenance/repair/other). Auto-generates control number. Converts to PDF for download. |
| `convertAndDownload(string $docxPath, string $baseName)` | — (private) | — | Shared DOCX-to-PDF conversion via LibreOffice headless. Falls back to DOCX download if conversion fails. |
| `generateControlNumber(string $formType)` | — (private) | — | Generates a sequential control number in format `GSU-YYYYMMDD-XXXX`. Looks up the last control number for today from the `FormControl` model and increments. Stores the new control number in the database. |

---

## 10. `PublicCalendarController`
**File:** `app/Http/Controllers/PublicCalendarController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index(Request $request)` | GET | `/` (home) | Public-facing month calendar. Accepts optional `?month=YYYY-MM`. Shows all approved/rescheduled bookings grouped by day. No authentication required. |

---

## 11. `NotificationController`
**File:** `app/Http/Controllers/NotificationController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/notifications` | Lists all notifications for the authenticated user, ordered newest first, paginated (20 per page). |
| `markAsRead(Notification $notification)` | POST | `/notifications/{notification}/read` | Marks a notification as read. Aborts 403 if the notification doesn't belong to the current user. |

---

## 12. `College\DashboardController`
**File:** `app/Http/Controllers/College/DashboardController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/college/dashboard` | Renders the college staff dashboard view (`college.dashboard`). |

---

## 13. `College\FacilityController`
**File:** `app/Http/Controllers/College/FacilityController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/college/facilities` | Lists facilities owned by the logged-in user's college (`owner_type=college`, `owner_college` matches user's `college_name`), paginated (10 per page). |
| `create()` | GET | `/college/facilities/create` | Renders the facility creation form, pre-filling the college name. |
| `store(Request $request)` | POST | `/college/facilities` | Creates a new college-owned facility. Validates name, location, description, is_active, availability_status. Forces `owner_type=college`, sets `is_active=false` and `availability_status=unavailable` (new college facilities require GSU verification before activation). |
| `edit(Facility $facility)` | GET | `/college/facilities/{facility}/edit` | Renders the edit form. Aborts 403 if the facility doesn't belong to the user's college. |
| `update(Request $request, Facility $facility)` | PUT | `/college/facilities/{facility}` | Updates a college-owned facility. Aborts 403 if ownership doesn't match. |
| `destroy(Facility $facility)` | DELETE | `/college/facilities/{facility}` | Deletes a college-owned facility. Aborts 403 if ownership doesn't match. |

---

## 14. `College\BookingController`
**File:** `app/Http/Controllers/College/BookingController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `calendar(Request $request)` | GET | `/college/calendar` | Read-only month calendar for college staff. Shows bookings the user requested plus bookings for any facility owned by their college. Includes per-facility booking counts for the month. Accepts optional `?month=YYYY-MM`. |

---

## 15. `College\FormController`
**File:** `app/Http/Controllers/College/FormController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `createFacilities()` | GET | `/college/requests/facilities` | Renders the Facilities Utilization request form. Loads GSU-managed facilities and the user's college-owned active facilities for the dropdown. |
| `storeFacilities(Request $request)` | POST | `/college/requests/facilities` | Submits a facilities utilization request. Validates: `date_activity` (must be at least 7 days in the future), `start_time`, `end_time`, `facility_id`, `purpose`, and optional equipment quantities. Builds a JSON payload and creates a `FormSubmission` with status `pending`. Sends notifications to the requester (confirmation) and to all admin users (new request alert). |
| `indexFacilities()` | GET | `/college/requests` | Lists the current college staff user's own facilities utilization submissions, paginated (10 per page). |
| `showFacilities(FormSubmission $submission)` | GET | `/college/requests/facilities/{submission}` | Shows a single request's details. Aborts 404 if the submission type doesn't match or it doesn't belong to the current user. Loads the associated facility from the payload. |

---

## 16. `Org\DashboardController`
**File:** `app/Http/Controllers/Org/DashboardController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `index()` | GET | `/org/dashboard` | Renders the organization staff dashboard view (`org.dashboard`). |

---

## 17. `Org\BookingController`
**File:** `app/Http/Controllers/Org/BookingController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `calendar(Request $request)` | GET | `/org/bookings` | Read-only month calendar for org staff. Shows only the bookings requested by the current user. Accepts optional `?month=YYYY-MM`. |

---

## 18. `Org\FormController`
**File:** `app/Http/Controllers/Org/FormController.php`

| Method | HTTP | Route | Description |
|---|---|---|---|
| `createFacilities()` | GET | `/org/requests/facilities` | Renders the Facilities Utilization request form for org staff. Loads GSU-managed facilities and the user's organization-owned active facilities for the dropdown. |
| `storeFacilities(Request $request)` | POST | `/org/requests/facilities` | Submits a facilities utilization request. Same validation as College (7-day lead time, equipment, etc.). Builds JSON payload with `requester_unit` set to the user's `organization_name`. Creates `FormSubmission` with `requester_type=org`. Notifies requester and all admins. |
| `indexFacilities()` | GET | `/org/requests/facilities/index` | Lists the current org staff user's own facilities utilization submissions, paginated (10 per page). |
