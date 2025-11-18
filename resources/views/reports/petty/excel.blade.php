@extends('layouts.report')

@section('content')

    <table>
        <thead>
            <tr>
                <th colspan="3" style="text-align:center; font-weight: bold;">
                    MARS COMMUNICATIONS LTD - PETTY CASH REPLENISHMENT FOR {{ $department }}
                    @if ($from && $to)
                        FROM {{ $from }} TO {{ $to }}
                    @endif
                    <h4 class="text-center">
                        @if ($status)
                            @if ($status != 'paid')
                                <br>
                                {{ ucfirst($status) }}
                            @endif
                        @endif
                    </h4>
                </th>
            </tr>
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
                        <strong>Name:</strong> {{ $petty->user->name }}<br>
                        <strong>Reason:</strong> {{ $petty->reason }}<br>

                        @if ($petty->request_for === 'Sales Delivery')
                            <strong>Delivery Details:</strong><br>
                            @foreach ($petty->attachments as $attachment)
                                - {{ $attachment->name }}: {{ $attachment->product_name }}<br>
                            @endforeach
                            <strong>Routes:</strong><br>
                            @foreach ($petty->trips as $trip)
                                - {{ $trip->startPoint->name }}
                                @foreach ($trip->stops as $stop)
                                    → {{ $stop->destination }}
                                @endforeach
                                <br>
                            @endforeach
                        @elseif ($petty->request_for === 'Transport')
                            <strong>Routes:</strong><br>
                            @foreach ($petty->trips as $trip)
                                - {{ $trip->startPoint->name }}
                                @foreach ($trip->stops as $stop)
                                    → {{ $stop->destination }}
                                @endforeach
                                <br>
                            @endforeach
                        @elseif ($petty->request_for === 'Office Supplies')
                            <strong>Items:</strong><br>
                            @foreach ($petty->lists as $item)
                                - {{ $item->item_name }} ({{ $item->quantity }}) = TZS
                                {{ number_format($item->price) }}<br>
                            @endforeach
                        @endif
                    </td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No petties found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
