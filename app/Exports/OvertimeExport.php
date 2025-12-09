<?php

namespace App\Exports;

use App\Models\Overtime;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OvertimeExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee Name',
            'Branch',
            'Department',
            'Clock Sessions',
            'Requested Hours',
            'Actual Hours',
            'Remarks',
        ];
    }

    public function map($overtime): array
    {
        // Format clock sessions nicely: In: 09:00 - Out: 17:30 (8h 30m) | In: 18:00 - Out: 20:00 (2h)
        $clockSessions = $overtime->clocks->map(function ($session) {
            $in  = $session->clock_in?->format('H:i') ?? '-';
            $out = $session->clock_out?->format('H:i') ?? '-';
            return "In: {$in} - Out: {$out} ({$session->total_hm})";
        })->implode(' | ');

        if ($clockSessions === '') {
            $clockSessions = '-';
        }

        return [
            $overtime->date->format('d M Y'),
            $overtime->staff->staff_name ?? 'N/A',
            $overtime->branch?->name ?? 'N/A',
            $overtime->department?->department_name ?? 'N/A',
            $clockSessions,
            $overtime->requested_hm ?? '-',
            $overtime->total_hm ?? '-',
            $overtime->remarks ?? '-',
        ];
    }
}