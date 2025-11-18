@extends('mail.app')

@section('content')
    <div class="email-header">
        <h2>Petty Cash Rejected!</h2>
    </div>
    <div>
        <p>Hello {{ $name }}, </p>
        <p>We regret to inform you that your petty cash request for <strong>{{ $reason }}</strong> has been rejected.</p>

        <p>To review the request details and understand the reason for rejection, please click the button below:</p>
        <p>
            <a href="{{ config('app.url') }}/petty/{{ $id }}"
                style="display: inline-block; padding: 10px 20px; background-color: #1d72b8; color: white; text-decoration: none; border-radius: 4px;">
                Review
            </a>
        </p>
    </div>
@endsection
