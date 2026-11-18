# SHARE Project — Feature-to-File Map

**Project:** Systematic Hazard Assessment & Relief Engine (SHARE)  
**Root:** `C:\Users\USER\PhpstormProjects\CSE470`  
**Stack:** PHP 8.2, Laravel 12, SQLite (configured), Bootstrap 5, Blade (Laravel MVC)

This document maps implemented features (including R16–R20) to their exact code locations. No existing project files were modified when this document was generated.

## Scanned Areas

- `routes/web.php`, `routes/console.php`
- `app/Http/Controllers/`
- `app/Models/`
- `database/migrations/`
- `database/seeders/`
- `app/Console/Commands/`
- `resources/views/`
- `datasets/`

---

## Shared / Cross-Cutting (Dashboard & Layout)

**Purpose:** Admin dashboard shell and summary statistics (real DB queries).

| Item | Location / Details |
|---|---|
| **Routes** (`routes/web.php`) | `GET /` → `App\Http\Controllers\DashboardController@index` (name: `dashboard`) |
| **Controller** | `app/Http/Controllers/DashboardController.php` |
| **Controller methods** | `index()` — computes counts for active disasters, total zones, total registered volunteers, pending relief requests, unresolved emergency requests, total audit records |
| **Models (reads)** | `app/Models/Disaster.php`, `app/Models/Zone.php`, `app/Models/VolunteerProfile.php`, `app/Models/ReliefRequest.php`, `app/Models/AuditTrail.php` |
| **Database tables** | `disasters`, `zones`, `volunteer_profiles`, `relief_requests`, `audit_trails` |
| **Migrations** | See R16–R20 migration entries below |
| **Blade views** | `resources/views/layouts/app.blade.php` (Bootstrap 5 layout, sidebar/top navbar, flash/validation), `resources/views/dashboard.blade.php` |
| **CSV datasets** | None |
| **Data Flow** | **Entry:** No user-submitted form or CSV input (read-only dashboard). **Processing:** `DashboardController@index()` runs aggregate/count queries (`Disaster` active, `Zone` count, `VolunteerProfile` count, `ReliefRequest` pending and unresolved emergency `request_type` not null/not resolved, `AuditTrail` count). **Storage:** Reads from `disasters` (`status`), `zones`, `volunteer_profiles`, `relief_requests` (`status`, `request_type`), `audit_trails`. **Output:** Passes counts to and renders `resources/views/dashboard.blade.php` within `resources/views/layouts/app.blade.php`. |

**Other (scaffold/base, not feature-specific):** `app/Http/Controllers/Controller.php`, `app/Models/User.php`, `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/0001_01_01_000001_create_cache_table.php`, `database/migrations/0001_01_01_000002_create_jobs_table.php`, `resources/views/welcome.blade.php` (unused).

---

## R16 — Disaster Declaration & Zoning

**SRS:** Admin defines hazard event profiles, maps impacted sub-districts/zones, and configures priority tiers.  
**Actor:** Command Center Admin

