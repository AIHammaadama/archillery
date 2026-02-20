@component('mail::message')
# New Purchase Request Submitted

A new purchase request has been submitted and requires your attention.

@component('mail::panel')
**Request Number:** {{ $request->request_number }}  
**Project:** {{ $request->project->name }}  
**Requested By:** {{ $request->requestedBy->firstname }} {{ $request->requestedBy->lastname }}  
**Date:** {{ $request->request_date->format('M d, Y') }}
@if($request->required_by_date)  
**Required By:** {{ $request->required_by_date->format('M d, Y') }}
@endif
@endcomponent

**Justification:**  
{{ $request->justification }}

**Items Requested:** {{ $request->items->count() }}

@component('mail::button', ['url' => route('requests.show', $request)])
View Request Details
@endcomponent

Please review and assign vendors to this request.

Best regards,  
{{ config('app.name') }}
@endcomponent
