<?php

namespace App\Listeners;

use App\Events\PostChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPostNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostChanged $event): void
    {
        Log::info("Listener caught event: {$event->action} for Post ID {$event->post->id}");
    }
}
