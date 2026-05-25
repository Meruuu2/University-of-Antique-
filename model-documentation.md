# Database Model Documentation — UA Facility Management System

---

## 1. `User`
**File:** `app/Models/User.php`
**Table:** `users`

### Description
The central authentication model. Extends Laravel's `Authenticatable` base. Represents all system users — admins (GSU staff), college staff, organization staff, and viewers.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `name` | string | required | Full name |
| `email` | string | required, unique | Login email |
| `email_verified_at` | timestamp | nullable | Email verification timestamp |
| `password` | string | required, hashed | Bcrypt-hashed password |
| `remember_token` | string | nullable | "Remember me" session token |
| `role` | string | default `'viewer'` | One of: `admin`, `college_staff`, `org_staff`, `viewer` |
| `college_name` | string | nullable | College affiliation (used when `role = college_staff`) |
| `organization_name` | string | nullable | Organization affiliation (used when `role = org_staff`) |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`name`, `email`, `password`, `role`, `college_name`, `organization_name`

### Hidden
`password`, `remember_token`

### Casts
- `email_verified_at` → `datetime`
- `password` → `hashed`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `bookings()` | hasMany | `Booking` | `requester_id` | Bookings requested by this user |
| `maintenanceTickets()` | hasMany | `MaintenanceTicket` | `requester_id` | Maintenance tickets filed by this user |
| `staffAssignments()` | hasMany | `StaffAssignment` | `staff_id` | Maintenance tasks assigned to this staff member |
| `maintenanceLogs()` | hasMany | `MaintenanceLog` | `staff_id` | Work logs recorded by this staff member |
| `notifications()` | hasMany | `Notification` | `user_id` | Notifications for this user |

### Traits
`HasFactory`, `Notifiable`

---

## 2. `Facility`
**File:** `app/Models/Facility.php`
**Table:** `facilities`

### Description
Represents a bookable university facility (e.g., halls, AVRs, gyms, track oval). Facilities can be owned by GSU (university-wide) or by a specific college.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `name` | string | required | Facility name (e.g., "BUSALAN HALL") |
| `location` | string | nullable | Campus/building location |
| `owner_type` | string | default `'gsu'` | Ownership: `gsu`, `college`, or `org` |
| `owner_college` | string | nullable | College name (for college-owned facilities) |
| `description` | text | nullable | Description of the facility |
| `is_active` | boolean | default `true` | Whether the facility is active/visible |
| `availability_status` | string(20) | default `'available'` | One of: `available`, `unavailable`, `maintenance` |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`name`, `location`, `owner_type`, `owner_college`, `description`, `is_active`, `availability_status`

### Casts
- `is_active` → `boolean`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `bookings()` | hasMany | `Booking` | `facility_id` | All bookings for this facility |
| `maintenanceTickets()` | hasMany | `MaintenanceTicket` | `facility_id` | Maintenance tickets filed for this facility |

### Custom Methods
| Method | Returns | Description |
|---|---|---|
| `isCoreFacility()` | `bool` | Returns `true` if the facility name matches one of the 11 official seeded UA facilities (BUSALAN HALL, AVR-USA HALL, E-HUB, BALAY NI JUAN, ICT AVR, CEA AVR, CBA AVR, NEW AVR, GRAND STAND, COVERED GYM, TRACK OVAL). Used to prevent changing ownership of core facilities. |

### Traits
`HasFactory`

---

## 3. `Equipment`
**File:** `app/Models/Equipment.php`
**Table:** `equipment`

### Description
Represents equipment items that can be requested alongside facility bookings (e.g., monobloc chairs, tables, fans, rostrums, sound systems, LED displays).

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `name` | string | required | Equipment name (e.g., "Monobloc chair") |
| `category` | string | required | Category label (e.g., "seating", "sound") |
| `total_quantity` | integer | default `0` | Total available quantity |
| `is_active` | boolean | default `true` | Whether the equipment is active/available |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`name`, `category`, `total_quantity`, `is_active`

### Casts
- `total_quantity` → `integer`
- `is_active` → `boolean`

### Relationships
None defined.

### Traits
`HasFactory`

---

## 4. `Booking`
**File:** `app/Models/Booking.php`
**Table:** `bookings`

