
@extends('mail.app')

@section('content')
    <div class="email-header">
        <h2>Approved Petty Cash Request from {{ $name }}</h2>
    </div>
    <div>
        <p>Hello,</p>
        <p>The petty cash request for <strong>{{ $reason }}</strong> has been fully approved and is now ready for
            disbursement.</p>

        <p>Please proceed with providing the funds to {{ $name }}. For your reference, click the button below to
            view the request details:</p>
        <p>
            <a href="{{ config('app.url') }}/pettycash/request/{{ $id }}/details"
                style="display: inline-block; padding: 10px 20px; background-color: #1d72b8; color: white; text-decoration: none; border-radius: 4px;">
                View Request
            </a>
        </p>
    </div>
@endsection
