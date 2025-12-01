<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteExpiredExports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "exports:delete-expired";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Deletes Excel files from storage that are older than 24 hours";

    /**
     * Execute the console command.
     * @return void
     */
    public function handle(): void
    {
        $files = Storage::disk("public")->files("exports");

        $now = now();

        foreach ($files as $file) {
            $filePath = "public/$file";
            $lastModified = Storage::disk("public")->lastModified($file);
            $fileAge = $now->diffInHours($lastModified);

            if ($fileAge > 24) {
                Storage::disk("public")->delete($file);
                $this->info("Deleted expired export: $file");
            }
        }

        $this->info("Expired exports cleanup completed.");
    }
}
