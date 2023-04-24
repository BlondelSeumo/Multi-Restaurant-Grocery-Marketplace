<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketObserver
{
    /**
     * Handle the Category "creating" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function creating(Ticket $ticket)
    {
        $ticket->uuid = Str::uuid();
    }

    /**
     * Handle the Ticket "created" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "updated" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "deleted" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }
}
