# Demo Flow

Run the demo data seeder in a non-production environment:

```bash
vendor/bin/sail artisan db:seed --class=DemoDataSeeder
```

## Demo Credentials

All demo accounts use the password `DemoPass123!`.

| Role | Email | Purpose |
| --- | --- | --- |
| Super admin | `demo.admin@kyauksetu.test` | Admin review across ERP modules |
| Admission officer | `demo.admissions@kyauksetu.test` | Admission demo account using the existing registrar role |
| Teacher | `demo.teacher@kyauksetu.test` | Teacher portal, timetable, attendance, marks |
| Student | `demo.student@kyauksetu.test` | Student portal and KAI mobile/API context |
| Applicant | `demo.applicant@kyauksetu.test` | Applicant portal and admission application |

## Admin Path

1. Sign in at `/admin` as `demo.admin@kyauksetu.test`.
2. Review the academic foundation: academic year `2026-2027 Demo`, semester `Semester 1 Demo`, department `CSE-DEMO`, program `BTECH-DEMO`, major `CSE-DEMO`, class section `CSE First Year - Section A`, and course `CSE-101-DEMO`.
3. Review admissions: batch `DEMO-ADM-2026`, applicant `DEMO-APP-2026-0001`, application `DEMO-APP-CSE-2026-0001`, and the accepted decision.
4. Review student flow data: student profile `DEMO-STU-2026-0001`, enrollment, attendance, marks, published result, fee/payment, active library loan, active hostel allocation, and announcements.

## Applicant Path

1. Sign in at `/applicant/login` as `demo.applicant@kyauksetu.test`.
2. Open the dashboard and applications list.
3. View application `DEMO-APP-CSE-2026-0001` and its accepted status.

## Student Path

1. Sign in at `/student/login` as `demo.student@kyauksetu.test`.
2. Review profile, enrollment, timetable, attendance, results, fees, library, hostel, and announcements.
3. Confirm the visible story: accepted admission is linked to the active student profile, with course activity and support records already populated.

## Teacher Path

1. Sign in at `/teacher/login` as `demo.teacher@kyauksetu.test`.
2. Review profile, assignments, timetable, classes, announcements, attendance, and marks.
3. Open the demo assessment component to see the seeded student mark.

## KAI API/Mobile Path

The demo uses the local KAI responder by default. It does not enable a real external AI provider.

1. Log in as the demo student:

```bash
curl -s -X POST http://localhost/api/v1/auth/login \
  -H 'Accept: application/json' \
  -d 'email=demo.student@kyauksetu.test' \
  -d 'password=DemoPass123!' \
  -d 'device_name=Demo Mobile'
```

2. Use the returned bearer token to inspect KAI context:

```bash
curl -s http://localhost/api/v1/kai/context \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer <token>'
```

3. Ask a local KAI question:

```bash
curl -s -X POST http://localhost/api/v1/kai/chat \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer <token>' \
  -d 'message=What should I focus on this week?'
```
