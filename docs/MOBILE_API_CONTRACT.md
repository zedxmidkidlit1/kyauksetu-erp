# Mobile API Contract

This contract covers the Laravel API used by the `kai-flutter` mobile app.

The endpoints below are implemented as a demo-ready backend MVP. This contract does not mean the overall system is beta or production-ready; see `docs/MVP_REVIEW.md` for the current blockers. The last documented automated baseline must be re-run in the current worktree before integration sign-off.

## Base URLs

Local host machine:

```text
http://localhost/api/v1
```

Android emulator:

```text
http://10.0.2.2/api/v1
```

## Authentication

Mobile clients authenticate with Sanctum bearer tokens.

```http
POST /api/v1/auth/login
Accept: application/json
Content-Type: application/json

{
  "email": "demo.student@kyauksetu.test",
  "password": "DemoPass123!",
  "device_name": "Android Emulator"
}
```

Successful responses return:

```json
{
  "data": {
    "token": "...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Maya Hlaing",
      "email": "demo.student@kyauksetu.test"
    },
    "roles": ["student"],
    "primary_role": "student",
    "profile": {}
  }
}
```

Send the token on protected requests:

```http
Authorization: Bearer <token>
Accept: application/json
```

Supported mobile roles are `student` and `teacher`. Applicant, admin, and back-office roles are intentionally not exposed to the Flutter API until a mobile workflow is defined for them.

The login route is currently limited to 10 requests per minute. Protected data routes require both Sanctum authentication and the `mobile` token ability. KAI chat still needs its own production rate limit.

## Query Parameters

List endpoints support:

| Parameter | Type | Notes |
| --- | --- | --- |
| `page` | integer | Minimum `1` |
| `per_page` | integer | Minimum `1`, maximum `50`, default `15` |
| `from` | date | Inclusive date filter |
| `to` | date | Inclusive date filter, must be on or after `from` when `from` is present |

Paginated responses include Laravel `links` and `meta`.

## Endpoints

| Endpoint | Method | Roles | Notes |
| --- | --- | --- | --- |
| `/auth/login` | POST | student, teacher | Returns a Sanctum token with the `mobile` ability |
| `/auth/me` | GET | student, teacher | Current mobile user, roles, and profile summary |
| `/auth/logout` | POST | student, teacher | Revokes the current token |
| `/kai/context` | GET | student, teacher | Permission-safe KAI context |
| `/kai/chat` | POST | student, teacher | Body: `{ "message": "..." }` |
| `/notifications` | GET | student, teacher | Announcement-backed notification feed |
| `/me` | GET | student | Basic user record |
| `/my-profile` | GET | student | Student profile |
| `/my-enrollment` | GET | student | Active enrollment |
| `/my-timetable` | GET | student | Paginated timetable data |
| `/my-attendance` | GET | student | Paginated attendance records |
| `/my-results` | GET | student | Paginated course results |
| `/my-fees` | GET | student | Paginated fees and payments |
| `/my-library` | GET | student | Paginated library loans |
| `/my-hostel` | GET | student | Paginated hostel allocations |
| `/announcements` | GET | student | Paginated visible announcements |

## Error Shape

API routes force JSON errors.

Validation errors:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

Authentication and authorization errors use standard JSON responses with HTTP `401` or `403`.

## Demo Data

Seed non-production demo data with:

```bash
vendor/bin/sail artisan db:seed --class=DemoDataSeeder
```

Demo mobile accounts use:

```text
DemoPass123!
```

| Role | Email |
| --- | --- |
| Student | `demo.student@kyauksetu.test` |
| Teacher | `demo.teacher@kyauksetu.test` |

## Current Contract Limitations

- `/notifications` is an announcement-backed feed; there is no push delivery or unread state.
- The Flutter application is not included in this repository.
- Applicant, admin, and back-office mobile flows are unsupported.
- Production HTTPS, domain, CORS, token-expiration, monitoring, and secret-management values are not finalized.
- External KAI calls remain opt-in and currently fall back to the local responder when the provider fails.
