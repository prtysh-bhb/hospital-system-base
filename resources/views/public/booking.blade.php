@extends('layouts.public')

@section('title', 'Book Appointment')
@section('body-class', 'bg-gray-50 min-h-screen')

@section('content')
@php
    $step = request()->get('step', 1);
    $step = max(1, min(4, (int)$step)); // Ensure step is between 1 and 4
@endphp

@if($step == 1)
    @include('public.partials.booking-step1')
@elseif($step == 2)
    @include('public.partials.booking-step2')
@elseif($step == 3)
    @include('public.partials.booking-step3')
@else
    @include('public.partials.booking-step4')
@endif
@endsection
