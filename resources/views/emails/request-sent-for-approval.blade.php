@component('mail::message')
# Request Awaiting Your Approval

A purchase request has been sent for your approval.

@component('mail::panel')
**Request Number:** {{ $request->request_number }}  
**Project:** {{ $request->project->name }}  
**Requested By:** {{ $request->requestedBy->firstname }} {{ $request->requestedBy->lastname }}  
**Total Amount:** ₦{{ number_format($request->total_quoted_amount ?? $request->total_estimated_amount, 2) }}
@endcomponent

**Justification:**  
{{ $request->justification }}

**Items:** {{ $request->items->count() }}

@component('mail::button', ['url' => route('requests.show', $request)])
Review & Approve Request
@endcomponent

Please review the vendor assignments and pricing before approving.

Best regards,  
{{ config('app.name') }}
@endcomponent
