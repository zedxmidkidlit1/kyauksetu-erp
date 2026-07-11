# Kyauksetu ERP Product Roadmap

## Current Stage

Kyauksetu ERP is a demo-ready backend MVP, beyond prototype but not yet beta or production-ready.

The repository contains working applicant, student, teacher, mobile, and KAI journeys supported by a broad Filament administration and ERP data foundation. The strongest workflows have focused security, authorization, isolation, and integrity coverage. The next phase is to freeze a credible Beta v1 boundary, validate its complete cross-role journey, and prepare it for a controlled pilot.

Additional ERP breadth should not be treated as beta-ready merely because models and administrative CRUD exist.

## Beta v1 Scope

Beta v1 is an admin-assisted university academic workflow:

- Super-admin and an approved registrar manage essential academic setup and admissions.
- Applicants register, submit applications, review status, and progress through an accepted-applicant conversion.
- Accepted applicants become enrolled students.
- Students view their profile, enrollment, timetable, attendance, results, fees, library status, hostel status, and announcements.
- Teachers view assigned teaching activity and manage attendance and marks for their own classes.
- Student and teacher mobile users authenticate against the supported API boundary.
- Students consume their academic data through the mobile API.
- Students and teachers receive announcement-backed notification feeds.
- Local deterministic KAI provides permission-safe context and chat with administrative logging.

Beta v1 does not promise complete operational workflows for every ERP domain.

## Module Status Matrix

| Status | Modules | Product position |
| --- | --- | --- |
| Ready for beta validation | Applicant registration and applications; applicant conversion; student portal; teacher portal; attendance; marks; mobile authentication; student mobile API; announcement feed; local KAI and logging | Implemented with focused workflow and access-boundary evidence; requires release-wide manual acceptance |
| Needs completion for beta | Registrar workflow; operational role approval; essential academic setup; end-to-end demo acceptance; beta environment controls; documentation consistency | Required to establish an approved, supportable Beta v1 |
| Foundation only | Extended academic catalog; examinations and result publication; finance and payments; library operations; hostel operations; inventory and assets; HR and staff; document records; SSO identity mapping | Models and administrative surfaces exist, but complete operational maturity is not established |
| After beta | Flutter application; push notifications and unread state; payment gateway; SSO login; controlled uploads; advanced reports, transcripts, exports, and PDFs; external AI rollout; production-scale migration and operations automation | Excluded from Beta v1 to protect scope |

## Milestone 1: Beta Scope Completion

### Objective

Freeze the Beta v1 product boundary, ownership, and access model so the team has one defensible definition of beta.

### Included Modules

- IAM and operational roles
- Super-admin and registrar administration
- Essential academic setup
- Admissions and applicant conversion
- Student and teacher boundaries
- Mobile, announcements, and local KAI product decisions

### Exit Criteria

- Beta v1 scope and exclusions are approved.
- Stakeholders approve the operational role-permission matrix.
- Registrar ownership and allowed actions are unambiguous.
- Feed-only notifications and local-only KAI are accepted for beta.
- Documentation consistently distinguishes beta workflows from foundation-only modules.

## Milestone 2: Stabilization

### Objective

Validate the complete academic journey across supported roles and resolve only defects that block the Beta v1 outcome.

### Included Modules

- Registrar admissions administration
- Applicant submission, decision, and conversion
- Student enrollment and self-service
- Teacher attendance and marks
- Student mobile data
- Announcement feed
- Local KAI context and chat

### Exit Criteria

- Super-admin, registrar, applicant, student, and teacher journeys pass manual acceptance.
- Mobile authentication, student data, announcements, and local KAI pass manual acceptance.
- Cross-role and cross-user boundaries behave as approved.
- No release-blocking workflow, isolation, or data-integrity defects remain.
- Fixes made during stabilization have focused regression coverage.
- Supported behavior and known limitations match the documentation.

## Milestone 3: Beta Release Readiness

### Objective

Make the validated Beta v1 safe and supportable for a controlled stakeholder pilot.

### Included Modules

- Beta environment configuration
- Authentication and security controls
- Secrets and access ownership
- Monitoring and operational reporting
- Backup, restore, and rollback ownership
- Pilot support and acceptance

### Exit Criteria

- A controlled beta environment is available.
- HTTPS, CORS, token policy, secrets, and rate-limit expectations are approved.
- Monitoring and sanitized error reporting cover supported beta journeys.
- Backup, restore, and rollback responsibilities are defined and demonstrated.
- Automated release verification succeeds before release.
- Stakeholder pilot acceptance is complete.
- A formal beta go/no-go decision is recorded against this roadmap.

## Deferred Until After Beta

- New ERP modules and additional generic administrative CRUD
- Flutter application development
- Push delivery and unread notification state
- Payment gateway and production reconciliation
- SSO login behavior
- Controlled upload, retention, and malware-scanning platform
- Advanced reports, transcripts, exports, and PDFs
- External AI provider enablement
- Inventory and HR workflow expansion
- Broad portal visual redesign
- Production-scale automation and real-data migration

## Next 10 Priorities

1. Approve and freeze Beta v1 scope and exclusions.
2. Obtain stakeholder approval for operational role-permission boundaries.
3. Reconcile remaining registrar, mobile, KAI, and readiness documentation contradictions.
4. Validate the registrar admissions journey against the approved role matrix.
5. Validate the complete applicant-to-enrolled-student journey.
6. Validate teacher attendance and marks, including ownership boundaries.
7. Validate student portal, mobile data isolation, announcements, and local KAI.
8. Resolve only release-blocking defects discovered during beta validation.
9. Establish the minimum beta environment and operational controls.
10. Run stakeholder pilot acceptance and make the beta go/no-go decision.
