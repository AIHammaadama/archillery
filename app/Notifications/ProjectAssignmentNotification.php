<?php

namespace App\Notifications;

use App\Models\Project;
use App\Mail\ProjectAssignmentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjectAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Project $project;
    protected string $roleType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, string $roleType)
    {
        $this->project = $project;
        $this->roleType = $roleType;
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
        return new ProjectAssignmentMail($this->project, $this->roleType);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $role = ucwords(str_replace('_', ' ', $this->roleType));

        return [
            'type' => 'project_assigned',
            'title' => 'New Project Assignment',
            'message' => "You have been assigned as {$role} for project: {$this->project->name} ({$this->project->code})",
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'project_code' => $this->project->code,
            'role_type' => $this->roleType,
            'url' => route('projects.show', $this->project),
        ];
    }
}
