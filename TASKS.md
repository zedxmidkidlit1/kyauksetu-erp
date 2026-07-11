# Kyauksetu ERP Tasks

## Current Milestone

### Milestone 1: Beta Scope Completion

**Objective:** Freeze the Beta v1 product boundary, ownership, and access model so the team has one defensible definition of beta.

**Exit criteria:**

- Beta v1 scope and exclusions are approved.
- Stakeholders approve the operational role-permission matrix.
- Registrar ownership and allowed actions are unambiguous.
- Feed-only notifications and local-only KAI are accepted for beta.
- Project documentation consistently distinguishes beta workflows from foundation-only modules.

## Now

1. [ ] **Approve and freeze the Beta v1 scope and exclusions**
   - Expected outcome: The team treats the admin-assisted academic journey as Beta v1 and stops presenting broad ERP foundations as beta-ready.
   - Acceptance criteria: The scope in `ROADMAP.md` is accepted; applicant, registrar, student, teacher, mobile, announcements, and local KAI boundaries are explicit; deferred modules remain excluded.
   - Relevant files: `ROADMAP.md`, `README.md`, `docs/MVP_REVIEW.md`

2. [ ] **Obtain stakeholder approval for operational role boundaries**
   - Expected outcome: Each back-office role has an agreed product responsibility and access boundary.
   - Acceptance criteria: The registrar and other operational roles are approved or explicitly restricted; the agreed behavior matches the locally verified permission matrix; unresolved decisions are recorded.
   - Relevant modules: IAM, Filament administration, registrar admissions

3. [ ] **Reconcile remaining registrar and readiness documentation**
   - Expected outcome: Users can tell which registrar behavior is implemented, approved, and supported without contradictory claims.
   - Acceptance criteria: `README.md`, `docs/MVP_REVIEW.md`, and `docs/DEMO_FLOW.md` consistently separate implemented role mappings from pending stakeholder and manual approval.

4. [ ] **Confirm the Beta v1 notification boundary**
   - Expected outcome: Announcement-backed feeds are either accepted as the beta notification product or excluded from beta claims.
   - Acceptance criteria: The decision is reflected consistently in the roadmap, mobile contract, demo flow, and MVP review; push delivery and unread state remain deferred unless the scope decision changes.
   - Relevant modules: Announcements, mobile notifications

5. [ ] **Confirm the Beta v1 KAI boundary**
   - Expected outcome: Local deterministic KAI is the supported beta behavior, with external providers outside the release boundary.
   - Acceptance criteria: Product documentation consistently describes local KAI as supported; external provider enablement remains deferred pending its operational controls.
   - Relevant modules: KAI context, chat, logging

## Next

1. [ ] **Validate registrar admissions administration**
   - Expected outcome: The registrar can perform only the approved admissions responsibilities.
   - Acceptance criteria: The supported registrar journey completes as documented, and disallowed cross-module actions remain unavailable.

2. [ ] **Validate the applicant-to-enrolled-student journey**
   - Expected outcome: A valid applicant can progress from submission through acceptance and conversion without duplicate or inconsistent records.
   - Acceptance criteria: The documented journey completes with correct program, major, intake, profile, and enrollment outcomes.

3. [ ] **Validate teacher attendance and marks**
   - Expected outcome: Teachers can manage their own assigned classes without accessing another teacher's students or work.
   - Acceptance criteria: Attendance and marks journeys complete for eligible students, and ownership boundaries hold.

4. [ ] **Validate student portal, mobile, announcements, and local KAI**
   - Expected outcome: Students can consume the supported beta experience without cross-user data exposure.
   - Acceptance criteria: Portal and mobile data remain scoped to the authenticated student; announcements and local KAI behave as documented.

5. [ ] **Resolve release-blocking stabilization defects**
   - Expected outcome: Milestone 2 finishes without known workflow, authorization, isolation, or data-integrity defects that block beta.
   - Acceptance criteria: Defects found during supported journey validation are resolved with focused regression coverage, and observed behavior matches the documentation.

## Blocked

- **Operational role approval:** Requires stakeholder decisions on global access boundaries before the registrar workflow can be declared beta-ready.
- **Complete manual journey acceptance:** Requires the approved role matrix and an available non-production environment with the documented demo data.
- **Beta release readiness:** Requires completion of scope approval and stabilization before environment controls and stakeholder pilot acceptance can serve as a release gate.

## Recently Completed

- [x] Established a passing local baseline of 90 tests and 468 assertions.
- [x] Verified the focused hardening selection with 32 tests and 140 assertions.
- [x] Completed Pint without formatting changes for the verified hardening work.
- [x] Restricted Filament access and dashboard widgets through explicit authorization boundaries.
- [x] Implemented and locally verified least-privilege operational role mappings.
- [x] Hardened admissions consistency and duplicate-submission handling.
- [x] Hardened teacher attendance eligibility and assignment ownership.
- [x] Implemented named throttling, sanitized KAI provider failure reporting, and local fallback behavior.

## Deferred

- New ERP modules and additional generic administrative CRUD
- Flutter application development
- Push notifications and unread state
- Payment gateway and production reconciliation
- SSO login behavior
- Controlled uploads, retention, and malware scanning
- Advanced reports, transcripts, exports, and PDFs
- External AI provider enablement
- Inventory and HR workflow expansion
- Broad portal visual redesign
- Production-scale automation and real-data migration
