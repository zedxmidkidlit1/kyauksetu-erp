# MVP Review

## Executive Assessment

Kyauksetu ERP is a **demo-ready Laravel university ERP and KAI backend MVP**. It is beyond a prototype, but it is not beta or production-ready.

The repository has a broad ERP data model, Filament CRUD foundation, applicant/student/teacher portals, Sanctum mobile APIs, announcement-backed notification feeds, KAI context/chat behavior, audit logging, and seeded demo data. The first hardening implementation, local automated verification, and readiness-document reconciliation are complete. Before feature expansion, the project still needs stakeholder approval of global role boundaries, a successful CI run, and a manual pass through the supported demo.

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

- **IAM:** least-privilege mappings and authorization tests exist for operational roles, but the intended global boundaries still require stakeholder approval.
- **Admin security:** Filament panel entry uses an explicit back-office allow-list, and dashboard widgets use authorization-aware visibility rules.
- **Admissions:** intake dates and batch/program/major consistency are enforced with focused regression coverage.
- **Attendance:** record generation filters eligible active profiles and enrollments by academic year, semester, and class section.
- **Notifications:** the mobile endpoint is an announcement-backed feed only. There is no push delivery or unread state.
- **Documents:** document records contain file paths, but there is no controlled upload, validation, storage-visibility, retention, or malware-scanning workflow.
- **Finance:** fee and payment records exist, but there is no payment gateway or production reconciliation workflow.
- **KAI external provider:** external calls are opt-in, named throttling and sanitized provider failure reporting are implemented, and production monitoring, credential governance, and approved live smoke verification remain pending.
- **Frontend:** portal views work, but the JavaScript entry point is scaffold-level and the three portal layouts duplicate large inline CSS blocks.
- **Deployment:** Railway staging configuration exists, but production backups, monitoring, rollback, secret governance, and worker supervision are not established.

## Remaining Gates Before the Next Phase

1. Confirm the existing CI workflow passes migrations, frontend build, formatting, tests, and dependency audit.
2. Obtain stakeholder approval for the operational role-permission matrix and global access boundaries.
3. Manually verify the documented super-admin, registrar, applicant, student, teacher, mobile, and KAI demo journeys.
4. Retain production-specific HTTPS, CORS, token expiration, monitoring, backup, rollback, and secret-governance work as production-readiness requirements.

## Testing Status

- The current Sail baseline passes with 90 tests and 468 assertions.
- The focused hardening selection passes with 32 tests and 140 assertions, and Pint completes without changes.
- Coverage is strongest for portal/API authentication, cross-user data isolation, mobile token abilities, KAI context safety, applicant conversion, and teacher attendance/marks workflows.
- Focused coverage now includes panel and widget access, policies, role mappings, admissions consistency, attendance eligibility, rate limiting, and KAI provider failure handling.
- The passing totals describe the verified local worktree; CI and the complete manual demo remain separate gates.

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