### Description
Represents a confirmed facility reservation. Created when an admin converts an approved form submission into a booking, or when an admin creates one directly.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `requester_id` | bigint | FK → `users.id`, cascade delete | User who requested the booking |
| `facility_id` | bigint | FK → `facilities.id`, cascade delete | Booked facility |
| `start_time` | datetime | required | Booking start date/time |
| `end_time` | datetime | required | Booking end date/time |
| `requester_type` | string | required | Type of requester: `college` or `org` |
| `requester_unit` | string | nullable | Unit name (e.g., "College of Engineering", "Student Council") |
| `status` | string | default `'pending'` | One of: `pending`, `approved`, `denied`, `rescheduled`, `cancelled` |
| `request_method` | string | nullable | How the request was made (e.g., `online_form`) |
| `purpose` | text | required | Purpose/reason for the booking |
| `additional_details` | text | nullable | JSON-encoded extra data (equipment quantities, reschedule notes, cancel reasons) |
| `requested_at` | datetime | nullable | When the original request was submitted |
| `approved_at` | datetime | nullable | When the booking was approved |
| `booking_code` | string | unique | Reference number (e.g., `BKG-20260521143000-5`) |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`requester_id`, `facility_id`, `start_time`, `end_time`, `requester_type`, `requester_unit`, `status`, `request_method`, `purpose`, `additional_details`, `requested_at`, `approved_at`, `booking_code`

### Casts
- `start_time` → `datetime`
- `end_time` → `datetime`
- `requested_at` → `datetime`
- `approved_at` → `datetime`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `requester()` | belongsTo | `User` | `requester_id` | The user who made the booking |
| `facility()` | belongsTo | `Facility` | `facility_id` | The booked facility |
| `maintenanceTickets()` | hasMany | `MaintenanceTicket` | `booking_id` | Maintenance tickets associated with this booking |

### Traits
`HasFactory`

---

## 5. `MaintenanceTicket`
**File:** `app/Models/MaintenanceTicket.php`
**Table:** `maintenance_tickets`

### Description
Represents a repair/maintenance request for a facility. Can optionally be linked to a specific booking. Tracks the ticket lifecycle from submission through approval to completion.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `facility_id` | bigint | FK → `facilities.id`, cascade delete | Facility needing maintenance |
| `requester_id` | bigint | FK → `users.id`, cascade delete | User who filed the ticket |
| `booking_id` | bigint | FK → `bookings.id`, nullable, null on delete | Related booking (if applicable) |
| `request_method` | string | required | How submitted: `in-person`, `phone`, `email`, `system` |
| `status` | string | default `'pending'` | One of: `pending`, `approved`, `rejected`, `in_progress`, `completed` |
| `issue_description` | text | required | Description of the issue/problem |
| `admin_remarks` | text | nullable | Admin notes/remarks |
| `requested_at` | datetime | nullable | When the ticket was submitted |
| `approved_at` | datetime | nullable | When the ticket was approved |
| `completed_at` | datetime | nullable | When the work was completed |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`facility_id`, `requester_id`, `booking_id`, `request_method`, `status`, `issue_description`, `admin_remarks`, `requested_at`, `approved_at`, `completed_at`

### Casts
- `requested_at` → `datetime`
- `approved_at` → `datetime`
- `completed_at` → `datetime`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `facility()` | belongsTo | `Facility` | `facility_id` | The facility the ticket is for |
| `requester()` | belongsTo | `User` | `requester_id` | The user who filed the ticket |
| `booking()` | belongsTo | `Booking` | `booking_id` | The related booking (if any) |
| `staffAssignments()` | hasMany | `StaffAssignment` | `maintenance_ticket_id` | Staff members assigned to this ticket |
| `maintenanceLogs()` | hasMany | `MaintenanceLog` | `maintenance_ticket_id` | Work logs for this ticket |

### Traits
`HasFactory`

---

## 6. `MaintenanceLog`
**File:** `app/Models/MaintenanceLog.php`
**Table:** `maintenance_logs`

### Description
Records the actual work performed on a maintenance ticket. Each log entry represents a unit of work done by a staff member.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `maintenance_ticket_id` | bigint | FK → `maintenance_tickets.id`, cascade delete | Parent ticket |
| `staff_id` | bigint | FK → `users.id`, cascade delete | Staff member who performed the work |
| `work_done` | text | required | Description of work performed |
| `remarks` | text | nullable | Additional notes |
| `logged_at` | timestamp | required | When the work was logged |
| `staff_signature` | string | required | Staff name or code (as signature) |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`maintenance_ticket_id`, `staff_id`, `work_done`, `remarks`, `logged_at`, `staff_signature`

### Casts
- `logged_at` → `datetime`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `maintenanceTicket()` | belongsTo | `MaintenanceTicket` | `maintenance_ticket_id` | The parent ticket |
| `staff()` | belongsTo | `User` | `staff_id` | The staff member who did the work |

### Traits
`HasFactory`

---

## 7. `StaffAssignment`
**File:** `app/Models/StaffAssignment.php`
**Table:** `staff_assignments`

### Description
Links a staff member to a maintenance ticket, representing the assignment of personnel to a repair/maintenance job.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `maintenance_ticket_id` | bigint | FK → `maintenance_tickets.id`, cascade delete | The maintenance ticket |
| `staff_id` | bigint | FK → `users.id`, cascade delete | The assigned staff member |
| `assigned_at` | datetime | required | When the assignment was made |
| `preferred_time` | datetime | nullable | Preferred time for the work |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`maintenance_ticket_id`, `staff_id`, `assigned_at`, `preferred_time`

