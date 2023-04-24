<?php

namespace App\Console\Commands;

use App\Models\ShopClosedDate;
use App\Models\Story;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveExpiredStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:expired:stories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove expired stories';

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
//        $stories = Story::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime('-1 day')))
//            ->orWhere('created_at', '<=', date('Y-m-d 23:59:59', strtotime('-1 day')))
//            ->get();
//
//        foreach ($stories as $story) {
//            try {
//                $story->delete();
//            } catch (Throwable $e) {
//                Log::error($e->getMessage(), [
//                    'code'    => $e->getCode(),
//                    'message' => $e->getMessage(),
//                    'trace'   => $e->getTrace(),
//                    'file'    => $e->getFile(),
//                ]);
//            }
//        }

        return 0;
    }
}