| Item | Location / Details |
|---|---|
| **Routes** (`routes/web.php`) | `Route::resource('disasters', DisasterController::class)` → `disasters.index` `GET /disasters` (`index`), `disasters.create` `GET /disasters/create` (`create`), `disasters.store` `POST /disasters` (`store`), `disasters.show` `GET /disasters/{disaster}` (`show`), `disasters.edit` `GET /disasters/{disaster}/edit` (`edit`), `disasters.update` `PUT\|PATCH /disasters/{disaster}` (`update`), `disasters.destroy` `DELETE /disasters/{disaster}` (`destroy`) <br> `POST /disasters/{disaster}/zones` → `DisasterController@storeZone` (`disasters.zones.store`) <br> `PUT /disasters/{disaster}/zones/{zone}` → `DisasterController@updateZone` (`disasters.zones.update`) <br> `DELETE /disasters/{disaster}/zones/{zone}` → `DisasterController@destroyZone` (`disasters.zones.destroy`) |
| **Controller** | `app/Http/Controllers/DisasterController.php` |
| **Controller methods** | `index()`, `create()`, `store(Request $request)`, `show(Disaster $disaster)`, `edit(Disaster $disaster)`, `update(Request $request, Disaster $disaster)`, `destroy(Disaster $disaster)` (safe: marks disaster `Inactive` if dependent records exist), `storeZone(Request $request, Disaster $disaster)`, `updateZone(Request $request, Disaster $disaster, Zone $zone)`, `destroyZone(Disaster $disaster, Zone $zone)` (blocks delete if zone has dependent records). Validation and `AuditTrail::log(...)` on key actions. |
| **Models** | `app/Models/Disaster.php` — fillable: `name,type,description,start_date,end_date,status`; casts dates; relations `zones()`, `reliefRequests()`, `inventories()`, `dutyAssignments()` (hasMany). <br> `app/Models/Zone.php` — fillable: `disaster_id,district,sub_district,area_name,severity_level,priority_tier`; relations `disaster()` (belongsTo), `reliefRequests()`, `inventories()`, `dutyAssignments()` (hasMany). |
| **Database tables** | `disasters`, `zones` (one-to-many: `zones.disaster_id` → `disasters.id`, FK constrained, `onDelete restrict`) |
| **Migrations** | `database/migrations/2026_11_17_122046_create_disasters_table.php` — `id`, `name`, `type`, `description` (nullable), `start_date`, `end_date` (nullable), `status` (default `Active`), timestamps. <br> `database/migrations/2026_11_17_122047_create_zones_table.php` — `id`, `foreignId('disaster_id')`, `district`, `sub_district`, `area_name`, `severity_level`, `priority_tier`, timestamps. |
| **Blade views** | `resources/views/admin/disasters/index.blade.php` (disaster list), `resources/views/admin/disasters/create.blade.php`, `resources/views/admin/disasters/edit.blade.php`, `resources/views/admin/disasters/show.blade.php` (disaster details + add/edit/delete zones, priority tier management) |
| **CSV datasets** | None (disaster/zones may also be created/mapped during dataset import for R18/R19) |
| **Data Flow** | **Entry:** Blade forms — `create.blade.php`/`edit.blade.php` fields `name`, `type`, `description`, `start_date`, `end_date`, `status`; `show.blade.php` add-zone fields `district`, `sub_district`, `area_name`, `severity_level`, `priority_tier` and inline zone edit/delete forms. **Processing:** `DisasterController@store`/`@update` validate disaster inputs (`required` name/type/start_date/status, enums, date rules); `@storeZone`/`@updateZone` validate zone inputs (required strings, severity/priority enums) and enforce zone-disaster ownership; `@destroy` checks dependents (`zones/reliefRequests/inventories/dutyAssignments`) to delete or mark `Inactive`; `@destroyZone` blocks if dependents. Key actions logged via `AuditTrail::log(...)`. **Storage:** `Disaster` model → `disasters` table columns (`name,type,description,start_date,end_date,status`, timestamps); `Zone` model → `zones` table (`disaster_id,district,sub_district,area_name,severity_level,priority_tier`, timestamps). **Output:** `index()` → `admin.disasters.index`, `create()` → `admin.disasters.create`, `edit()` → `admin.disasters.edit`, `show()` (with zones) → `admin.disasters.show`; mutations redirect with success/error flash messages. |

---

## R17 — Volunteer Deployment Workflow

**SRS:** Screen volunteer registrations, validate/verify certifications, and map approved volunteers to active disaster units/zones.  
**Actor:** Command Center Admin

