<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("tasks", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table
                ->foreignId("assignee_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $table->date("due_date");
            $table->string("status")->default(TaskStatus::PENDING->value);
            $table->string("priority")->default(TaskPriority::LOW->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tasks");
    }
};
