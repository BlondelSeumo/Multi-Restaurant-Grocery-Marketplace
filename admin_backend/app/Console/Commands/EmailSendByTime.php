<?php

namespace App\Console\Commands;

use App\Events\Mails\EmailSendByTemplate;
use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class EmailSendByTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send:by:time';

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
        $emailTemplates = EmailTemplate::where([
            ['send_to', date('Y-m-d H')],
            ['status', 0],
            ['type', EmailTemplate::TYPE_SUBSCRIBE]
        ])->get();

        foreach ($emailTemplates as $emailTemplate) {

            $emailTemplate->update(['status' => 1]);

            event((new EmailSendByTemplate($emailTemplate)));
        }

        return 0;
    }
}
