<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
