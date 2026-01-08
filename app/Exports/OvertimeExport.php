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

    public function map($overtime): array
    {
        // Calculate total actual hours
        $totalSec = $overtime->clocks->sum('total_time_taken');
        $total_hm = $totalSec > 0 
            ? sprintf('%02d:%02d', floor($totalSec / 3600), floor(($totalSec % 3600) / 60))
            : '-';

        // Requested hours (decimal → HH:MM)
        $hours = floor($overtime->total_hours ?? 0);
        $minutes = round((($overtime->total_hours ?? 0) - $hours) * 60);

        $requested_hm = sprintf('%02d:%02d', $hours, $minutes); 

        return [
            $overtime->date->format('d M Y'),
            $overtime->staff?->staff_name ?? '-',
            $overtime->branch?->name ?? '-',
            $overtime->department?->department_name ?? '-',
            $overtime->type_of_work ?? '-',
            $overtime->clocks->map(function($c){
                $in  = $c->clock_in?->format('H:i') ?? '-';
                $out = $c->clock_out?->format('H:i') ?? '-';
                $total = $c->total_time_taken 
                    ? sprintf('%02d:%02d', floor($c->total_time_taken / 3600), floor(($c->total_time_taken % 3600)/60))
                    : '-';
                return "In: $in → Out: $out ($total)";
            })->implode("\n"),
            $requested_hm,
            $total_hm,
            $overtime->remarks ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee',
            'Branch',
            'Department',
            'Type of Work',
            'Clock Sessions',
            'Requested Hours',
            'Actual Hours',
            'Remarks',
        ];
    }
}