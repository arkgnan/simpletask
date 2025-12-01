<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Task::query();

        if ($request->filled("title")) {
            $query->where(
                "title",
                "like",
                "%" . $request->input("title") . "%",
            );
        }

        $tasks = $query->paginate(20);
        return view("pages.task.index", compact("tasks"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::all();
        $statuses = TaskStatus::cases();
        $priorities = TaskPriority::cases();

        return view(
            "pages.task.create",
            compact("users", "statuses", "priorities"),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return redirect()
            ->route("task.show", $task)
            ->with("success", "Task created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        $users = User::all();
        $statuses = TaskStatus::cases();
        $priorities = TaskPriority::cases();

        return view(
            "pages.task.show",
            compact("task", "users", "statuses", "priorities"),
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()
            ->route("task.show", $task)
            ->with("success", "Task updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route("task.index")
            ->with("success", "Task deleted successfully!");
    }
}