### Casts
- `assigned_at` → `datetime`
- `preferred_time` → `datetime`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `maintenanceTicket()` | belongsTo | `MaintenanceTicket` | `maintenance_ticket_id` | The parent ticket |
| `staff()` | belongsTo | `User` | `staff_id` | The assigned staff member |

### Traits
`HasFactory`

---

## 8. `FormControl`
**File:** `app/Models/FormControl.php`
**Table:** `form_controls`

### Description
Tracks auto-generated control numbers for GSU forms. Each record stores a unique control number used to identify a specific form document (facilities utilization or repair/maintenance).

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `control_number` | string | unique | Control number in format `GSU-YYYYMMDD-XXXX` |
| `form_type` | string | required | Form type: `facilities` or `repair` |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`control_number`, `form_type`

### Casts
None.

### Relationships
None defined.

### Traits
None (extends base `Model`).

---

## 9. `FormSubmission`
**File:** `app/Models/FormSubmission.php`
**Table:** `form_submissions`

### Description
Tracks the submission through its lifecycle from pending to approved/disapproved and eventually booked into a booking.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `type` | string | required | Form type (e.g., `facilities_utilization`, `repair_maintenance`) |
| `requester_id` | bigint | FK → `users.id`, cascade delete | User who submitted the form |
| `requester_type` | string | required | Requester role: `college`, `org`, or `admin` |
| `requester_unit` | string | nullable | College name or organization name |
| `status` | string | default `'pending'` | One of: `pending`, `approved`, `disapproved`, `cancelled`, `booked` |
| `payload` | json | required | Full form data as JSON (see Payload Structure below) |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Typical Payload Structure (facilities_utilization)
```json
{
  "control_no": null,
  "date_request": "2026-05-21",
  "requester_name": "College Staff User",
  "requester_unit": "College of Engineering",
  "date_activity": "2026-06-01",
  "time_range": { "start": "08:00", "end": "12:00" },
  "facility_id": 3,
  "venue_others": null,
  "purpose": "Seminar on AI",
  "equipment": {
    "monobloc_chair": 50,
    "table": 5,
    "electric_fan": 3,
    "rostrum": 1,
    "flag": 0,
    "sound": 1,
    "led": 0
  }
}
```

### Fillable
`type`, `requester_id`, `requester_type`, `requester_unit`, `status`, `payload`

### Casts
- `payload` → `array`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `requester()` | belongsTo | `User` | `requester_id` | The user who submitted the form |

### Traits
`HasFactory`

---

## 10. `Notification`
**File:** `app/Models/Notification.php`
**Table:** `notifications`

### Description
In-app notifications sent to users about form status changes, booking updates, and other events. Supports read/unread status tracking.

### Schema

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | bigint (auto) | PK | Primary key |
| `user_id` | bigint | FK → `users.id`, cascade delete, indexed | Recipient user |
| `type` | string | required | Notification type (e.g., `form_pending`, `form_approved`, `form_disapproved`, `booking_rescheduled`, `booking_cancelled`, `booking_created`, `form_pending_admin`) |
| `title` | string | required | Short notification title |
| `message` | text | nullable | Notification body text |
| `data` | json | nullable | Additional data (e.g., `{"submission_id": 1, "booking_id": 3}`) |
| `is_read` | boolean | default `false`, indexed | Whether the user has read this notification |
| `created_at` | timestamp | auto | Record creation time |
| `updated_at` | timestamp | auto | Record update time |

### Fillable
`user_id`, `type`, `title`, `message`, `data`, `is_read`

### Casts
- `data` → `array`
- `is_read` → `boolean`

### Relationships
| Relationship | Type | Related Model | Foreign Key | Description |
|---|---|---|---|---|
| `user()` | belongsTo | `User` | `user_id` | The recipient user |

### Traits
None (extends base `Model`).

---

## Entity Relationship Summary

```
User ──┬── hasMany ──→ Booking (requester_id)
       ├── hasMany ──→ MaintenanceTicket (requester_id)
       ├── hasMany ──→ StaffAssignment (staff_id)
       ├── hasMany ──→ MaintenanceLog (staff_id)
       └── hasMany ──→ Notification (user_id)

Facility ──┬── hasMany ──→ Booking (facility_id)
           └── hasMany ──→ MaintenanceTicket (facility_id)

Booking ──── hasMany ──→ MaintenanceTicket (booking_id)

MaintenanceTicket ──┬── hasMany ──→ StaffAssignment (maintenance_ticket_id)
                    └── hasMany ──→ MaintenanceLog (maintenance_ticket_id)

FormSubmission ──── belongsTo ──→ User (requester_id)
FormControl ──── (standalone, no relationships)
Equipment ──── (standalone, no relationships)
```