| Item | Location / Details |
|---|---|
| **Routes** (`routes/web.php`) | `GET /volunteers` → `VolunteerController@index` (`volunteers.index`) <br> `GET /volunteers/vetting` → `VolunteerController@vetting` (`volunteers.vetting`) <br> `GET /volunteers/roster` → `VolunteerController@roster` (`volunteers.roster`) <br> `GET /volunteers/{volunteer}` → `VolunteerController@show` (`volunteers.show`) <br> `POST /volunteers/{volunteer}/approve` → `VolunteerController@approve` (`volunteers.approve`) <br> `POST /volunteers/{volunteer}/reject` → `VolunteerController@reject` (`volunteers.reject`) <br> `POST /volunteers/{volunteer}/certification` → `VolunteerController@updateCertification` (`volunteers.certification`) <br> `POST /assignments` → `VolunteerController@storeAssignment` (`assignments.store`) <br> `PUT /assignments/{assignment}` → `VolunteerController@updateAssignment` (`assignments.update`) |
| **Controller** | `app/Http/Controllers/VolunteerController.php` |
| **Controller methods** | `index()`, `vetting()`, `show(VolunteerProfile $volunteer)`, `approve(VolunteerProfile $volunteer)`, `reject(VolunteerProfile $volunteer)`, `updateCertification(Request $request, VolunteerProfile $volunteer)`, `roster()`, `storeAssignment(Request $request)` (enforces: only **Approved** volunteers; zone must belong to an **Active** disaster), `updateAssignment(Request $request, DutyAssignment $assignment)`. Audit logging on key actions. |
| **Models** | `app/Models/VolunteerProfile.php` — fillable: `full_name,email,phone_number,address,skills,certification_name,certification_status,registration_status`; relation `dutyAssignments()` (hasMany). <br> `app/Models/DutyAssignment.php` — fillable: `volunteer_profile_id,disaster_id,zone_id,assigned_responsibility,assignment_date,assignment_status`; casts `assignment_date`; relations `volunteerProfile()`, `disaster()`, `zone()` (belongsTo). |
| **Database tables** | `volunteer_profiles`, `duty_assignments` (FKs: `volunteer_profile_id` → `volunteer_profiles`, `disaster_id` → `disasters`, `zone_id` → `zones`, `onDelete restrict`) |
| **Migrations** | `database/migrations/2026_11_17_122047_create_volunteer_profiles_table.php` — `id`, `full_name`, `email` (unique), `phone_number`, `address`, `skills`, `certification_name` (nullable), `certification_status` (default `Pending`), `registration_status` (default `Pending`), timestamps. <br> `database/migrations/2026_11_17_122048_create_duty_assignments_table.php` — `id`, FKs `volunteer_profile_id`, `disaster_id`, `zone_id`, `assigned_responsibility`, `assignment_date`, `assignment_status` (default `Assigned`), timestamps. |
| **Blade views** | `resources/views/volunteers/index.blade.php` (registration list), `resources/views/volunteers/vetting.blade.php` (approve/reject, certification verification), `resources/views/volunteers/show.blade.php` (profile details, assignment form for active zones, deployment records), `resources/views/volunteers/roster.blade.php` (deployment/roster + assignment form showing volunteers assigned to zones) |
| **Seeders** | `database/seeders/VolunteerSeeder.php` (5 sample volunteers, `updateOrCreate`), called by `database/seeders/DatabaseSeeder.php` |
| **CSV datasets** | None |
| **Data Flow** | **Entry:** `database/seeders/VolunteerSeeder.php` seeds sample volunteer records (name/email/phone/address/skills/certification/registration). UI actions/fields — `vetting.blade.php` certification select `certification_status` and approve/reject actions; `show.blade.php`/`roster.blade.php` assignment forms submit `volunteer_profile_id`, `zone_id`, `assigned_responsibility`, `assignment_date`, `assignment_status`; roster/show inline update forms submit `assigned_responsibility`, `assignment_status`. **Processing:** `VolunteerController@approve`/`@reject` set registration status; `@updateCertification` validates `certification_status` enum; `@storeAssignment` validates required fields/IDs/dates/enums, rejects if volunteer not `Approved`, resolves zone→disaster and rejects if disaster not `Active`, derives `disaster_id`; `@updateAssignment` validates responsibility/status. Actions logged via `AuditTrail::log(...)`. **Storage:** `VolunteerProfile` → `volunteer_profiles` (`full_name,email,phone_number,address,skills,certification_name,certification_status,registration_status`, timestamps); `DutyAssignment` → `duty_assignments` (`volunteer_profile_id,disaster_id,zone_id,assigned_responsibility,assignment_date,assignment_status`, timestamps). **Output:** `index/vetting/show/roster` render respective Blade views with volunteer/assignment/disaster-zone data; mutations redirect back/roster with flash messages. |

