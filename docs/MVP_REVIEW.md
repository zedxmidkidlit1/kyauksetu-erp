# MVP Review

## Executive Assessment

Kyauksetu ERP is a **demo-ready Laravel university ERP and KAI backend MVP**. It is beyond a prototype, but it is not beta or production-ready.

The repository has a broad ERP data model, Filament CRUD foundation, applicant/student/teacher portals, Sanctum mobile APIs, announcement-backed notification feeds, KAI context/chat behavior, audit logging, and seeded demo data. Before feature expansion, the project must close critical access-control, role-permission, data-integrity, rate-limiting, and verification gaps.

## Application Surfaces

- **Filament Admin** at `/admin` for ERP administration and review. The supported demo currently uses `super_admin` only.
- **Applicant Portal** at `/applicant/login` for registration, application submission, and application status.
- **Student Portal** at `/student/login` for profile, enrollment, timetable, attendance, results, fees, library, hostel, and announcements.
- **Teacher Portal** at `/teacher/login` for profile, assignments, timetable, classes, announcements, attendance, and marks.
- **Mobile API** under `/api/v1` for student/teacher authentication, student data, notifications, and KAI.
- **KAI Context / Chat API** at `/api/v1/kai/context` and `/api/v1/kai/chat`.

## Implemented MVP Capabilities

- Broad Filament resource coverage for the ERP foundation, primarily usable through `super_admin`.
- Eloquent schema for IAM, academic structure, SIS, admissions, teaching, attendance, examinations/results, communication, library, hostel, finance, inventory, HR, and KAI logs.
- Applicant registration, application submission/status, and accepted-applicant conversion to a student profile/enrollment.
- Student portal and paginated student API endpoints scoped to the authenticated student.
- Teacher portal with own-assignment attendance and marks workflows.
- Sanctum mobile authentication with a required `mobile` token ability.
- Announcement-backed `/notifications` feed for supported student and teacher roles.
- Permission-safe KAI context for students and teachers, with a deterministic local responder by default.
- Optional Laravel AI SDK provider configuration and a smoke command.
- KAI chat session/message logging for super-admin review.
- Activity logging on most admin-managed records.
- Non-production demo data covering the documented super-admin, applicant, student, teacher, and API journeys.

## Partially Implemented or Foundation-Only

- **IAM:** role names, permissions, and policies exist, but only `super_admin` receives the full permission set. Registrar, department admin, librarian, hostel, finance, and other operational role mappings remain unfinished.
- **Admin security:** resource policies exist, but Filament panel entry currently allows every non-applicant user and dashboard widgets do not have explicit visibility authorization.
- **Admissions:** the workflow validates that IDs exist but does not yet enforce batch open/close dates, fixed batch/program selection, or major/program consistency.
- **Attendance:** own-assignment authorization exists, but record generation is scoped only by class section and can include historical or otherwise ineligible enrollments.
- **Notifications:** the mobile endpoint is an announcement-backed feed only. There is no push delivery or unread state.
- **Documents:** document records contain file paths, but there is no controlled upload, validation, storage-visibility, retention, or malware-scanning workflow.
- **Finance:** fee and payment records exist, but there is no payment gateway or production reconciliation workflow.
- **KAI external provider:** external calls are opt-in and silently fall back to the local responder on provider errors; production reporting and rate limiting are still required.
- **Frontend:** portal views work, but the JavaScript entry point is scaffold-level and the three portal layouts duplicate large inline CSS blocks.
- **Deployment:** Railway staging configuration exists, but production backups, monitoring, rollback, secret governance, and worker supervision are not established.

## Critical Blockers Before the Next Phase

1. **Lock down Filament access**
   - Allow only explicit administrative/back-office roles into the panel.
   - Add authorization-aware visibility to every dashboard widget.
   - Remove or environment-gate the default `test@example.com` user and predictable factory password.

2. **Complete the role-permission matrix**
   - Define the permissions for registrar, department admin, librarian, hostel warden, finance officer, and any other back-office roles.
   - Add tests proving allowed and denied actions for each role.

3. **Correct domain integrity**
   - Enforce admissions dates and batch/program/major relationships.
   - Scope attendance generation by academic year, semester, class section, enrollment status, and eligibility.
   - Use forward-only migrations for any new database constraints because existing migrations may have run in shared environments.

4. **Harden exposed endpoints and integrations**
   - Rate-limit applicant/student/teacher web login and KAI chat routes.
   - Add external AI provider error reporting without exposing prompts, context, credentials, or raw provider errors.
   - Set production-specific HTTPS, CORS, token-expiration, and secret-management values.

5. **Restore a trustworthy verification baseline**
   - Start Sail and run the full PHPUnit suite in the current worktree.
   - Add coverage for Filament panel access, widget visibility, policies, role matrices, admissions consistency, attendance eligibility, rate limiting, and important database constraints.
   - Add CI with tests, frontend build, Pint, and dependency audits.

## Testing Status

- The suite contains 77 PHPUnit test methods, mostly feature tests.
- Coverage is strongest for portal/API authentication, cross-user data isolation, mobile token abilities, KAI context safety, applicant conversion, and teacher attendance/marks workflows.
- Admin resource, widget, policy, role-matrix, and database-integrity coverage is sparse relative to the size of the ERP model.
- The last documented baseline is 77 passing tests and 391 assertions.
- That baseline is not currently verified. During the 2026-07-10 review, `vendor/bin/sail artisan test --compact --do-not-cache-result` could not start because Docker/Podman was not running.

## Supported Demo

Use `docs/DEMO_FLOW.md` as the source of truth. The supported admin journey uses `demo.admin@kyauksetu.test` with the `super_admin` role. The seeded registrar account is retained for future role-matrix verification and should not be presented as a working admissions-admin journey until its permissions are assigned and tested.

The demo uses the local KAI responder and announcement-backed notifications. It does not require an external AI provider or push-notification service.

## Known Product Gaps

- No Flutter application in this repository.
- No FCM/APNs push notifications or unread notification state.
- No controlled document upload workflow.
- No payment gateway.
- No SSO login behavior.
- No advanced reports, transcripts, or PDFs.
- No complete production deployment/operations platform.

## Work After the Blockers

After the five blockers above are complete and the suite passes:

1. Run the final manual browser/API demo pass.
2. Build the Flutter KAI MVP against `docs/MOBILE_API_CONTRACT.md`.
3. Add controlled document uploads with private storage and review states.
4. Define push-notification events, ownership, unread state, and delivery channels.
5. Extend result publishing toward transcript-ready records and later PDF/reporting.
6. Complete production deployment, backups, monitoring, rollback, real-data migration, and stakeholder permission review.
