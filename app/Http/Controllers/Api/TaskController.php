<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Exports\TasksExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChartDataRequest;
use App\Http\Requests\FilterTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::with("assignee")->latest()->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Check if assignee exists and convert name to ID
        if ($request->has("assignee") && !empty($request->assignee)) {
            $user = User::where("name", $request->assignee)->first();

            if ($user) {
                $validated["assignee_id"] = $user->id;
            }

            // Remove assignee from validated data as we now use assignee_id
            unset($validated["assignee"]);
        }

        // Create the task with validated data
        $task = Task::create($validated);

        // Load the assignee relationship
        $task->load("assignee");

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load("assignee");

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, Task $task): JsonResponse
    {
        $validated = $request->validated();

        // Check if assignee exists and convert name to ID
        if ($request->has("assignee") && !empty($request->assignee)) {
            $user = User::where("name", $request->assignee)->first();

            if ($user) {
                $validated["assignee_id"] = $user->id;
            }

            // Remove assignee from validated data as we now use assignee_id
            unset($validated["assignee"]);
        }

        $task->update($validated);

        // Load the assignee relationship
        $task->load("assignee");

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(["message" => "Task deleted successfully"]);
    }

    /**
     * Generate an Excel report of tasks with optional filtering.
     * Returns a URL for downloading the file instead of direct download.
     */
    public function exportExcel(FilterTaskRequest $request): JsonResponse
    {
        // Build the query with filters
        $query = Task::query()->with("assignee");

        // Apply title filter (partial match)
        if ($request->filled("title")) {
            $query->where("title", "like", "%" . $request->title . "%");
        }

        // Apply assignee filter
        $assignees = $request->getAssigneeValues();
        if (!empty($assignees)) {
            $query->whereHas("assignee", function ($q) use ($assignees) {
                $q->whereIn("name", $assignees);
            });
        }

        // Apply status filter
        $statuses = $request->getStatusValues();
        if (!empty($statuses)) {
            $query->whereIn("status", $statuses);
        }

        // Apply priority filter
        $priorities = $request->getPriorityValues();
        if (!empty($priorities)) {
            $query->whereIn("priority", $priorities);
        }

        // Apply due date range filters
        $dueDateStart = $request->getDueDateStart();
        if ($dueDateStart) {
            $query->where("due_date", ">=", $dueDateStart);
        }

        $dueDateEnd = $request->getDueDateEnd();
        if ($dueDateEnd) {
            $query->where("due_date", "<=", $dueDateEnd);
        }

        // Generate a unique filename with timestamp and random string
        $filename =
            "tasks_export_" .
            now()->format("Ymd_His") .
            "_" .
            Str::random(8) .
            ".xlsx";
        $relativePath = "exports/" . $filename;

        // Make sure the export directory exists
        Storage::makeDirectory("public/exports");

        // Store the Excel file and return a URL
        Excel::store(new TasksExport($query), $relativePath, "public");

        // Generate public download URL
        $url = url(Storage::url($relativePath));

        // Return a JSON response with the download URL
        return response()->json([
            "message" => "Excel file generated successfully",
            "file_name" => $filename,
            "download_url" => $url,
            "expires_at" => now()->addDay()->toDateTimeString(), // URL expires in 24 hours
            "task_count" => $query->count(),
        ]);
    }

    /**
     * Generate chart data.
     */
    public function chartData(ChartDataRequest $request): JsonResponse
    {
        $type = $request->type;

        switch ($type) {
            case "status":
                return $this->getStatusSummary();
            case "priority":
                return $this->getPrioritySummary();
            case "assignee":
                return $this->getAssigneeSummary();
            default:
                return response()->json(["error" => "Invalid chart type"], 400);
        }
    }

    /**
     * Get status summary data for charts.
     */
    private function getStatusSummary(): JsonResponse
    {
        $statusCounts = Task::query()
            ->select("status", DB::raw("count(*) as count"))
            ->groupBy("status")
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            })
            ->toArray();

        // Ensure all statuses are represented, even with zero counts
        $statusSummary = [];
        foreach (TaskStatus::cases() as $status) {
            $statusSummary[$status->value] = $statusCounts[$status->value] ?? 0;
        }

        return response()->json([
            "status_summary" => $statusSummary,
        ]);
    }

    /**
     * Get priority summary data for charts.
     */
    private function getPrioritySummary(): JsonResponse
    {
        // dd(TaskPriority::cases());
        $priorityCounts = Task::query()
            ->select("priority", DB::raw("count(*) as count"))
            ->groupBy("priority")
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->priority->value => $item->count];
            })
            ->toArray();

        // Ensure all priorities are represented, even with zero counts
        $prioritySummary = [];
        foreach (TaskPriority::cases() as $priority) {
            $prioritySummary[$priority->value] =
                $priorityCounts[$priority->value] ?? 0;
        }

        return response()->json([
            "priority_summary" => $prioritySummary,
        ]);
    }

    /**
     * Get assignee summary data for charts.
     */
    private function getAssigneeSummary(): JsonResponse
    {
        $assigneeSummary = [];

        $users = User::has("tasks")->get();

        foreach ($users as $user) {
            $tasks = $user->tasks;
            $pendingTasks = $tasks->where("status", TaskStatus::PENDING);
            $completedTasks = $tasks->where("status", TaskStatus::COMPLETED);

            $assigneeSummary[$user->name] = [
                "total_tasks" => $tasks->count(),
                "total_pending_tasks" => $pendingTasks->count(),
            ];
        }

        return response()->json([
            "assignee_summary" => $assigneeSummary,
        ]);
    }
}