---

## R18 — Real-Time Need Assessment Grid

**SRS:** Compare citizen-requested resource quantities against active warehouse inventory holdings.  
**Actor:** Command Center Admin

| Item | Location / Details |
|---|---|
| **Routes** (`routes/web.php`) | `GET /assessment` → `App\Http\Controllers\AssessmentController@index` (`assessment.index`) |
| **Controller** | `app/Http/Controllers/AssessmentController.php` |
| **Controller methods** | `index(Request $request)` — applies filters (disaster, zone, resource, scope `active/unresolved` vs `all`), groups demand from `relief_requests` and stock from `inventories`, computes per disaster-zone-resource: `Total Demand = SUM(quantity_requested)`, `Available Stock = quantity_in_stock`, `Shortage = max(demand-stock,0)`, `Surplus = max(stock-demand,0)` from DB (includes inventory-only rows) |
| **Models** | `app/Models/ReliefRequest.php` — fillable includes `disaster_id,zone_id,resource_name,quantity_requested,request_type,description,priority,status,resolved_at,last_escalated_at,escalation_level,previous_priority,created_at`; casts datetimes; `disaster()`, `zone()`; `scopeUnresolved()` (excludes `Resolved`). <br> `app/Models/Inventory.php` — fillable `disaster_id,zone_id,resource_name,quantity_in_stock`; `disaster()`, `zone()`. <br> Uses `app/Models/Disaster.php`, `app/Models/Zone.php` for filters/display. |
| **Database tables** | `relief_requests` (resource request rows for R18; unified with R19), `inventories` (both linked to `disasters` and `zones`) |
| **Migrations** | `database/migrations/2026_11_17_122049_create_relief_requests_table.php` — FKs `disaster_id`, `zone_id` (restrict); `resource_name`, `quantity_requested` (nullable); `request_type`, `description` (nullable); `priority`, `status`, `resolved_at`, `last_escalated_at`, `escalation_level`, `previous_priority`, timestamps. <br> `database/migrations/2026_11_17_122050_create_inventories_table.php` — FKs `disaster_id`, `zone_id` (restrict); `resource_name`, `quantity_in_stock`, timestamps. |
| **Blade views** | `resources/views/admin/assessment_grid.blade.php` (disaster/zone/resource/scope filters, demand vs stock table, shortage/surplus, red/green status indicators) |
| **Console command** | `app/Console/Commands/ImportShareDatasets.php` — signature `share:import-datasets`; `handle()` runs `ShareDataSeeder` to import CSVs from `datasets/` |
| **Seeders** | `database/seeders/ShareDataSeeder.php` — `run()`, `readCsv()`, `getOrCreateDisaster()`, `getOrCreateZone()`, `importInventory()`, `importReliefRequests()` (maps CSV `disaster_name`/`zone` to DB, validates, idempotent/no-duplicate import, preserves CSV timestamps/statuses); called by `database/seeders/DatabaseSeeder.php` |
| **CSV datasets** | `datasets/R18_relief_requests.csv` (8 records; columns: `id,disaster_name,zone,resource_name,quantity_requested,priority,status,created_at`) <br> `datasets/R18_inventory.csv` (7 records; columns: `id,disaster_name,zone,resource_name,quantity_in_stock`) |
| **Data Flow** | **Entry:** CSV files — `R18_relief_requests.csv` (`disaster_name,zone,resource_name,quantity_requested,priority,status,created_at`) and `R18_inventory.csv` (`disaster_name,zone,resource_name,quantity_in_stock`) imported via `share:import-datasets` / `ShareDataSeeder`; UI filter query params from `assessment_grid.blade.php`: `disaster_id`, `zone_id`, `resource_name`, `scope` (`active`/`all`). **Processing:** `ShareDataSeeder` reads/validates required columns and row shape, maps `disaster_name`/`zone` to `Disaster`/`Zone` (get-or-create), casts quantities/dates, and idempotently upserts records (preserving CSV timestamps/statuses; sets escalation defaults for new requests). `AssessmentController@index` applies filters, aggregates `relief_requests` (`SUM(quantity_requested)` grouped by disaster/zone/resource, unresolved by default via `scopeUnresolved` unless `scope=all`) and joins/matches `inventories` to compute demand/stock/shortage/surplus. **Storage:** `ReliefRequest` → `relief_requests` (`disaster_id,zone_id,resource_name,quantity_requested,priority,status,created_at,updated_at,resolved_at,last_escalated_at,escalation_level`, `request_type/description` null for R18 rows); `Inventory` → `inventories` (`disaster_id,zone_id,resource_name,quantity_in_stock`, timestamps). **Output:** Renders `resources/views/admin/assessment_grid.blade.php` with filter options and computed rows (DB-backed, not hardcoded). |

