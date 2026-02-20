@component('mail::message')
# New Project Assignment

Hello,

You have been assigned as **{{ ucwords(str_replace('_', ' ', $roleType)) }}** for the following project:

@component('mail::panel')
**Project:** {{ $project->name }}  
**Code:** {{ $project->code }}  
**Status:** {{ ucfirst($project->status) }}
@if($project->start_date)  
**Start Date:** {{ $project->start_date->format('M d, Y') }}
@endif
@endcomponent

@if($project->description)
**Description:**  
{{ $project->description }}
@endif

@component('mail::button', ['url' => route('projects.show', $project)])
View Project Details
@endcomponent

Thank you for your service!

Best regards,  
{{ config('app.name') }}
@endcomponent
