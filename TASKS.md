# Kyauksetu ERP Roadmap

## Current Stage

Kyauksetu ERP is a demo-ready Laravel university ERP and KAI backend MVP, not a beta or production-ready system. Core portal and API journeys are implemented, while the current phase is establishing a trustworthy verification baseline, confirming authorization boundaries, reconciling readiness documentation, and validating the supported demo before further feature expansion.

## Current Development Mode

- Prioritize core feature completion.
- Use focused tests during implementation.
- Defer full-suite and CI hardening until the next stabilization milestone.

## Current Phase: Verification and Hardening

- [x] Establish a passing full local test baseline in the current worktree: 90 tests and 468 assertions.
- [ ] Confirm a green CI run for migrations, frontend build, formatting, tests, and dependency audit.
- [x] Reconcile PHP version, test counts, implemented hardening, and readiness claims across project documentation.
- [ ] Obtain stakeholder approval for the operational role-permission matrix and global access boundaries.
- [ ] Manually verify the documented super-admin, registrar, applicant, student, teacher, mobile, and KAI demo flows.

## Completed Milestones

### Admissions

- Applicant registration, authentication, dashboard, application submission, detail, and status routes are implemented.
- Admission submission validates batch/program/major consistency and handles duplicate programless applications within a transaction using row locking.
- Accepted applications have a guarded conversion path to student profile and enrollment records.
- Evidence: `routes/web.php`, `app/Http/Controllers/Applicant/`, `app/Models/AdmissionApplication.php`, `tests/Feature/ApplicantPortalTest.php`, `tests/Feature/ApplicantConversionTest.php`.

### Student Portal/API

- The student portal exposes profile, enrollment, timetable, attendance, results, fees, library, hostel, and announcements.
- Sanctum-protected student API endpoints provide scoped, paginated data for the same supported areas.
- Cross-user isolation and mobile token boundaries have focused feature coverage.
- Evidence: `routes/web.php`, `routes/api.php`, `app/Http/Controllers/Student/`, `app/Http/Controllers/Api/V1/StudentDataController.php`, `tests/Feature/StudentPortalTest.php`, `tests/Feature/StudentApiTest.php`, `tests/Feature/MobileApiHardeningTest.php`.

### Teacher Portal

- The teacher portal exposes profile, assignments, timetable, classes, announcements, attendance, and marks.
- Attendance and marks writes are transaction-backed and scoped to the authenticated teacher's assignments.
- Focused tests cover access boundaries, attendance eligibility, and mark entry.
- Evidence: `routes/web.php`, `app/Http/Controllers/Teacher/`, `tests/Feature/TeacherPortalTest.php`, `tests/Feature/TeacherAttendanceWorkflowTest.php`, `tests/Feature/TeacherMarksWorkflowTest.php`.

### Mobile/KAI Backend

- Mobile authentication issues Sanctum tokens with the `mobile` ability for supported student and teacher roles.
- KAI context/chat endpoints, named throttling, deterministic local fallback, provider configuration, sanitized provider failure handling, and chat logging are implemented.
- Notifications are delivered as an announcement-backed API feed, not push notifications.
- Evidence: `routes/api.php`, `app/Http/Controllers/Api/V1/`, `tests/Feature/MobileAuthApiTest.php`, `tests/Feature/KaiChatTest.php`, `tests/Feature/KaiContextTest.php`, `tests/Feature/KaiProviderTest.php`, `docs/MOBILE_API_CONTRACT.md`.

### ERP Data Foundation

- Models and migrations cover IAM, academic structure, SIS, admissions, teaching, attendance, exams/results, communication, library, hostel, finance, inventory, HR, and KAI logs.
- This milestone represents schema and model coverage only; it does not classify every domain as a completed operational workflow.
- Evidence: `app/Models/`, `database/migrations/`, `docs/ERP_MODULE_PATTERN.md`.

## In Progress

- **Admin/IAM verification:** panel restrictions, widget visibility, least-privilege mappings, and focused security tests exist, but the suite, role boundaries, and registrar demo still require verification. Evidence: `README.md`, `tests/Feature/SecurityHardeningTest.php`.
- **Library/Hostel/Finance maturity:** models and student read surfaces exist; payment gateway, reconciliation, and production-grade operational workflows remain incomplete. Evidence: `app/Models/LibraryLoan.php`, `app/Models/HostelAllocation.php`, `app/Models/StudentFee.php`, `app/Models/StudentPayment.php`, `docs/MVP_REVIEW.md`.
- **Inventory/HR workflow maturity:** schema and audited models exist, but dedicated operational controller routes and focused workflow tests are absent from the inspected surfaces. Evidence: `app/Models/Asset.php`, `app/Models/StockMovement.php`, `app/Models/StaffEmployment.php`, `database/migrations/`, `tests/Feature/`.
- **External KAI readiness:** external provider support is opt-in and covered with fakes, but production monitoring, credentials governance, approved live smoke verification, and operational limits are pending. Evidence: `docs/KAI_AI_SETUP.md`, `tests/Feature/KaiProviderTest.php`, `tests/Feature/KaiSmokeCommandTest.php`.

