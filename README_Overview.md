# Laravel Form Filler – UA GSU Forms

Fill the official `.docx` templates with submitted form data and download as PDF.

## Requirements

```bash
composer require phpoffice/phpword
```

LibreOffice must be installed on the server for DOCX → PDF conversion:
```bash
# Ubuntu/Debian
sudo apt install libreoffice

# Or on shared hosting: use a service like CloudConvert API instead
```

## Setup

1. Copy the two template files into `storage/app/templates/`:
   - `FACILITIES-AND-UTILIZATION-FORM-TEMPLATE.docx`
   - `REPAIR-AND-MAINTENANCE-FORM-TEMPLATE.docx`

2. Copy the controller into `app/Http/Controllers/`

3. Add routes in `routes/web.php`:
```php
use App\Http\Controllers\GsuFormController;

Route::get('/forms/facilities', [GsuFormController::class, 'showFacilities']);
Route::post('/forms/facilities/download', [GsuFormController::class, 'downloadFacilities']);

Route::get('/forms/repair', [GsuFormController::class, 'showRepair']);
Route::post('/forms/repair/download', [GsuFormController::class, 'downloadRepair']);
```

## Placeholder Reference

### Facilities Form
| Placeholder | Field |
|---|---|
| `${control_no}` | Control Number |
| `${date_request}` | Date of Request |
| `${requester_name}` | Requester Name & Signature |
| `${requester_contact}` | Contact Number |
| `${date_activity}` | Date of Activity |
| `${time_activity}` | Time of Activity |
| `${purpose}` | Purpose |
| `${venues_selected}` | Comma-separated list of selected venues |
| `${venue_others}` | "Others – please specify" text |
| `${qty_table}` | Quantity: Table |
| `${qty_fan}` | Quantity: Electric Fan |
| `${qty_rostrum}` | Quantity: Rostrum |
| `${qty_flag}` | Quantity: Flag & School Color |
| `${qty_sound}` | Quantity: Sound |
| `${qty_led}` | Quantity: LED Wall |
| `${req_name}` | Name of Requisitioner |
| `${req_datetime}` | Requisitioner Date and Time |
| `${noted_name}` | Name of Head of Office |
| `${noted_datetime}` | Head of Office Date and Time |
| `${approved_name}` | Name of GSU Person in Charge |
| `${approved_datetime}` | GSU Person Date and Time |

### Repair & Maintenance Form
| Placeholder | Field |
|---|---|
| `${control_no}` | Control Number |
| `${date_request}` | Date of Request |
| `${requester_name}` | Requester Name & Signature |
| `${department}` | Department / Office |
| `${requester_contact}` | Contact Number |
| `${repair_location}` | Maintenance / Repair Location |
| `${cb_inperson}` | `[✓]` or `[ ]` for In-person |
| `${cb_phone}` | `[✓]` or `[ ]` for Phone |
| `${cb_maintenance}` | `[✓]` or `[ ]` for Maintenance |
| `${cb_repair}` | `[✓]` or `[ ]` for Repair |
| `${cb_other_services}` | `[✓]` or `[ ]` for Other Services |
| `${problem_description}` | Problem description / work details |
| `${cb_approved}` | `[✓]` or `[ ]` for Approved |
| `${cb_disapproved}` | `[✓]` or `[ ]` for Disapproved |
| `${preferred_time}` | Preferred time for maintenance/repair |
| `${gsu_personnel}` | Assigned GSU Personnel name |
| `${worker_name}` | Maintenance Worker Name |
| `${worker_datetime}` | Maintenance Worker Date and Time |
| `${remarks}` | Actual Work Done |
| `${enduser_name}` | End-user Name |
| `${enduser_datetime}` | End-user Date and Time |
