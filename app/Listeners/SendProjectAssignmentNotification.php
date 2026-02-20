<?php

namespace App\Listeners;

use App\Events\ProjectAssigned;
use App\Notifications\ProjectAssignmentNotification;

class SendProjectAssignmentNotification
{
    /**
     * Handle the event.
     */
    public function handle(ProjectAssigned $event): void
    {
        $event->user->notify(
            new ProjectAssignmentNotification($event->project, $event->roleType)
        );
    }
}
