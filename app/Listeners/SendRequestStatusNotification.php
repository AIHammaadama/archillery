<?php

namespace App\Listeners;

use App\Events\RequestStatusChanged;
use App\Models\User;
use App\Enums\RequestStatus;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RequestStatusNotification;

class SendRequestStatusNotification
{
    /**
     * Handle the event.
     */
    public function handle(RequestStatusChanged $event): void
    {
        $request = $event->request;
        $newStatus = $event->newStatus;
        $changedBy = $event->changedBy;

        // Determine who should be notified based on the new status
        $usersToNotify = $this->getUsersToNotify($request, $newStatus, $changedBy);

        // Send notifications to each user
        foreach ($usersToNotify as $user) {
            $user->notify(new RequestStatusNotification($request, $newStatus, $changedBy, $event->comments));
        }
    }

    /**
     * Get users who should be notified based on status
     */
    protected function getUsersToNotify($request, RequestStatus $newStatus, User $changedBy): array
    {
        $users = [];

        switch ($newStatus) {
            case RequestStatus::SUBMITTED:
                // Notify procurement officers assigned to the project
                $procurementOfficers = $request->project->procurementOfficers;
                foreach ($procurementOfficers as $officer) {
                    if ($officer->id !== $changedBy->id) {
                        $users[] = $officer;
                    }
                }
                break;

            case RequestStatus::PENDING_DIRECTOR:
                // Notify directors
                $directors = User::whereHas('role', function ($q) {
                    $q->where('name', 'director');
                })->get();
                foreach ($directors as $director) {
                    if ($director->id !== $changedBy->id) {
                        $users[] = $director;
                    }
                }
                break;

            case RequestStatus::APPROVED:
            case RequestStatus::REJECTED:
                // Notify the person who requested and procurement officer
                if ($request->requestedBy && $request->requestedBy->id !== $changedBy->id) {
                    $users[] = $request->requestedBy;
                }
                if ($request->procurementOfficer && $request->procurementOfficer->id !== $changedBy->id) {
                    $users[] = $request->procurementOfficer;
                }
                break;

            case RequestStatus::PROCUREMENT_PROCESSING:
                // Notify site manager (requester)
                if ($request->requestedBy && $request->requestedBy->id !== $changedBy->id) {
                    $users[] = $request->requestedBy;
                }
                break;

            case RequestStatus::PARTIALLY_DELIVERED:
            case RequestStatus::FULLY_DELIVERED:
                // Notify procurement officer, director, and requester
                if ($request->procurementOfficer && $request->procurementOfficer->id !== $changedBy->id) {
                    $users[] = $request->procurementOfficer;
                }
                if ($request->requestedBy && $request->requestedBy->id !== $changedBy->id) {
                    $users[] = $request->requestedBy;
                }
                $directors = User::whereHas('role', function ($q) {
                    $q->where('name', 'director');
                })->get();
                foreach ($directors as $director) {
                    if ($director->id !== $changedBy->id) {
                        $users[] = $director;
                    }
                }
                break;
        }

        return array_unique($users);
    }
}
