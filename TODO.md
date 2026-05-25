# TODO - Org booking calendar alignment (compact cells + daily details)

- [ ] Update `app/Http/Controllers/Org/BookingController.php`
  - [ ] Filter bookings to `status in ['booked','rescheduled']`
  - [ ] Add `selectedDate` + `selectedDateBookings` based on `?day=` query
  - [ ] Pass new variables to `org.bookings.calendar` view

- [ ] Update `resources/views/org/bookings/calendar.blade.php`
  - [ ] Replace old per-day inline details layout with compact cell UI
  - [ ] Each day cell shows `N bookings` link when bookings exist
  - [ ] When `selectedDate` is set, render daily details table (time/facilities/purpose/status)
  - [ ] Keep prev/next month links working using existing `month` query

- [ ] Verify routes + query params
  - [ ] Ensure `route('org.bookings.index', ['month'=>..., 'day'=>...])` matches existing route `/org/bookings`

- [ ] Run quick sanity check
  - [ ] `php artisan test` (if any) or `php artisan serve` + manual check in browser

