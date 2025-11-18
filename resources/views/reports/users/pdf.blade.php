@extends('layouts.report')

@section('content')
    <h2>MARS COMMUNICATIONS LTD <br> Active users list</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Branch Name</th>
                <th>Department Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $i => $user)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->branch->name }}</td>
                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
