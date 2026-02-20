<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Enums\RequestStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ProcurementRequest $request;
    protected RequestStatus $status;
    protected User $changedBy;
    protected ?string $comments;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        ProcurementRequest $request,
        RequestStatus $status,
        User $changedBy,
        ?string $comments = null
    ) {
        $this->request = $request;
        $this->status = $status;
        $this->changedBy = $changedBy;
        $this->comments = $comments;
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
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('requests.show', $this->request);
        
        return (new MailMessage)
            ->subject($this->getTitle())
            ->greeting('Hello ' . $notifiable->firstname . ',')
            ->line($this->getMessage())
            ->when($this->comments, function (MailMessage $mail) {
                return $mail->line('Comments: "' . $this->comments . '"');
            })
            ->action('View Request', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $title = $this->getTitle();
        $message = $this->getMessage();

        return [
            'type' => 'request_status_changed',
            'title' => $title,
            'message' => $message,
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'changed_by' => $this->changedBy->firstname . ' ' . $this->changedBy->lastname,
            'comments' => $this->comments,
            'url' => route('requests.show', $this->request),
        ];
    }

    /**
     * Get notification title based on status
     */
    protected function getTitle(): string
    {
        return match ($this->status) {
            RequestStatus::SUBMITTED => 'New Request Submitted',
            RequestStatus::PENDING_PROCUREMENT => 'Request Awaiting Vendor Assignment',
            RequestStatus::PROCUREMENT_PROCESSING => 'Request Being Processed',
            RequestStatus::PENDING_DIRECTOR => 'Request Awaiting Approval',
            RequestStatus::APPROVED => 'Request Approved',
            RequestStatus::REJECTED => 'Request Rejected',
            RequestStatus::PARTIALLY_DELIVERED => 'Partial Delivery Received',
            RequestStatus::FULLY_DELIVERED => 'Request Fully Delivered',
            default => 'Request Status Updated',
        };
    }

    /**
     * Get notification message
     */
    protected function getMessage(): string
    {
        $requestNumber = $this->request->request_number;
        $project = $this->request->project->name;

        return match ($this->status) {
            RequestStatus::SUBMITTED => "Request {$requestNumber} for {$project} has been submitted and requires vendor assignment.",
            RequestStatus::PENDING_DIRECTOR => "Request {$requestNumber} for {$project} requires your approval.",
            RequestStatus::APPROVED => "Your request {$requestNumber} for {$project} has been approved.",
            RequestStatus::REJECTED => "Request {$requestNumber} for {$project} has been rejected.",
            RequestStatus::PARTIALLY_DELIVERED => "Partial delivery received for request {$requestNumber} ({$project}).",
            RequestStatus::FULLY_DELIVERED => "Request {$requestNumber} for {$project} has been fully delivered.",
            default => "Status updated for request {$requestNumber} ({$project}).",
        };
    }
}
