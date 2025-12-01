<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class FilterTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => ["nullable", "string"],
            "assignee" => ["nullable", "string"],
            "status" => ["nullable", "string"],
            "priority" => ["nullable", "string"],
            "due_date" => ["nullable", "array"],
            "due_date.start" => ["nullable", "date"],
            "due_date.end" => [
                "nullable",
                "date",
                "after_or_equal:due_date.start",
            ],
        ];
    }

    /**
     * Get status values as array from comma-separated string
     */
    public function getStatusValues(): array
    {
        if (!$this->has("status") || empty($this->status)) {
            return [];
        }

        $statuses = array_map("trim", explode(",", $this->status));
        $validStatusValues = TaskStatus::values();

        return array_filter($statuses, function ($status) use (
            $validStatusValues,
        ) {
            return in_array($status, $validStatusValues);
        });
    }

    /**
     * Get priority values as array from comma-separated string
     */
    public function getPriorityValues(): array
    {
        if (!$this->has("priority") || empty($this->priority)) {
            return [];
        }

        $priorities = array_map("trim", explode(",", $this->priority));
        $validPriorityValues = TaskPriority::values();

        return array_filter($priorities, function ($priority) use (
            $validPriorityValues,
        ) {
            return in_array($priority, $validPriorityValues);
        });
    }

    /**
     * Get assignee values as array from comma-separated string
     */
    public function getAssigneeValues(): array
    {
        if (!$this->has("assignee") || empty($this->assignee)) {
            return [];
        }

        return array_map("trim", explode(",", $this->assignee));
    }

    /**
     * Get due date start value or null
     *
     * @return string|null
     */
    public function getDueDateStart(): ?string
    {
        if ($this->filled("due_date.start")) {
            return $this->input("due_date.start");
        }

        if ($this->has("due_date") && isset($this->due_date["start"])) {
            return $this->due_date["start"];
        }

        return null;
    }

    /**
     * Get due date end value or null
     *
     * @return string|null
     */
    public function getDueDateEnd(): ?string
    {
        if ($this->filled("due_date.end")) {
            return $this->input("due_date.end");
        }

        if ($this->has("due_date") && isset($this->due_date["end"])) {
            return $this->due_date["end"];
        }

        return null;
    }
}
