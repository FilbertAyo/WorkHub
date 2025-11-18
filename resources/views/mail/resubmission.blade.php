@extends('mail.app')

@section('content')
    <div class="email-header">
        <h2>Resubmission from {{ $name }}!</h2>
    </div>
    <div>
        <p>Hello,</p>
        <p>The rejected petty cash for <strong>{{ $reason }}</strong> was already submitted please check.</p>
        <p>
            <a href="{{ config('app.url') }}/pettycash/request/{{ $id }}/details"
                style="display: inline-block; padding: 10px 20px; background-color: #1d72b8; color: white; text-decoration: none; border-radius: 4px;">
                Details
            </a>
        </p>
    </div>
@endsection
