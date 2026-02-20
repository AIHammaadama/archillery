@component('mail::message')
# Request Rejected

Your purchase request has been rejected.

@component('mail::panel')
**Request Number:** {{ $request->request_number }}  
**Project:** {{ $request->project->name }}  
**Status:** Rejected
@endcomponent

**Reason for Rejection:**  
{{ $reason }}

@component('mail::button', ['url' => route('requests.show', $request)])
View Request Details
@endcomponent

If you have questions about this decision, please contact the approving authority.

Best regards,  
{{ config('app.name') }}
@endcomponent