## Next Priorities

1. **Run Pint, focused tests, and full suite**
   - Status: complete — Pint passed without changes; 32 focused tests passed with 140 assertions; the full suite passed with 90 tests and 468 assertions.
   - Acceptance criteria: Pint completes without an uncommitted formatting diff; focused hardening tests pass; the complete PHPUnit suite passes in the current worktree; the resulting test and assertion totals are recorded accurately.
   - Relevant files: `README.md`, `tests/Feature/SecurityHardeningTest.php`, `tests/Feature/ApplicantPortalTest.php`, `tests/Feature/TeacherAttendanceWorkflowTest.php`, `tests/Feature/KaiChatTest.php`, `tests/Feature/KaiProviderTest.php`.
   - Dependencies/blockers: Docker/Sail must be available; PostgreSQL and required services must become healthy.

2. **Get CI green**
   - Acceptance criteria: the GitHub Actions workflow completes Composer install, Sail startup, migrations, frontend build, Pint clean-diff check, full PHPUnit suite, Composer audit, and teardown successfully.
   - Relevant files: `.github/workflows/ci.yml`, `database/migrations/`, `tests/`.
   - Dependencies/blockers: current workflow changes must be present on GitHub; required container images and package registries must be available.

3. **Reconcile PHP version, test counts, and readiness docs**
   - Status: complete — documentation now records the PHP 8.4.1+ requirement, current PHP 8.5 runtime, verified 90-test baseline, and implemented hardening while retaining the demo-ready MVP classification.
   - Acceptance criteria: runtime/version claims agree with CI; test totals match the verified run; implemented hardening is no longer described as unfinished; all readiness claims consistently retain the demo-ready MVP status until gates pass.
   - Relevant files: `README.md`, `docs/MVP_REVIEW.md`, `docs/DEMO_FLOW.md`, `docs/MOBILE_API_CONTRACT.md`, `.github/workflows/ci.yml`.
   - Dependencies/blockers: requires results from priorities 1 and 2; readiness must not be inferred without those results.

4. **Approve role-permission matrix**
   - Acceptance criteria: stakeholders approve intended global access boundaries for each operational role; allowed and denied behavior matches focused authorization tests; unresolved access decisions are documented before further role expansion.
   - Relevant files: `README.md`, `docs/MVP_REVIEW.md`, `tests/Feature/SecurityHardeningTest.php`.
   - Dependencies/blockers: requires stakeholder decisions and a passing authorization test baseline.

5. **Re-run documented demo flows**
   - Acceptance criteria: super-admin, registrar, applicant, student, teacher, mobile API, notifications feed, and KAI journeys complete as documented without cross-role data exposure; observed limitations remain consistent with the docs.
   - Relevant files: `docs/DEMO_FLOW.md`, `docs/MOBILE_API_CONTRACT.md`, `routes/web.php`, `routes/api.php`.
   - Dependencies/blockers: priorities 1-4 should be complete; non-production demo data and Sail services must be available.

## Backlog

- Build the Flutter client against `docs/MOBILE_API_CONTRACT.md`.
- Add push notification delivery and unread-state ownership.
- Add controlled private uploads, validation, retention, and review workflows.
- Add payment gateway integration and production reconciliation.
- Implement SSO login behavior on top of the identity-mapping foundation.
- Add advanced reports, transcripts, exports, and PDFs.
- Establish production deployment, backups, restore testing, monitoring, centralized logging, alerting, rollback, secret governance, and real-data migration planning.

## Blockers / Risks

- The current local baseline passes with 90 tests and 468 assertions; CI has not been verified.
- CI contains the required verification stages, but repository evidence does not yet establish a successful run.
- `docs/MVP_REVIEW.md` and `docs/DEMO_FLOW.md` retain stale descriptions of some hardening work that recent code, tests, and history show as implemented but unverified.
- Operational role boundaries still require stakeholder approval and manual verification.
- Production storage, payment reconciliation, monitoring, backups, rollback, and secret-management processes are not established.

## Recently Completed

- `0ec1d7b` — hardened MVP security and workflow integrity, including access controls, role mappings, admissions and attendance integrity, named throttling, provider failure sanitization, focused tests, and initial CI.
- `ad16d26` — updated GitHub Actions CI for the 2026 runner/action baseline.
- `9d51e39` — adjusted the CI Composer bootstrap image.
- `9087f4b` — revised CI dependency setup to use PHP setup and Composer directly before starting Sail.
