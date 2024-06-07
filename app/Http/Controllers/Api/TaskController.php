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

    /**
     * Show a task by Id
     * 
     * @param Int $id Task Id
     * @return json Response information
     */
    public function show($id): JsonResponse
    {
        # Validates the $id parameter
        if(!is_numeric($id)) {
            $data = [
                'message' => 'Error validando los datos',
                'error' => 'El Id debe ser numérico',
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        # Gets the task
        $task = Task::find($id);

        # If the task was not found
        if (!$task) {
            $data = [
                'message' => "Tarea con id:{$id} no encontrado",
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        # If the task was found
        $data = [
            'task' => $task,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Update a task by Id
     * @param Request $request Data sent to be recorded
     * @param Int $id Task Id
     * @return json Response information
     */
    public function update(Request $request, $id)
    {
        # Validates the $id parameter
        if(!is_numeric($id)) {
            $data = [
                'message' => 'Error validando los datos',
                'error' => 'El Id debe ser numérico',
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        # Gets the task
        $task = Task::find($id);

        # If the task was not found
        if (!$task) {
            $data = [
                'message' => "Tarea con id:{$id} no encontrado",
                'status' => 404
            ];

            return response()->json($data, 404);
        }

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

        # If the task was found and the validation was successfull
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->due_date = $request->due_date;

        # Updates the data
        $task->save();

        $data = [
            'message' => "Tarea con id:{$id} actualizada exitosamente",
            'task' => $task,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Deletes the task from the database
     * 
     * @param Int $id Task Id
     * @return json Response Information
     */
    public function destroy($id): JsonResponse
    {
        # Validates the $id parameter
        if (!is_numeric($id)) {
            $data = [
                'message' => 'Data Validation Error',
                'error' => 'El Id debe ser numérico',
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        # Gets the task
        $task = Task::find($id);

        # If the task was not found
        if (!$task) {
            $data = [
                'message' => "Tarea con id:{$id} no encontrado",
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        # If the task was found
        $task->delete();

        $data = [
            'task' => "Tarea con id:{$id} eliminada",
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
