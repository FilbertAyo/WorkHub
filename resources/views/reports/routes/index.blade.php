<x-app-layout>

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="routes-tab" data-toggle="tab" href="#routes" role="tab"
                        aria-controls="routes" aria-selected="true">Start - Destinations Routes</a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('reports.route.download', ['type' => 'pdf'] + request()->all()) }}"
                class="btn btn-sm btn-danger mb-2"><i class="bi bi-file-earmark-pdf-fill me-2"></i> Download PDF</a>
        </div>
    </div>

    <div class="row my-2">
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-body">
                    <table class="table table-bordered datatables" id="dataTable-routes">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Pick Point</th>
                                <th>Destinations</th>
                                <th>Amount (Tsh)</th>
                                <th>Transport Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routes as $index => $route)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $route['pick_point'] }}</td>
                                    <td>{{ implode(' → ', $route['destinations']->toArray()) }}</td>
                                    <td>{{ number_format($route['petty_amount'], 0) }}</td>
                                    <td>{{ $route['transport_mode'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No route found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
