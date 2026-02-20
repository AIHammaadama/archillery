<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use App\Mail\RequestSubmittedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProcurementRequest $request
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable)
    {
        return new RequestSubmittedMail($this->request);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_submitted',
            'title' => 'New Request Submitted',
            'message' => "Request {$this->request->request_number} has been submitted for {$this->request->project->name}",
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'project_name' => $this->request->project->name,
            'url' => route('requests.show', $this->request),
        ];
    }
}
