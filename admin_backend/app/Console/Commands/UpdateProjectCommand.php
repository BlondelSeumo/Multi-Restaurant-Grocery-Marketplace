<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpdateProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update project files';

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
     */
    public function handle()
    {
        // 1. Unzip updated project
        $process = Process::fromShellCommandline("cd ". base_path() . " && unzip -o foodyman.zip");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        // \Log::info('unzip: ', [$process->getOutput()]);
        $this->info('1. Project was successfully updated');

       // 2. Update composer files
//      $composer = Process::fromShellCommandline("cd ". base_path() . "/ && composer update");
//        $composer->setInput('y');
//        $composer->run();
//        if (!$composer->isSuccessful()) {
//            throw new ProcessFailedException($composer);
//        }
//        echo $composer->getOutput();
//        $this->info('2. Composer updated');

        // 3. Migrate new migration and add new Seed
        $artisan = Process::fromShellCommandline("cd ". base_path() . " && php artisan migrate --seed");
        $artisan->setInput('y');

        $artisan->run();
        if (!$artisan->isSuccessful()) {
            throw new ProcessFailedException($artisan);
        }
        //echo $artisan->getOutput();
        $this->info('3. Artisan migration & seed success');

        // 4. Clear config and cache files
        $artisan =  Process::fromShellCommandline("cd ". base_path() . "/ && php artisan optimize:clear && php artisan config:cache && php artisan route:cache");

        $artisan->run();
        if (!$artisan->isSuccessful()) {
            throw new ProcessFailedException($artisan);
        }
        //echo $artisan->getOutput();
        $this->info('4. Artisan cache and config clear');
    }
}


