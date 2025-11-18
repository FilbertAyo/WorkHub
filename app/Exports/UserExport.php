<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from, $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    protected $index = 0;
    public function collection()
    {
        $query = User::with(['branch', 'department'])
            ->where('status', 'active');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Name', 'Email', 'Phone Number', 'Branch Name', 'Department Name'];
    }

    public function map($user): array
    {
        return [
            ++$this->index,
            $user->name,
            $user->email,
            $user->phone,
            optional($user->branch)->name,
            optional($user->department)->name,
        ];
    }
}
