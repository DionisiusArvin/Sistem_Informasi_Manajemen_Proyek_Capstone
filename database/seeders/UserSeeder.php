<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID divisi untuk penugasan
        $divisiTeknis1 = Division::where('name', 'Teknis 1')->first();
        $divisiTeknis2 = Division::where('name', 'Teknis 2')->first();
        $divisiArsitektur = Division::where('name', 'Arsitektur')->first();

        // 1. Buat Manager
        User::create([
            'name' => 'Niko',
            'email' => 'niko@proyek.com',
            'password' => Hash::make('manager3210'),
            'role' => 'manager',
        ]);

        // 2. Buat Kepala Divisi
        User::create([
            'name' => 'Kepala_Divisi_Teknis1',
            'email' => 'divisi.teknis1@proyek.com',
            'password' => Hash::make('divisi43210'),
            'role' => 'kepala_divisi',
            'division_id' => $divisiTeknis1->id,
        ]);

        User::create([
            'name' => 'Kepala_Divisi_Teknis 2',
            'email' => 'divisi.teknis2@proyek.com',
            'password' => Hash::make('divisi43210'),
            'role' => 'kepala_divisi',
            'division_id' => $divisiTeknis2->id,
        ]);

        User::create([
            'name' => 'Kepala_Divisi_Arsitektur',
            'email' => 'divisi.arsitektur@proyek.com',
            'password' => Hash::make('divisi43210'),
            'role' => 'kepala_divisi',
            'division_id' => $divisiArsitektur->id,
        ]);


        // 3. Buat beberapa Staff
        User::create([
            'name' => 'Arvin',
            'email' => 'staff.teknis1@proyek.com',
            'password' => Hash::make('arvin1234'),
            'role' => 'staff',
            'division_id' => $divisiTeknis1->id,
        ]);
        
        User::create([
            'name' => 'Ihksan',
            'email' => 'staff.arsitektur1@proyek.com',
            'password' => Hash::make('ihksan1234'),
            'role' => 'staff',
            'division_id' => $divisiArsitektur->id,
        ]);

        // 4. Buat Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@proyek.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
        ]);
    }
}