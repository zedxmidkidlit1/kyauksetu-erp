# Frontend API Usage Guide for kai-flutter

## Purpose

This guide explains how the `kai-flutter` mobile app should consume API data from the `kyauksetu-erp` Laravel backend.

It is written for Flutter developers and AI coding agents working in the Flutter project. Treat this as the frontend-facing companion to `docs/MOBILE_API_CONTRACT.md` in the Laravel repo.

The Laravel endpoints are implemented as a demo-ready backend MVP. Do not treat this guide as production-readiness approval. Review `docs/MVP_REVIEW.md`, re-run the Laravel test suite in the current worktree, and close the documented security/data-integrity blockers before production integration sign-off.

## Backend Base URLs

Use the environment-specific host as the Flutter `API_BASE_URL`:

| Runtime | Base URL |
| --- | --- |
| Local desktop/browser | `http://localhost` |
| Android emulator | `http://10.0.2.2` |

The Laravel API prefix is:

```text
/api/v1
```

Flutter run example for Android emulator:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2
```

Flutter should receive only the host/base URL from `dart-define`. Append `/api/v1` internally in one API client/config file, for example:

```dart
const apiBaseUrl = String.fromEnvironment('API_BASE_URL');
const apiPrefix = '/api/v1';
```

Feature code should call named API client methods or shared endpoint constants. Do not hardcode full URLs repeatedly across screens or repositories.

## Required Request Headers

Every API request should send:

```http
Accept: application/json
```

POST requests with JSON bodies should also send:

```http
Content-Type: application/json
```

Protected endpoints require:

```http
Authorization: Bearer <token>
```

## Authentication Flow for Flutter

Login with:

```http
POST /api/v1/auth/login
```

Example login body:

```json
{
  "email": "demo.student@kyauksetu.test",
  "password": "DemoPass123!",
  "device_name": "Android Emulator"
}
```

Login returns a Laravel Sanctum bearer token. Store it using the Flutter app's existing secure/local storage approach. Use that token for all protected requests.

On app startup, restore the saved token and verify the session with:

```http
GET /api/v1/auth/me
```

Logout with:

```http
POST /api/v1/auth/logout
```

After logout succeeds, clear the local token and any cached user/profile state.

Supported mobile roles are only `student` and `teacher`. Applicant, admin, and back-office mobile login is intentionally unsupported by the Laravel API.

## Endpoint Usage Table

All endpoints below are relative to `/api/v1`.

| Flutter Feature | Method | Endpoint | Auth | Expected Data | Notes |
| --- | --- | --- | --- | --- | --- |
| Login | POST | `/auth/login` | No | Token, user, roles, primary role, profile summary | Supports `student` and `teacher` only |
| Session restore | GET | `/auth/me` | Yes | Current mobile auth user payload | Use after loading a saved token |
| Logout | POST | `/auth/logout` | Yes | Revocation confirmation | Clear local session after success |
| Basic user | GET | `/me` | Yes | `{ id, name, email }` | Student-only route |
| Profile screen | GET | `/my-profile` | Yes | Student profile | Student-only route |
| Enrollment | GET | `/my-enrollment` | Yes | Active student enrollment | Student-only route |
| Timetable | GET | `/my-timetable` | Yes | Paginated timetable records | Supports pagination/query parameters |
| Attendance | GET | `/my-attendance` | Yes | Paginated attendance records | Supports pagination/query parameters |
| Results | GET | `/my-results` | Yes | Paginated course results | Supports pagination/query parameters |
| Fees | GET | `/my-fees` | Yes | Paginated fee records with payments | Supports pagination/query parameters |
| Library | GET | `/my-library` | Yes | Paginated library loans | Supports pagination/query parameters |
| Hostel | GET | `/my-hostel` | Yes | Paginated hostel allocations | Supports pagination/query parameters |
| Announcements | GET | `/announcements` | Yes | Paginated visible announcements | Student-only route |
| Notifications | GET | `/notifications` | Yes | Paginated announcement-backed notification feed | Student and teacher route |
| KAI context | GET | `/kai/context` | Yes | Permission-safe student/teacher context | Use for home/chat preloading if needed |
| KAI chat | POST | `/kai/chat` | Yes | Reply, context keys, suggestions | Body: `{ "message": "..." }` |

## Response Shape Rules

Single-resource endpoints usually return:

```json
{
  "data": {}
}
```

List endpoints usually return:

```json
{
  "data": [],
  "links": {},
  "meta": {}
}
```

Notifications currently return a paginated `data` array plus Laravel pagination `links` and `meta`. The current backend does not expose `meta.unread_count`; do not depend on unread counts until the backend adds that field.

Convert error responses into friendly UI messages. Do not show raw server traces or raw validation JSON directly to users.

### Login Success

```json
{
  "data": {
    "token": "1|example-token",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Maya Hlaing",
      "email": "demo.student@kyauksetu.test"
    },
    "roles": ["student"],
    "primary_role": "student",
    "profile": {
      "type": "student",
      "id": 1,
      "student_no": "DEMO-STU-2026-0001",
      "roll_no": "CSE-1A-001",
      "status": "active",
      "program": {
        "id": 1,
        "name": "Bachelor of Technology",
        "code": "BTECH-DEMO"
      },
      "major": {
        "id": 1,
        "name": "Computer Science",
        "code": "CSE-DEMO"
      },
      "class_section": {
        "id": 1,
        "name": "CSE First Year - Section A",
        "section": "A"
      }
    }
  }
}
```

### Current User

`GET /auth/me` returns the same auth user payload shape as login, without `token` and `token_type`:

```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "Maya Hlaing",
      "email": "demo.student@kyauksetu.test"
    },
    "roles": ["student"],
    "primary_role": "student",
    "profile": {
      "type": "student",
      "id": 1,
      "student_no": "DEMO-STU-2026-0001",
      "roll_no": "CSE-1A-001",
      "status": "active",
      "program": null,
      "major": null,
      "class_section": null
    }
  }
}
```

### Announcements List

```json
{
  "data": [
    {
      "id": 1,
      "title": "Demo Orientation Week Schedule",
      "body": "Welcome to orientation week.",
      "announcement_type": "general",
      "priority": "normal",
      "publish_at": "2026-07-01T08:00:00.000000Z",
      "expires_at": null
    }
  ],
  "links": {
    "first": "http://localhost/api/v1/announcements?page=1",
    "last": "http://localhost/api/v1/announcements?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

### Notifications List

```json
{
  "data": [
    {
      "id": "announcement:1",
      "type": "announcement",
      "title": "Demo Orientation Week Schedule",
      "body": "Welcome to orientation week.",
      "priority": "normal",
      "published_at": "2026-07-01T08:00:00.000000Z",
      "expires_at": null,
      "read_at": null,
      "data": {
        "announcement_id": 1,
        "announcement_type": "general"
      }
    }
  ],
  "links": {
    "first": "http://localhost/api/v1/notifications?page=1",
    "last": "http://localhost/api/v1/notifications?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

### KAI Chat Response

```json
{
  "data": {
    "reply": "I found 1 upcoming timetable item(s) in your student context.",
    "context_used": {
      "keys": [
        "user",
        "student_profile",
        "current_enrollment",
        "today_upcoming_timetable"
      ]
    },
    "suggestions": [
      "Show my timetable",
      "Check unpaid fees",
      "Show latest results"
    ]
  }
}
```

### Validation Error

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```

### Unauthenticated Error

```json
{
  "message": "Unauthenticated."
}
```

### Forbidden Error

```json
{
  "message": "This mobile endpoint is not available for this account."
}
```

## Pagination and Query Parameters

Paginated student list endpoints and notifications support:

| Parameter | Type | Notes |
| --- | --- | --- |
| `page` | integer | Minimum `1` |
| `per_page` | integer | Minimum `1`, maximum `50`, default `15` |
| `from` | date | Inclusive date filter |
| `to` | date | Inclusive date filter, must be on or after `from` when `from` is present |

Examples:

```http
GET /api/v1/my-results?page=1&per_page=10
GET /api/v1/my-attendance?from=2026-07-01&to=2026-07-31&per_page=20
GET /api/v1/notifications?per_page=10
```

Flutter repositories should:

- Handle empty `data` arrays.
- Treat pagination `meta` as optional but useful.
- Preserve `page`, `per_page`, and filters when loading the next page.
- Avoid assuming list responses are plain arrays.
- Safely handle `null` relationship fields.

## Error Handling Guide

| Status | Meaning | Flutter Behavior |
| --- | --- | --- |
| `401` | Missing, invalid, or expired token | Clear local session and redirect to login |
| `403` | Authenticated but wrong role/profile | Show "This feature is not available for your role" |
| `422` | Validation error | Show the first field validation message near the relevant input |
| `429` | Rate limited | Show a retry-later message and avoid immediate repeated requests |
| `500` | Server error | Show a generic error and log/report diagnostics |
| Network timeout/offline | Backend unreachable | Show retry/offline state; keep mock fallback explicit |

Recommended frontend rules:

- Never silently swallow auth failures.
- Do not let mock fallback hide real API errors during integration testing.
- Log status code, endpoint, and request ID if available.
- Avoid storing sensitive tokens in plain text storage.

## Screen-to-API Mapping

| Flutter Screen/Flow | API Calls |
| --- | --- |
| Login screen | `POST /auth/login` |
| App startup/session restore | `GET /auth/me` |
| Home screen | `GET /my-profile`, `GET /announcements`, `GET /notifications`, `GET /kai/context` |
| Chat screen | `POST /kai/chat` |
| Profile screen | `GET /my-profile`, `GET /my-enrollment` |
| Academic screens | `GET /my-timetable`, `GET /my-attendance`, `GET /my-results` |
| Services screens | `GET /my-fees`, `GET /my-library`, `GET /my-hostel` |
| Notifications screen | `GET /notifications` |

## Mock Fallback Policy

Use the real Laravel API when `API_BASE_URL` is provided.

Mock fallback can remain for development, UI prototyping, or offline fallback. During real integration testing, mock fallback must not hide backend errors silently. If a real API request fails, the app should show an error state or require an explicit developer setting before falling back to mock data.

Recommended behavior:

- `API_BASE_URL` present: use real API.
- `API_BASE_URL` missing: use mock/offline mode if the app supports it.
- Real API returns `401`, `403`, `422`, `429`, or `500`: handle the error; do not replace it with mock success data.

## Manual Verification Checklist for Flutter

Start Laravel Sail:

```bash
vendor/bin/sail up -d
```

Run migrations:

```bash
vendor/bin/sail artisan migrate
```

Seed demo data:

```bash
vendor/bin/sail artisan db:seed --class=DemoDataSeeder
```

Start Flutter with the Android emulator URL:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2
```

Verify:

- Login as `demo.student@kyauksetu.test` with `DemoPass123!`.
- Session restores after app restart through `/auth/me`.
- Home screen loads profile, announcements, notifications, and KAI context.
- Profile screen loads profile and enrollment.
- Academic screens load timetable, attendance, and results.
- Services screens load fees, library, and hostel data.
- Notifications screen loads announcement-backed notification items.
- Chat screen sends a message to `/kai/chat` and displays the reply/suggestions.
- Invalid token or logout redirects to login.
- Teacher login works for shared student/teacher features but is blocked from student-only endpoints.

## Current Limitations

- Notifications are announcement-backed API notifications, not FCM/APNs push notifications yet.
- Notification unread counts are not currently exposed by the backend.
- KAI uses the local deterministic fallback unless external provider env is configured.
- KAI chat does not yet have an endpoint-specific production rate limit, and external-provider failures currently fall back locally without operational reporting.
- Applicant/admin mobile flows are not supported yet.
- Production still needs real HTTPS/domain/CORS/env values.
- The last documented Laravel test baseline is historical and must be re-run through Sail before integration sign-off.
