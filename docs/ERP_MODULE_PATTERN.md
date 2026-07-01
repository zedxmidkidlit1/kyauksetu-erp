# ERP Module Pattern

Use this guide for future foundation-only ERP modules. Keep each module small, admin-facing, and conventional: data model, admin CRUD, IAM permissions, audit logging, and targeted verification.

## Scope Rule

Foundation modules should not grow into portals, integrations, notifications, analytics engines, exports, PDFs, payments, automation workflows, or background jobs unless the prompt explicitly asks for them.

## 1. Module Structure

- Models live in `app/Models` and use descriptive singular names.
- Migrations live in `database/migrations` and use plural snake_case table names.
- Policies live in `app/Policies` and map directly to module permissions.
- Filament resources live in `app/Filament/Resources/{PluralName}`.
- Seed new permissions through `database/seeders/IamRolePermissionSeeder.php`.
- Add relationships on both sides when useful for admin screens and future modules.

Typical Filament resource shape:

```text
app/Filament/Resources/Books/
  BookResource.php
  Pages/CreateBook.php
  Pages/EditBook.php
  Pages/ListBooks.php
  Schemas/BookForm.php
  Tables/BooksTable.php
  RelationManagers/...RelationManager.php   # only when useful
```

Model conventions:

- Use the same fillable style as existing models, either `#[Fillable([...])]` or `protected $fillable = [...]`.
- Define Eloquent relationships with explicit return types.
- Use model `$attributes` for simple default statuses.
- Use `casts(): array` for dates, decimals, booleans, and enums/status-like fields when needed.
- Eager load common table relationships in `Resource::getEloquentQuery()` when it prevents N+1 queries.

## 2. Database Conventions

- Table names are plural snake_case, for example `student_fees`.
- Foreign keys are singular snake_case with `_id`, for example `student_profile_id`.
- Include timestamps on foundation tables.
- Optional descriptive fields should be nullable, for example `description`, `remarks`, `code`, `subtitle`.
- Add unique constraints for natural identifiers such as `code`, `isbn`, `barcode`, `accession_no`.
- Add scoped unique constraints where the value is only unique under a parent, for example room number per hostel or bed number per room.
- Add indexes for foreign keys, statuses, dates, and common admin filters.
- Use basic check constraints for safe numeric rules, such as non-negative amounts.
- Use PostgreSQL partial unique indexes where already established and clean, especially for active-only constraints such as one active hostel allocation per student or bed.

Keep migrations readable. Prefer clear schema definitions plus small `DB::statement()` calls only when Laravel schema builder does not express the constraint cleanly.

## 3. IAM Conventions

Permission names use plural resource keys plus action:

```text
books.view
books.create
books.update
books.delete
student_fees.view
student_fees.create
student_fees.update
student_fees.delete
```

Policy pattern:

- Include a `before()` super-admin bypass.
- `viewAny()` and `view()` check `{resource}.view`.
- `create()` checks `{resource}.create`.
- `update()` checks `{resource}.update`.
- `delete()` checks `{resource}.delete`.
- Leave `restore()` and `forceDelete()` as `false` unless explicitly requested.

After adding permissions, assign them to `super_admin` in `IamRolePermissionSeeder` using the existing seeder pattern. Re-run the IAM seeder only when permissions changed.

## 4. Audit Logging

Foundation models should be audited when they represent admin-managed ERP records.

Use Spatie activity log consistently:

- Add the activity log trait used by sibling audited models.
- Log fillable attributes.
- Log only dirty changes.
- Avoid empty logs.
- Use the module log name.

Common log names:

- `iam`
- `academic`
- `sis`
- `facilities`
- `communication`
- `library`
- `hostel`
- `finance`
- `inventory`

Audit all core models in a new foundation module unless the prompt says otherwise.

## 5. Filament Conventions

- Group resources by module with `$navigationGroup`, for example `Library`, `Hostel`, `Finance`, or `Inventory`.
- Use `Filament\Support\Icons\Heroicon` for navigation icons.
- Keep forms clean with `Section` and `Grid`.
- Use searchable and preloaded relationship selects:

```php
Select::make('book_id')
    ->relationship('book', 'title')
    ->searchable()
    ->preload()
    ->required();
```

- Use status/category selects with clear fixed options.
- Use numeric inputs with sensible `minValue()` rules for amounts, years, capacity, and quantities.
- Use date or date-time pickers for date fields.
- Tables should show the most useful identifying fields, relationships, status, and key dates.
- Use badges for statuses and categories.
- Add filters for status/category and important relationships.
- Add relation managers or repeaters only when they simplify a natural parent-child workflow.
- Keep resources CRUD-focused and admin-friendly. Do not add dashboards, charts, exports, or custom workflows unless requested.

## 6. Verification Rules

Run only the checks that match the change:

- Run migrations only when migrations are added or changed.
- Re-run `IamRolePermissionSeeder` only when permissions changed.
- Run Pint on dirty files after PHP edits.
- Run compact tests for functional changes.
- Verify only targeted admin route paths for new Filament resources.
- Use targeted Boost/schema checks only when useful.
- Do not run full `route:list`.
- Do not print long command outputs.

Preferred commands:

```bash
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed --class=IamRolePermissionSeeder
vendor/bin/sail bin pint --dirty --format agent
vendor/bin/sail artisan test --compact
vendor/bin/sail artisan route:list --path=admin/{module-path} --except-vendor
```

For docs-only changes, do not run migrations, seeders, route checks, or tests unless the prompt specifically asks.

## 7. Reporting Format

Final reports should be short and operational:

```text
Changed files:
- app/Models/Example.php
- app/Filament/Resources/Examples/ExampleResource.php

Commands run:
- vendor/bin/sail artisan migrate
- vendor/bin/sail bin pint --dirty --format agent
- vendor/bin/sail artisan test --compact

Pass/fail summary:
- Migrations: PASS
- IAM seeder: PASS
- Pint: PASS
- Compact tests: PASS
- Targeted admin routes: PASS

Remaining manual steps:
- None
```

If a command was intentionally skipped, say why in one line.

## Short Prompt Template

```text
Implement the {Module Name} Foundation following docs/ERP_MODULE_PATTERN.md.

Scope:
- Add models/migrations for: ...
- Add relationships: ...
- Add audit logging.
- Add Filament resources under {Navigation Group}.
- Add permissions and policies.
- Assign new permissions to super_admin in IamRolePermissionSeeder.
- Add reasonable indexes and constraints.

Verification:
- Run migrations if migrations were added.
- Re-run IAM seeder if permissions changed.
- Run Pint on dirty files.
- Run compact tests.
- Verify only targeted admin route paths.
- Do not run full route:list or print long outputs.
```
