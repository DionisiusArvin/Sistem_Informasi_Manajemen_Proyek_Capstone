<?php

namespace App\Exports;

use App\Models\DailyTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DailyTasksReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date;

    public function __construct(string $date)
    {
        $this->date = Carbon::parse($date);
    }

    public function collection()
    {
        $user = Auth::user();
        return DailyTask::whereHas('task', function ($query) use ($user) {
                $query->where('division_id', $user->division_id);
            })->whereDate('updated_at', $this->date)
              ->with(['assignedToStaff', 'task.project'])
              ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Staff',
            'Proyek',
            'Tugas Harian',
            'Status',
            'Penyelesaian',
        ];
    }

    public function map($dailyTask): array
    {
        return [
            $dailyTask->assignedToStaff->name ?? 'N/A',
            $dailyTask->task->project->name,
            $dailyTask->name,
            $dailyTask->status,
            ucfirst(str_replace('_', ' ', $dailyTask->completion_status)),
        ];
    }
}