<?php

namespace App\Exports;

use App\Models\Petty;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;


class PettyExport implements FromView, WithTitle
{
    protected $from;
    protected $to;
    protected $status;
    protected $department;

    public function __construct($from = null, $to = null, $status = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->status = $status;
        $this->department = optional(Auth::user()->department)->name ?? 'N/A';
    }

    public function view(): View
{
    $query = Petty::with(['user', 'attachments', 'trips.stops', 'lists', 'trips.startPoint']);

    if ($this->from && $this->to) {
        // Convert dates
        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();

        // If status is 'paid', filter by paid_date. Otherwise, filter by created_at.
        if ($this->status && $this->status === 'paid') {
            $query->whereBetween('paid_date', [$from, $to]);
        } else {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    if ($this->status) {
        $query->where('status', $this->status);
    }

    return view('reports.petty.excel', [
        'petties' => $query->get(),
        'department' => $this->department,
        'from' => $this->from,
        'to' => $this->to,
        'status' => $this->status,
    ]);
}


    public function title(): string
    {
        return 'Petty Cash Report';
    }
}
