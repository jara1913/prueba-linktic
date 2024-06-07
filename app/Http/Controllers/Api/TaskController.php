<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * This class takes control over tasks endpoints
 */
class TaskController extends Controller
{
    /**
     * Lists the tasks
     * 
     * @param String $status Filters tasks as active or completed
     * @return json Response information
     */
    public function index(): JsonResponse
    {
        # Gets the tasks from database
        $tasks = Task::all();

        # If there aren't tasks
        if ($tasks->isEmpty()) {
            $data = [
                'message' => 'No se encontraron tareas',
                'status' => 200
            ];

            return response()->json($data, 200);
        }

        # If there are tasks
        $data = [
            'tasks' => $tasks,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Creates the task in the database
     * 
     * @param Request $request Data sent to be recorded
     * @return json Response information
     */
    public function store(Request $request): JsonResponse
    {
        # Validates de incoming data
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:155',
            'description' => 'required|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error validando los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        # Creates the task
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date
        ]);

        # If task creation fails
        if (!$task) {
            $data = [
                'message' => 'Error creando la tarea',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        # If task creation was successful
        $data = [
            'task' => $task,
            'status' => 201
        ];

        return response()->json($data, 201);
    }
}
