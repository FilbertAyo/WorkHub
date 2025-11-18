@extends('layouts.report')

@section('content')
    <h2>MARS COMMUNICATIONS LTD <br> Route Price List</h2>
    <table>
        <thead>
            <tr>
                   <th>#</th>
                <th>Pick Point</th>
                <th>Destinations</th>
                <th>Petty Amount</th>
                <th>Transport Mode</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($routes as $index => $route)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $route['pick_point'] }}</td>
                    <td>
                        @foreach ($route['destinations'] as $key => $destination)
                            {{ $destination }}
                            @if (!$loop->last)
TO                            @endif
                        @endforeach
                    </td>
                    <td>{{ number_format($route['petty_amount'], 0) }}</td>
                    <td>{{ $route['transport_mode'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
