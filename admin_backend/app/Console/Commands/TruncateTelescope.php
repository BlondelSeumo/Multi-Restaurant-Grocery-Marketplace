<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class TruncateTelescope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truncate:telescope';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email by template when send_to is now';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        DB::statement("SET foreign_key_checks = 0");

        try {
            DB::table('telescope_entries')->truncate();
        } catch (Throwable) {}

        try {
            DB::table('telescope_entries_tags')->truncate();
        } catch (Throwable) {}

        try {
            DB::table('telescope_monitoring')->truncate();
        } catch (Throwable) {}

        DB::statement("SET foreign_key_checks = 1");

        return 0;
    }
}
