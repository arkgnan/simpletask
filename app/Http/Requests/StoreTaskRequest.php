<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
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
        $rules = [
            "title" => ["required", "string", "max:255"],
            "assignee_id" => ["nullable", "exists:users,id"],
            "assignee" => [
                "nullable",
                "string",
                "max:255",
                "exists:users,name",
            ],
            "due_date" => ["required", "date"],
            "status" => ["required", new Enum(TaskStatus::class)],
            "priority" => ["required", new Enum(TaskPriority::class)],
            "description" => ["nullable", "string", "min:5"],
        ];

        // Apply 'after_or_equal:today' only for POST requests (new task creation)
        if ($this->isMethod("post")) {
            $rules["due_date"][] = "after_or_equal:today";
        }
        return $rules;
    }

    /**
     * Handle assignee name to id conversion
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // If assignee name is provided but assignee_id is not, convert name to ID
        if (
            isset($validated["assignee"]) &&
            !isset($validated["assignee_id"])
        ) {
            $user = User::where("name", $validated["assignee"])->first();
            if ($user) {
                $validated["assignee_id"] = $user->id;
            }
            unset($validated["assignee"]);
        }

        return $validated;
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "due_date.after_or_equal" => "The due date cannot be in the past.",
            "assignee.exists" => "The specified assignee does not exist.",
            "assignee_id.exists" => "The specified assignee does not exist.",
        ];
    }
}
