<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use App\Models\User; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (! Gate::allows('view-reports')) {
            abort(403);
        }

        $user = Auth::user();
        $selectedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $selectedUserId = $request->input('user_id'); // Ambil ID user dari filter

        $reportDataQuery = DailyTask::whereDate('updated_at', $selectedDate)
                                  ->with(['assignedToStaff', 'task.project']);
        
        $filterableUsers = collect(); // Siapkan koleksi kosong untuk user yang bisa difilter

        // Logika pengambilan data berdasarkan peran
        if ($user->role === 'manager') {
            // Manager bisa memfilter semua staff dan kepala divisi
            $filterableUsers = User::whereIn('role', ['staff', 'kepala_divisi'])->get();
            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_staff_id', $selectedUserId);
            }
        } 
        elseif ($user->role === 'kepala_divisi') {
            // Kepala Divisi hanya bisa memfilter staff di divisinya
            $staffIds = User::where('division_id', $user->division_id)->pluck('id');
            $filterableUsers = User::whereIn('id', $staffIds)->get();

            $reportDataQuery->whereHas('task.divisions', function ($query) use ($user) {
                $query->where('divisions.id', $user->division_id);
            });
            if ($selectedUserId) {
                $reportDataQuery->where('assigned_to_staff_id', $selectedUserId);
            }
        } 
        elseif ($user->role === 'staff') {
            // Staff hanya melihat laporannya sendiri
            $reportDataQuery->where('assigned_to_staff_id', $user->id);
        }

        $reportData = $reportDataQuery->get();

        return view('reports.index', [
            'reportData' => $reportData,
            'selectedDate' => $selectedDate->format('Y-m-d'),
            'filterableUsers' => $filterableUsers, // Kirim daftar user ke view
            'selectedUserId' => $selectedUserId, // Kirim user yang dipilih
        ]);

    }

    public function exportDailyTasks(Request $request)
{
    $date = $request->input('date') ?? Carbon::today()->format('Y-m-d');

    $tasks = DailyTask::with(['assignedToStaff', 'task.project'])
        ->whereDate('updated_at', $date)
        ->get();

    $filename = "daily_tasks_" . $date . ".csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Staff', 'Project', 'Deskripsi', 'Tanggal Update'];

    $callback = function() use ($tasks, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($tasks as $task) {
            fputcsv($file, [
                $task->id,
                $task->assignedToStaff?->name ?? '-',
                $task->task?->project?->name ?? '-',
                $task->description ?? '-',
                $task->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}