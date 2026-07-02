# MVP Review

## Big Picture

Kyauksetu ERP is currently a Laravel university ERP and KAI backend MVP. The main application provides the ERP data model, web portals, admin operations, API authentication, KAI context assembly, and local KAI chat behavior.

The MVP is organized around these presentation surfaces:

- **Filament Admin** at `/admin` for ERP administration, review, and operational records.
- **Applicant Portal** at `/applicant/login` for applicant access and admission application status.
- **Student Portal** at `/student/login` for student profile, enrollment, academic activity, finance, library, hostel, and announcements.
- **Teacher Portal** at `/teacher/login` for teacher profile, assignments, timetable, classes, announcements, attendance, and marks.
- **Mobile Auth API** at `/api/v1/auth/login` using Sanctum bearer tokens.
- **KAI Context / Chat API** at `/api/v1/kai/context` and `/api/v1/kai/chat` for permission-safe student and teacher assistant context.

## Completed MVP Features

- **ERP admin foundation**: Filament resources for the university ERP foundation and operational records used by the demo.
- **IAM / roles / permissions / audit**: role-based access, permissions, policies, and audit logging across important managed records.
- **Admissions foundation**: admission batches, applicants, applications, decisions, and accepted demo flow data.
- **Applicant portal**: applicant login, dashboard, application list, application detail, and status view.
- **Applicant-to-student conversion**: accepted applications can be converted into student profiles/enrollment data through the admin workflow.
- **Student portal**: student login, dashboard, profile, enrollment, timetable, attendance, results, fees, library, hostel, and announcements.
- **Teacher portal**: teacher login, dashboard, profile, assignments, timetable, classes, announcements, attendance, and marks.
- **Teacher attendance workflow**: teachers can review assigned classes and manage attendance sessions/records within their own assignment scope.
- **Teacher marks workflow**: teachers can view assigned assessment components and enter/update student marks for their own assigned classes.
- **Sanctum mobile auth API**: student and teacher accounts can authenticate and receive bearer tokens for mobile/API access.
- **KAI student/teacher context**: KAI builds scoped context from the authenticated user role and profile.
- **KAI chat responder**: local deterministic KAI responder is available by default for demo and development safety.
- **Laravel AI SDK integration foundation**: external AI configuration and smoke command are in place, while external sending remains opt-in.
- **KAI logging/admin review**: KAI chat sessions/messages are persisted for admin review without exposing raw unrestricted data access.
- **Demo data and demo flow**: seeded demo accounts and records support the admin, applicant, student, teacher, and mobile/KAI journeys in `docs/DEMO_FLOW.md`.

## Demo-Ready User Journeys

### Admin Journey

Sign in at `/admin` as `demo.admin@kyauksetu.test` and review the seeded ERP story:

- Academic foundation: academic year, semester, department, program, major, class section, and course.
- Admissions: demo batch, applicant, application, and accepted decision.
- Student flow data: converted/active student profile, enrollment, attendance, marks, result, fee/payment, library loan, hostel allocation, and announcements.
- KAI admin review records for chat/session history.

### Applicant Journey

Sign in at `/applicant/login` as `demo.applicant@kyauksetu.test`.

- Open the applicant dashboard.
- Review the applications list.
- View application `DEMO-APP-CSE-2026-0001`.
- Confirm the accepted application status.

### Student Journey

Sign in at `/student/login` as `demo.student@kyauksetu.test`.

- Review the student dashboard and profile.
- Confirm enrollment and academic placement.
- Review timetable, attendance, results, fees, library, hostel, and announcements.
- Confirm the visible story: accepted admission is linked to an active student profile with populated academic and support records.

### Teacher Journey

Sign in at `/teacher/login` as `demo.teacher@kyauksetu.test`.

- Review teacher profile, assignments, timetable, classes, and announcements.
- Open attendance and review/manage assigned class attendance records.
- Open marks and review the seeded assessment component and student mark.

### Mobile / KAI API Journey

Use `/api/v1/auth/login` with the demo student account to get a Sanctum bearer token.

Then call:

- `/api/v1/kai/context` to inspect permission-safe KAI context.
- `/api/v1/kai/chat` to ask a local KAI question.

The demo uses the local KAI responder by default and does not require a real external AI provider.

## Current Known Limitations

- No Flutter app yet.
- External AI is not enabled by default.
- No notifications yet.
- No file upload workflow yet.
- No payment gateway.
- No SSO.
- No advanced reports/PDFs.
- No full production deployment hardening yet.

## Security Notes

- Access is role-based through Laravel policies and role/permission checks.
- Portal workflows scope data to the authenticated applicant, student, or teacher.
- Teacher attendance and marks workflows include own-assignment / own-class scoping.
- Tests cover important cross-user protection paths, including unauthorized portal access and attempts to view or update another teacher's records.
- KAI context is assembled from permission-safe user context rather than unrestricted database access.
- KAI does not receive unrestricted SQL/database access.
- External AI calls are opt-in; the default responder is local for deterministic demo behavior.
- KAI logging is designed for admin review while avoiding raw secrets and unrestricted context exposure.

## Production Readiness Gap

Before production rollout, the project still needs focused hardening in these areas:

- **Deployment**: production hosting target, build process, deploy automation, SSL, domains, and rollback plan.
- **Backups**: database backup schedule, restore testing, and retention policy.
- **Monitoring/logging**: centralized application logs, error tracking, uptime checks, and operational alerts.
- **Environment management**: production `.env` governance, secret rotation, config caching, and least-privilege credentials.
- **Queue/scheduler**: production queue worker and scheduler supervision where async work is introduced.
- **File storage**: private/public disk strategy, validation, retention, virus scanning if required, and backup policy.
- **Rate limiting review**: confirm limits for auth, KAI, and future high-risk endpoints under realistic usage.
- **Real data migration**: mapping, cleaning, import validation, and reconciliation for existing university data.
- **Permission review**: role matrix review with stakeholders before real users are onboarded.

## Recommended Next Phases

1. **Final manual browser demo pass**
   - Walk through the exact journey in `docs/DEMO_FLOW.md` with the demo accounts.
   - Confirm labels, browser layout, and stakeholder-facing story on the actual demo machine.

2. **Flutter KAI App MVP**
   - Build the mobile login and KAI context/chat experience against the existing Sanctum and KAI APIs.
   - Keep mobile scope focused on student/teacher assistant use cases first.

3. **Notification foundation**
   - Add a small notification foundation for high-value academic and administrative events.
   - Avoid broad automation until event ownership and delivery channels are confirmed.

4. **File upload foundation**
   - Add controlled document upload for admissions and student/admin records.
   - Include validation, storage visibility rules, and admin review states.

5. **Result publishing / transcript**
   - Extend results from seeded/demo visibility toward controlled publishing and transcript-ready records.
   - Keep PDF/reporting as a later production-grade step unless explicitly prioritized.

6. **Production deployment preparation**
   - Finalize deployment target, environment strategy, backups, monitoring, rate limiting, and permission matrix review.
