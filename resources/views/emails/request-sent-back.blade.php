@component('mail::message')
# Request Sent Back for Revision

A purchase request has been sent back to you for revision.

@component('mail::panel')
**Request Number:** {{ $request->request_number }}  
**Project:** {{ $request->project->name }}  
**Status:** Sent Back for Revision
@endcomponent

**Revision Required:**  
{{ $reason }}

Please review the feedback, make necessary changes, and resubmit for approval.

@component('mail::button', ['url' => route('approvals.edit-assignment', $request)])
Edit Vendor Assignments
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
