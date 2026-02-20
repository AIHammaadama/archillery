<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use App\Mail\RequestSentBackMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestSentBackNotification extends Notification implements ShouldQueue
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
        return new RequestSentBackMail($this->request, $this->reason);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_sent_back',
            'title' => 'Request Sent Back',
            'message' => "Request {$this->request->request_number} for {$this->request->project->name} has been sent back for revision",
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'project_name' => $this->request->project->name,
            'reason' => $this->reason,
            'url' => route('requests.show', $this->request),
        ];
    }
}
