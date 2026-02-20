<?php

namespace App\Jobs;

use App\Events\MessageEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public function __construct($event, $data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param Pusher $pusher
     * @return void
     */

    public function handle()
    {
        // Pusher Configuration
        // $pusher = new Pusher(
        //     env('PUSHER_APP_KEY'), 
        //     env('PUSHER_APP_SECRET'), 
        //     env('PUSHER_APP_ID'), 
        //     [
        //         'cluster' => env('PUSHER_APP_CLUSTER'),
        //         'useTLS' => false,
        //         'encrypted' => true,
        //     ]
        // );

        // $pusher->trigger('users-chat', $this->event, $this->data);
    }
}
