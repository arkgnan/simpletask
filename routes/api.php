<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::prefix("auth")->group(function () {
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);

    // Protected auth routes
    Route::middleware("auth:api")->group(function () {
        Route::post("/logout", [AuthController::class, "logout"]);
        Route::get("/me", [AuthController::class, "me"]);
    });
});

// Task routes (all protected)
Route::middleware("auth:api")
    ->prefix("task")
    ->group(function () {
        // List and create tasks
        Route::get("/", [TaskController::class, "index"]);
        Route::post("/", [TaskController::class, "store"]);

        Route::get("/export", [TaskController::class, "exportExcel"]);
        Route::get("/chart", [TaskController::class, "chartData"]);

        Route::get("/{task}", [TaskController::class, "show"]);
        Route::put("/{task}", [TaskController::class, "update"]);
        Route::delete("/{task}", [TaskController::class, "destroy"]);
    });

Route::middleware(["web"])->group(function () {
    // This api can hit from dashboard only
    Route::post("/weather/update", WeatherController::class);
});
