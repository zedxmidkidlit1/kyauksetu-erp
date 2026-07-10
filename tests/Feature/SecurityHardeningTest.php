<?php

namespace Tests\Feature;

use App\Filament\Widgets\AcademicOverview;
use App\Filament\Widgets\AttendanceOverview;
use App\Filament\Widgets\CommunicationOverview;
use App\Filament\Widgets\ExamsResultsOverview;
use App\Filament\Widgets\IdentityPeopleOverview;
use App\Filament\Widgets\StudentStatusOverview;
use App\Filament\Widgets\TodayUpcomingAcademicActivity;
use App\Models\AdmissionApplication;
use App\Models\Book;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\IamRolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_explicit_back_office_roles_can_access_the_admin_panel(): void
    {
        $this->seed(IamRolePermissionSeeder::class);
        $panel = Filament::getPanel('admin');

        foreach (['super_admin', 'registrar', 'department_admin', 'librarian', 'hostel_warden', 'finance_officer'] as $role) {
            $user = User::factory()->create(['email' => "{$role}@example.test"]);
            $user->assignRole($role);

            $this->assertTrue($user->canAccessPanel($panel), "Expected {$role} to access the admin panel.");
        }

        foreach (['teacher', 'student', 'applicant'] as $role) {
            $user = User::factory()->create(['email' => "denied-{$role}@example.test"]);
            $user->assignRole($role);

            $this->assertFalse($user->canAccessPanel($panel), "Expected {$role} to be denied admin panel access.");
        }

        $this->assertFalse(User::factory()->create()->canAccessPanel($panel));

        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student)->get('/admin')->assertForbidden();

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');
        $this->actingAs($registrar)->get('/admin')->assertOk();
    }

    public function test_dashboard_widgets_require_module_permissions(): void
    {
        $this->seed(IamRolePermissionSeeder::class);
        $widgets = [
            AcademicOverview::class,
            AttendanceOverview::class,
            CommunicationOverview::class,
            ExamsResultsOverview::class,
            IdentityPeopleOverview::class,
            StudentStatusOverview::class,
            TodayUpcomingAcademicActivity::class,
        ];

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');
        $this->actingAs($superAdmin);

        foreach ($widgets as $widget) {
            $this->assertTrue($widget::canView(), "Expected super_admin to view {$widget}.");
        }

        $librarian = User::factory()->create();
        $librarian->assignRole('librarian');
        $this->actingAs($librarian);

        $this->assertTrue(StudentStatusOverview::canView());
        $this->assertFalse(IdentityPeopleOverview::canView());
        $this->assertFalse(AcademicOverview::canView());
        $this->assertFalse(AttendanceOverview::canView());
        $this->assertFalse(CommunicationOverview::canView());
        $this->assertFalse(ExamsResultsOverview::canView());
        $this->assertFalse(TodayUpcomingAcademicActivity::canView());

        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student);

        foreach ($widgets as $widget) {
            $this->assertFalse($widget::canView(), "Expected student to be denied {$widget}.");
        }
    }

    public function test_operational_roles_receive_a_least_privilege_permission_matrix(): void
    {
        $this->seed(IamRolePermissionSeeder::class);

        $this->assertRoleCanAndCannot('registrar', 'admission_applications.update', 'books.update');
        $this->assertRoleCanAndCannot('department_admin', 'courses.update', 'student_payments.update');
        $this->assertRoleCanAndCannot('librarian', 'library_loans.update', 'admission_applications.update');
        $this->assertRoleCanAndCannot('hostel_warden', 'hostel_allocations.update', 'student_fees.update');
        $this->assertRoleCanAndCannot('finance_officer', 'student_payments.update', 'books.update');

        foreach (['teacher', 'student', 'applicant'] as $role) {
            $this->assertCount(0, Role::findByName($role, 'web')->permissions);
        }

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');
        $this->assertTrue($registrar->can('viewAny', AdmissionApplication::class));
        $this->assertFalse($registrar->can('viewAny', Book::class));

        $librarian = User::factory()->create();
        $librarian->assignRole('librarian');
        $this->assertTrue($librarian->can('viewAny', Book::class));
        $this->assertFalse($librarian->can('viewAny', AdmissionApplication::class));
    }

    public function test_default_database_seeder_does_not_create_a_predictable_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_web_login_is_rate_limited_by_normalized_email_and_ip_address(): void
    {
        config(['rate_limits.web_login_per_minute' => 2]);

        $payload = [
            'email' => 'BLOCKED@example.test',
            'password' => 'invalid-password',
        ];

        $this->post(route('student.login.store'), $payload)->assertRedirect();
        $this->post(route('student.login.store'), $payload)->assertRedirect();
        $this->post(route('student.login.store'), $payload)->assertTooManyRequests();
    }

    private function assertRoleCanAndCannot(string $role, string $allowedPermission, string $deniedPermission): void
    {
        $roleModel = Role::findByName($role, 'web');

        $this->assertTrue($roleModel->hasPermissionTo($allowedPermission));
        $this->assertFalse($roleModel->hasPermissionTo($deniedPermission));
    }
}