---

## R19 — Incident Escalation Engine

**SRS:** Upgrade unresolved rescue/emergency ticket urgency using chronological baseline checks (automatic and manual).  
**Actor:** Command Center Admin

| Item | Location / Details |
|---|---|
| **Routes** (`routes/web.php`) | `GET /escalations` → `App\Http\Controllers\EscalationController@index` (`escalations.index`) <br> `POST /escalations/run` → `App\Http\Controllers\EscalationController@run` (`escalations.run`) <br> `PUT /escalations/{reliefRequest}/priority` → `App\Http\Controllers\EscalationController@updatePriority` (`escalations.priority`) |
| **Scheduled route/command** (`routes/console.php`) | `Schedule::command(App\Console\Commands\CheckEscalations::class)->hourly()`; run locally with `php artisan schedule:work` |
| **Controller** | `app/Http/Controllers/EscalationController.php` |
| **Controller methods** | `index(Request $request)` — filters (disaster, zone, status, priority), lists emergency requests (`request_type` not null) with waiting duration from `created_at` to now/resolved; `run()` — safely executes `Artisan::call('requests:check-escalations')` and logs audit; `updatePriority(Request $request, ReliefRequest $reliefRequest)` — manual priority update, stores `previous_priority`, saves new priority, audits (resolved/emergency-only guarded). |
| **Console command** | `app/Console/Commands/CheckEscalations.php` — signature `requests:check-escalations`; `handle()` escalates **unresolved** emergency requests by waiting time (<2h no change, 2–<4h +1 level, 4–<6h +2 levels, ≥6h Urgent, capped at Urgent), uses `escalation_level`/`last_escalated_at` to prevent repeated escalation, records `previous_priority`, logs `AuditTrail`, preserves original CSV timestamps. |
| **Models** | `app/Models/ReliefRequest.php` (shared unified table with R18) — emergency fields `request_type`, `description`; escalation tracking `resolved_at`, `last_escalated_at`, `escalation_level`, `previous_priority`; `scopeUnresolved()`. <br> `app/Models/AuditTrail.php` for escalation/audit logging. |
| **Database tables** | `relief_requests` (R19 emergency requests stored in same table as R18, distinguished by `request_type`) |
| **Migrations** | `database/migrations/2026_11_17_122049_create_relief_requests_table.php` (includes escalation tracking columns and emergency fields) |
| **Blade views** | `resources/views/admin/escalations.blade.php` (filters, “Run Escalation Check” button, request table with ID/type/zone/description/submitted/waiting/current & previous priority/status/last escalation/manual priority control; priority badges) |
| **Seeders / Import** | `database/seeders/ShareDataSeeder.php` (`importReliefRequests(..., true)` for emergency CSV), `app/Console/Commands/ImportShareDatasets.php` (`share:import-datasets`) |
| **CSV datasets** | `datasets/R19_emergency_requests.csv` (8 records; columns: `id,disaster_name,zone,request_type,description,priority,status,created_at,resolved_at`) — imported into `relief_requests`, timestamps/statuses preserved |
| **Data Flow** | **Entry:** `datasets/R19_emergency_requests.csv` (`disaster_name,zone,request_type,description,priority,status,created_at,resolved_at`) via `ShareDataSeeder`/`share:import-datasets`; UI filter params in `escalations.blade.php` (`disaster_id,zone_id,status,priority`); “Run Escalation Check” POST (`escalations.run`); manual priority form field `priority` per request (`escalations.priority`). **Processing:** `ShareDataSeeder` validates/maps CSV rows to `Disaster`/`Zone`, preserves original timestamps/statuses/`resolved_at`, stores emergency fields, initializes escalation tracking for new rows. `EscalationController@index` queries emergency `ReliefRequest` rows and computes waiting duration. `EscalationController@run` invokes `requests:check-escalations`. `CheckEscalations@handle` selects unresolved emergency requests, calculates waiting hours, determines tier/target priority, skips already-applied tiers/resolved, updates priority/escalation fields, and logs audits. `EscalationController@updatePriority` validates `priority` enum, guards non-emergency, saves `previous_priority`/`priority`, logs audit. **Storage:** `ReliefRequest` → `relief_requests` (`disaster_id,zone_id,request_type,description,priority,status,created_at,updated_at,resolved_at,last_escalated_at,escalation_level,previous_priority`, R18 resource fields null for emergency rows); `AuditTrail` → `audit_trails` for auto/manual escalation and run events. **Output:** `resources/views/admin/escalations.blade.php` renders filtered emergency requests with computed waiting times, statuses/priorities, escalation metadata, and manual controls; run/update actions redirect with flash messages. |

