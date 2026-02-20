@component('mail::message')
# Request Approved ✓

Good news! Your purchase request has been approved.

@component('mail::panel')
**Request Number:** {{ $request->request_number }}  
**Project:** {{ $request->project->name }}  
**Approved Amount:** ₦{{ number_format($request->total_quoted_amount ?? $request->total_estimated_amount, 2) }}  
**Status:** Approved
@endcomponent

@if($comments)
**Approval Comments:**  
{{ $comments }}
@endif

You can now proceed with procurement.

@component('mail::button', ['url' => route('requests.show', $request)])
View Request Details
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
