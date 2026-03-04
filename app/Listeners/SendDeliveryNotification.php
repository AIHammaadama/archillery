<?php

namespace App\Listeners;

use App\Events\DeliveryReceived;
use App\Models\User;
use App\Notifications\DeliveryReceivedNotification;

class SendDeliveryNotification
{
    /**
     * Handle the event.
     */
    public function handle(DeliveryReceived $event): void
    {
        $delivery = $event->delivery;
        $request = $delivery->request;

        // Notify procurement officer
        if ($request->procurementOfficer) {
            app()->terminating(function () use ($request, $delivery) {
                $request->procurementOfficer->notify(
                    new DeliveryReceivedNotification($delivery)
                );
            });
        }

        // Notify directors
        $directors = User::whereHas('role', function ($q) {
            $q->where('name', 'director');
        })->get();

        foreach ($directors as $director) {
            app()->terminating(function () use ($director, $delivery) {
                $director->notify(
                    new DeliveryReceivedNotification($delivery)
                );
            });
        }
    }
}
