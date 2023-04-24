<?php

namespace App\Jobs;

use App\Traits\Loggable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Loggable;

    protected string $name;
    protected Collection $row;
    protected string $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $name, Collection $row, string $export)
    {
        $this->name   = $name;
        $this->row    = $row;
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function handle()
    {
        ini_set("memory_limit", "2G");
        set_time_limit(86400);
        Excel::store(new $this->export($this->row), $this->name, 'public');
    }
}
