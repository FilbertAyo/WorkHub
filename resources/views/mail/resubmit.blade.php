@extends('mail.app')

@section('content')
    <div class="email-header">
        <h2>Review In Progress!</h2>
    </div>
    <div>
        <p>Hello {{ $name }},</p>
        <p>Your petty cash request for <strong>{{ $reason }}</strong> is currently in the resubmission phase.</p>
        <p>Please review and update your request as recommended within 24 hours. After that, resubmission will no longer be
            available.</p>
        <p>
            <a href="{{ config('app.url') }}/petty/{{ $id }}"
                style="display: inline-block; padding: 10px 20px; background-color: #1d72b8; color: white; text-decoration: none; border-radius: 4px;">
                Update Request
            </a>
        </p>
    </div>
@endsection
