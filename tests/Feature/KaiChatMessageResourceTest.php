<?php

namespace Tests\Feature;

use App\Filament\Resources\KaiChatMessages\Pages\ListKaiChatMessages;
use App\Models\KaiChatMessage;
use App\Models\KaiChatSession;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KaiChatMessageResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_chat_messages_table_context_keys(): void
    {
        Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.test',
        ]);
        $admin->assignRole('super_admin');

        $student = User::factory()->create([
            'email' => 'student@example.test',
        ]);
        $session = KaiChatSession::create([
            'user_id' => $student->id,
            'title' => 'Show my week',
            'driver' => 'local',
            'last_message_at' => now(),
        ]);

        KaiChatMessage::create([
            'kai_chat_session_id' => $session->id,
            'user_id' => $student->id,
            'role' => 'assistant',
            'content' => 'Focus on attendance and upcoming coursework.',
            'context_keys' => ['student_profile', 'attendance', 'latest_results'],
            'driver' => 'local',
            'status' => 'completed',
        ]);

        Livewire::actingAs($admin)
            ->test(ListKaiChatMessages::class)
            ->assertOk()
            ->assertSee('student_profile, attendance, latest_results');
    }
}