---

## R20 — Audit & Transparency Logs

**SRS:** Trace end-to-end routing/pathways for financial or material items via system-wide audit reporting (application-level immutable records).  
**Actor:** Command Center Admin / Oversight Officer

| Item | Location / Details |
|---|---|
| **Routes** (`routes/web.php`) | `GET /audit` → `App\Http\Controllers\AuditController@index` (`audit.index`) <br> `GET /audit/{auditTrail}` → `App\Http\Controllers\AuditController@show` (`audit.show`) <br> `GET /donations` → `App\Http\Controllers\AuditController@donations` (`donations.index`) <br> `POST /donations` → `App\Http\Controllers\AuditController@storeDonation` (`donations.store`) <br> *No edit/delete routes for audit records.* |
| **Controller** | `app/Http/Controllers/AuditController.php` |
| **Controller methods** | `index(Request $request)` — read-only filtered, paginated audit list (action_type, related_type/record, date range); `show(AuditTrail $auditTrail)` — read-only details; `donations()` — list donation/operation records; `storeDonation(Request $request)` — creates `Donation` and corresponding `AuditTrail` entry (Received/Allocated/Dispatched/Delivered operations). No update/delete methods. |
| **Models** | `app/Models/AuditTrail.php` — `$timestamps=false`; fillable `action_type,related_type,related_id,description,quantity,performed_by,created_at`; casts `created_at`; static `log($actionType,$description,$relatedType=null,$relatedId=null,$quantity=null,$performedBy='Command Center Admin')` helper. Application-level append-only (no UI/controller edit/delete). <br> `app/Models/Donation.php` — fillable `donor_name,donation_type,resource_name,quantity,donation_date,status`; casts `donation_date`. |
| **Database tables** | `audit_trails`, `donations` |
| **Migrations** | `database/migrations/2026_11_17_122050_create_audit_trails_table.php` — `id`, `action_type`, `related_type` (nullable), `related_id` (nullable), `description`, `quantity` (nullable), `performed_by` (nullable), `created_at` (useCurrent; no `updated_at`). <br> `database/migrations/2026_11_17_122051_create_donations_table.php` — `id`, `donor_name`, `donation_type`, `resource_name`, `quantity`, `donation_date`, `status` (default `Received`), timestamps. |
| **Blade views** | `resources/views/admin/audit_report.blade.php` (filters, paginated audit table, view-only), `resources/views/admin/audit_show.blade.php` (read-only record detail), `resources/views/admin/donations.blade.php` (record donation/operation form + recorded operations list) |
| **Cross-module audit logging hooks** | `AuditTrail::log(...)` called from `app/Http/Controllers/DisasterController.php`, `app/Http/Controllers/VolunteerController.php`, `app/Http/Controllers/EscalationController.php`, `app/Http/Controllers/AuditController.php`, and `app/Console/Commands/CheckEscalations.php` |
| **CSV datasets** | None |
| **Data Flow** | **Entry:** Donation/operation form in `donations.blade.php` submits `donor_name`, `donation_type`, `resource_name`, `quantity`, `donation_date`, `status`; audit filter query params in `audit_report.blade.php` submit `action_type`, `related_type`, `date_from`, `date_to`; system-generated events from R16–R19 controllers/command invoke `AuditTrail::log(...)`. **Processing:** `AuditController@storeDonation` validates inputs (required strings, `quantity` integer ≥1, valid date, status enum), creates `Donation`, derives `action_type` from status, and writes an audit entry via `AuditTrail::log(...)`. `AuditController@index` applies optional filters and paginates results; `@show` loads a single record. No edit/delete processing exists. **Storage:** `Donation` → `donations` (`donor_name,donation_type,resource_name,quantity,donation_date,status`, timestamps); `AuditTrail` → `audit_trails` (`action_type,related_type,related_id,description,quantity,performed_by,created_at`). **Output:** `audit_report.blade.php` renders filtered/paginated audit log, `audit_show.blade.php` renders read-only details, `donations.blade.php` renders donation/operation form and recorded list. |

