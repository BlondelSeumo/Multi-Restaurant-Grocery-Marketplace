<?php

namespace App\Console\Commands;

use App\Models\ShopClosedDate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveExpiredShopClosedDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:expired:closed:dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove expired closed dates';

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
        $dates = ShopClosedDate::where('date', '<=', date('Y-m-d', strtotime('-1 day')))->get();

        foreach ($dates as $value) {
            try {
                $value->delete();
            } catch (Throwable $e) {
                Log::error($e->getMessage(), [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTrace(),
                    'file'    => $e->getFile(),
                ]);
            }
        }

        return 0;
    }
}
