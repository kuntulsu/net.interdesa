<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientDownNotification implements ShouldQueue
{
    use Queueable;
    protected string $user;
    /**
     * Create a new job instance.
     */
    public function __construct(string $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if reconnect was detected
        if (!Cache::has("ppp:pending_down:{$this->user}")) {
            return; // Reconnected, cancel alert
        }

        $message = "❌ *PPP User*: `{$this->user}`\n*Event*: `down`\nTime: `" . now() . "`";
        
        Log::error($message);
        // Remove cache after notification
        Cache::forget("ppp:pending_down:{$this->user}");
    }
}
