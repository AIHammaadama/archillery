@component('mail::message')
# Delivery Recorded

A new delivery has been recorded for your project.

@component('mail::panel')
**Request Number:** {{ $delivery->request->request_number }}  
**Project:** {{ $delivery->request->project->name }}  
**Material:** {{ $delivery->requestItem->material->name }}  
**Quantity Delivered:** {{ number_format($delivery->quantity_delivered, 2) }} {{ $delivery->requestItem->material->unit_of_measurement }}  
**Delivery Date:** {{ $delivery->delivery_date->format('M d, Y') }}
@endcomponent

@if($delivery->waybill_number)
**Waybill Number:** {{ $delivery->waybill_number }}
@endif

@if($delivery->invoice_number)
**Invoice Number:** {{ $delivery->invoice_number }}
@endif

@if($delivery->quality_notes)
**Quality Notes:**  
{{ $delivery->quality_notes }}
@endif

@component('mail::button', ['url' => route('deliveries.show', $delivery)])
View Delivery Details
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
