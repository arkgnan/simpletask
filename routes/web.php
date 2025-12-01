<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect("/dashboard");
})->name("home");

Route::get("/dashboard", [DashboardController::class, "index"])
    ->middleware(["auth"])
    ->name("dashboard");

Route::resource("task", TaskController::class);

require __DIR__ . "/auth.php";
