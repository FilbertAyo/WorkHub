@extends('mail.app')

@section('content')

        <div class="email-header">
             <h2>New Petty Cash from {{ $name }}</h2>
        </div>

        <div>
            <p>Hello,</p>
            <p>A new petty cash request has been submitted for: <strong>{{ $reason }}</strong>.</p>
            <p>To review the request and proceed with approval, please click the button below:</p>
            <p>
                <a href="{{ config('app.url') }}/pettycash/request/{{ $id }}/details"
                    style="display: inline-block; padding: 10px 20px; background-color: #1d72b8; color: white; text-decoration: none; border-radius: 4px;">
                    View Request
                </a>
            </p>
        </div>


@endsection
