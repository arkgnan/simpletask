<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        "title",
        "assignee_id",
        "due_date",
        "status",
        "priority",
        "description",
    ];

    protected $casts = [
        "due_date" => "date",
        "status" => TaskStatus::class,
        "priority" => TaskPriority::class,
    ];

    protected $attributes = [
        "status" => TaskStatus::PENDING,
        "priority" => TaskPriority::LOW,
    ];

    protected $with = ["assignee"];

    /**
     * Get the user that is assigned to this todo.
     * @return BelongsTo<User>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, "assignee_id");
    }
}
