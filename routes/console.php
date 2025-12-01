<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command("exports:delete-expired")
    ->everyMinute()
    ->timezone("Asia/Jakarta")
    ->onOneServer()
    ->onSuccess(function () {
        $this->info("Deleted expired exports successfully.");
    })
    ->onFailure(function () {
        $this->error("Deleted expired exports failed.");
    });
