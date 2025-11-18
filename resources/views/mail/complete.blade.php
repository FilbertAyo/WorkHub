
@extends('mail.app')

@section('content')

        <div class="email-header">
             <h2>Payment Completed</h2>
        </div>
        <div>
            <p>Hello,</p>
           <p>Your petty cash request for <strong>{{ $reason }}</strong> has been approved, and the amount has been disbursed successfully.</p>
            <p>To review the request details for your records, please click the button below:</p>
            <p>
                <a href="{{ config('app.url') }}/petty/{{ $id }}"
                    style="display: inline-block; padding: 10px 20px; background-color: #1d72b8; color: white; text-decoration: none; border-radius: 4px;">
                    Review
                </a>
            </p>
        </div>


@endsection
