<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use App\Mail\RequestRejectedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProcurementRequest $request,
        public string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable)
    {
        return new RequestRejectedMail($this->request, $this->reason);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_rejected',
            'title' => 'Request Rejected',
            'message' => "Request {$this->request->request_number} for {$this->request->project->name} has been rejected",
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'project_name' => $this->request->project->name,
            'reason' => $this->reason,
            'url' => route('requests.show', $this->request),
        ];
    }
}
