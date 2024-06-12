<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    /**
     * Initializes some things needed for the testing purpose
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create();
        
        // Create tasks
        Task::factory()->count(3)->create();

        // Generate a token for the user
        $this->token = JWTAuth::fromUser($this->user);
    }

    /**
     * @test
     * Listing tasks
     */
    public function it_can_list_tasks()
    {
        $response = $this->getJson('/api/tasks', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'tasks');
    }

    /**
     * @test
     * Creating tasks
     */
    public function it_can_create_a_task()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => '2024-12-31',
        ];

        $response = $this->postJson('/api/tasks', $taskData, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment($taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /**
     * @test
     * Showing task
     */
    public function it_can_show_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->getJson('/api/tasks/' . $task->id, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                    'status' => 200,
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'due_date' => $task->due_date,
                        'created_at' => $task->created_at,
                        'updated_at' => $task->updated_at
                    ]
                ]);
    }

    /**
     * @test
     * Updating task
     */
    public function it_can_update_a_task()
    {
        $task = Task::factory()->create();

        $updatedData = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'due_date' => '2024-12-31',
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $updatedData, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment($updatedData);

        $this->assertDatabaseHas('tasks', $updatedData);
    }

    /**
     * @test
     * Deleting task
     */
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson('/api/tasks/' . $task->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * @test
     * Filtering task by status
     */
    public function it_can_filter_tasks_by_status()
    {
        $pendingTask = Task::factory()->create(['status' => 'pending']);
        $inProgressTask = Task::factory()->create(['status' => 'in_progress']);

        $response = $this->getJson('/api/tasks/filter/status/pending', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'pending'])
                 ->assertJsonMissing(['status' => 'in_progress']);
    }

    /**
     * @test
     * Filtering task by due date
     */
    public function it_can_filter_tasks_by_due_date()
    {
        $taskDueToday = Task::factory()->create(['due_date' => now()->toDateString()]);
        $taskDueTomorrow = Task::factory()->create(['due_date' => now()->addDay()->toDateString()]);

        $response = $this->getJson('/api/tasks/filter/due-date/' . now()->toDateString(), [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['due_date' => now()->toDateString()])
                 ->assertJsonMissing(['due_date' => now()->addDay()->toDateString()]);
    }
}
