@extends('layouts.report')

@section('content')
    <h2>
        TRANSACTION REPORT OF {{ Auth::user()->department->name ?? 'N/A' }}
        @if (request()->has('from') && request()->has('to'))
            AS FROM {{ \Carbon\Carbon::parse(request('from'))->format('M d, Y') }}
            TO {{ \Carbon\Carbon::parse(request('to'))->format('M d, Y') }}
        @endif
    </h2>

    <table>
        <thead>
            <tr>
                <th>#PVC</th>
                <th>Date</th>
                <th>Name</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp

            @forelse ($transactions as $transaction)
                @php
                    $totalAmount += $transaction->amount;
                @endphp
                <tr>
                    <td>#{{ str_pad($transaction->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->paid_date)->format('d/m/Y') }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No transactions found.</td>
                </tr>
            @endforelse

            @if (count($transactions) > 0)
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                </tr>
            @endif
        </tbody>

    </table>
@endsection