---

## Supporting Files (Commands, Seeders, Datasets)

| Type | File | Purpose |
|---|---|---|
| Console command | `app/Console/Commands/CheckEscalations.php` | R19 automatic escalation (`requests:check-escalations`) |
| Console command | `app/Console/Commands/ImportShareDatasets.php` | R18/R19 CSV import (`share:import-datasets`) |
| Seeder | `database/seeders/DatabaseSeeder.php` | Calls `VolunteerSeeder` and `ShareDataSeeder` |
| Seeder | `database/seeders/VolunteerSeeder.php` | R17 sample volunteer records |
| Seeder | `database/seeders/ShareDataSeeder.php` | Imports/maps R18+R19 CSV datasets into DB (idempotent) |
| Dataset (input) | `datasets/R18_relief_requests.csv` | R18 relief demand records (8) |
| Dataset (input) | `datasets/R18_inventory.csv` | R18 inventory stock records (7) |
| Dataset (input) | `datasets/R19_emergency_requests.csv` | R19 emergency request records (8) |

---

## Notes

- **Unified request table:** R18 resource requests and R19 emergency requests both use `relief_requests` (`App\Models\ReliefRequest`).
- **Audit immutability:** Enforced at application/UI/controller level (no edit/delete actions/routes for audit records), not claimed as cryptographic or DB-level tamper-proofing.
- **Dataset provenance:** Provided CSV records are synthetic demonstration data (August 2024 Eastern Bangladesh flood scenario) and were used as-is from `datasets/`.
- **Scope:** Mapping reflects the scanned implementation in `routes/`, `app/Http/Controllers/`, `app/Models/`, `database/migrations/`, `resources/views/`, `app/Console/Commands/`, `database/seeders/`, and `datasets/`.
