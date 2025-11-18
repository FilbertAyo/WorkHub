@extends('layouts.report')

@section('content')
    <div class="text-center">
        <h2 class="text-center">MARS COMMUNICATIONS LTD <br> PETTY CASH REPLENISHMENT FOR
            {{ optional(Auth::user()->department)->name ?? 'N/A' }}
            @if (request()->filled('from') && request()->filled('to'))
                FROM {{ request('from') }} TO {{ request('to') }}
            @endif

        </h2>
        <h4 class="text-center">
            @if (request()->filled('status'))
                @if (request('status') != 'paid')
                    <br>
                    {{ ucfirst(request('status')) }}
                @endif
            @endif
        </h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Particulars</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($petties as $petty)
                <tr>
                    <td>
                        @if (request('status') != 'paid')
                            {{ $petty->created_at->format('d/m/Y') }}
                        @else
                            {{ $petty->paid_date ? \Carbon\Carbon::parse($petty->paid_date)->format('d/m/Y') : '-' }}
                        @endif
                    </td>
                    <td><strong>{{ $petty->request_for }}</strong></td>
                    <td><strong>{{ number_format($petty->amount, 2) }}</strong></td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <div class="mb-1">
                            <strong>Name:</strong> {{ $petty->user->name }}
                        </div>
                        <div class="mb-1">
                            {{ $petty->reason }}
                        </div>

                        @if ($petty->request_for == 'Sales Delivery')
                            <div class="mb-1">
                                <ul class="mb-1">
                                    @foreach ($petty->attachments as $attachment)
                                        <li>{{ $attachment->name }}: {{ $attachment->product_name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <strong><em>Routes:</em></strong>
                                <ul>
                                    @foreach ($petty->trips as $trip)
                                        <li>
                                            {{ $trip->startPoint->name }}
                                            @foreach ($trip->stops as $stop)
                                                TO {{ $stop->destination }}
                                            @endforeach
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif ($petty->request_for == 'Transport')
                            <div>
                                <strong><em>Routes:</em></strong>
                                <ul>
                                    @foreach ($petty->trips as $trip)
                                        <li>
                                            {{ $trip->startPoint->name }}
                                            @foreach ($trip->stops as $stop)
                                                TO {{ $stop->destination }}
                                            @endforeach
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif ($petty->request_for == 'Office Supplies')
                            <div>
                                <strong><em>Items:</em></strong>
                                <ul>
                                    @foreach ($petty->lists as $item)
                                        <li>
                                            {{ $item->item_name }} ({{ $item->quantity }}) –
                                            TZS {{ number_format($item->price) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </td>
                    <td>
                         @if (request('status') != 'paid')
                         {{ $petty->status }}
                         @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No petties found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
