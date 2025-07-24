<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure storage disk is set for testing file uploads
        Storage::fake('local');
    }

    /** @test */
    public function it_can_list_tasks_for_admin_user()
    {
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole);

        Task::factory()->count(5)->create();

        $this->actingAs($admin);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) =>
            $page->component('Tasks/Index')
                ->has('tasks.data', 5)
                ->where('isAdmin', true)
        );
    }

    /** @test */
    public function it_can_list_tasks_for_regular_user()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create(['user_id' => $user->id]);
        Task::factory()->count(2)->create(); // Other user's tasks

        $this->actingAs($user);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) =>
            $page->component('Tasks/Index')
                ->has('tasks.data', 3)
                ->where('isAdmin', false)
        );
    }

    /** @test */
    public function it_can_filter_tasks_by_search_query()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Task::factory()->create(['user_id' => $user->id, 'title' => 'Task A']);
        Task::factory()->create(['user_id' => $user->id, 'title' => 'Task B']);
        Task::factory()->create(['user_id' => $user->id, 'title' => 'Another Task']);

        $response = $this->get(route('tasks.index', ['search' => 'Task A']));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) =>
            $page->component('Tasks/Index')
                ->has('tasks.data', 1)
                ->where('tasks.data.0.title', 'Task A')
        );
    }

    /** @test */
    public function it_displays_create_task_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('tasks.create'));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) =>
            $page->component('Tasks/Create')
        );
    }

    /** @test */
    public function it_can_store_a_new_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taskData = [
            'title' => 'New Task Title',
            'description' => 'New Task Description',
            'due_date' => Carbon::now()->startOfDay()->toDateTimeString(),
            'is_completed' => false,
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'New Task Title',
            'description' => 'New Task Description',
            'due_date' => Carbon::now()->startOfDay()->toDateTimeString(),
            'is_completed' => false,
        ]);
    }

    /** @test */
    public function it_validates_task_store_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('tasks.store'), [
            'title' => '', // Invalid
            'description' => 'Some description',
            'due_date' => 'invalid-date',
        ]);

        $response->assertSessionHasErrors(['title', 'due_date']);
    }

    /** @test */
    public function it_displays_edit_task_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('tasks.edit', $task));

        $response->assertStatus(200);

        $response->assertInertia(fn ($page) =>
            $page->component('Tasks/Edit')
                ->where('task.id', $task->id)
        );
    }

    /** @test */
    public function it_can_update_an_existing_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'due_date' => Carbon::now()->startOfDay()->toDateTimeString(),
            'is_completed' => true,
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'due_date' => Carbon::now()->startOfDay()->toDateTimeString(),
            'is_completed' => 1,
        ]);
    }

    /** @test */
    public function it_validates_task_update_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->put(route('tasks.update', $task), [
            'title' => '', // Invalid
            'due_date' => 'invalid-date',
        ]);

        $response->assertSessionHasErrors(['title', 'due_date']);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}