<?php

namespace App\Notifications;

use App\Models\Delivery;
use App\Mail\DeliveryRecordedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DeliveryReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Delivery $delivery;

    /**
     * Create a new notification instance.
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return new DeliveryRecordedMail($this->delivery);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $material = $this->delivery->requestItem->material->name;
        $requestNumber = $this->delivery->request->request_number;
        $quantity = number_format($this->delivery->quantity_delivered, 2);
        $unit = $this->delivery->requestItem->material->unit_of_measurement;

        return [
            'type' => 'delivery_received',
            'title' => 'Delivery Received',
            'message' => "Delivery received for {$requestNumber}: {$quantity} {$unit} of {$material}",
            'delivery_id' => $this->delivery->id,
            'delivery_number' => $this->delivery->delivery_number,
            'request_id' => $this->delivery->request_id,
            'request_number' => $requestNumber,
            'material_name' => $material,
            'quantity' => $quantity,
            'unit' => $unit,
            'url' => route('deliveries.show', $this->delivery),
        ];
    }
}
