<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Task;
use App\Models\DailyTask;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-projects', function (User $user) {
            return $user->role === 'manager';
        });

        Gate::define('view-project', function (User $user) {
            return $user->role === 'manager' || $user->role === 'kepala_divisi' || $user->role === 'admin' || $user->role === 'staff';
        });

        Gate::define('create-task', function (User $user) {
            return $user->role === 'kepala_divisi';
        });

        // Modifikasi Gate ini
        Gate::define('view-task', function (User $user, Task $task) {
            // Izinkan jika user adalah Manager
            if ($user->role === 'manager') {
                return true;
            }
            // Izinkan jika user adalah Kepala Divisi DAN divisinya 
            // ada di dalam daftar kolaborator tugas tersebut.
            if ($user->role === 'kepala_divisi') {
                return $task->divisions->contains($user->division_id);
            }
            return false;
        });

        Gate::define('claim-task', function (User $user, DailyTask $dailyTask) {
            // User harus berada di divisi yang sama dengan tugas tersebut
            $isCorrectDivision = $user->division_id === $dailyTask->task->division_id;

            // Izinkan jika user adalah staff atau kepala divisi dari divisi yang benar
            return ($user->role === 'staff' || $user->role === 'kepala_divisi') && $isCorrectDivision;
        });

        Gate::define('validate-task', function (User $user, DailyTask $dailyTask) {
        // Izinkan jika user adalah Manager
            if ($user->role === 'manager') {
                return true;
            }
            // Izinkan jika user adalah Kepala Divisi dari divisi yang sama dengan tugas
            return $user->role === 'kepala_divisi' && $user->division_id === $dailyTask->task->division_id;
        });

        Gate::define('manage-admin-tasks', function (User $user){
            return $user->role === 'manager' || $user->role === 'admin';
        });

        Gate::define('view-reports', function (User $user) {
            return in_array($user->role, ['manager', 'kepala_divisi', 'staff']);
        });

        Gate::define('manage-ad-hoc-tasks', function (User $user) {
            return in_array($user->role, ['manager', 'kepala_divisi']);
        });

        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
    }
}
