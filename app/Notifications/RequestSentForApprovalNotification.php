<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use App\Mail\RequestSentForApprovalMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestSentForApprovalNotification extends Notification implements ShouldQueue
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
        return new RequestSentForApprovalMail($this->request);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_pending_approval',
            'title' => 'Request Awaiting Approval',
            'message' => "Request {$this->request->request_number} for {$this->request->project->name} requires your approval",
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'project_name' => $this->request->project->name,
            'url' => route('requests.show', $this->request),
        ];
    }
}
