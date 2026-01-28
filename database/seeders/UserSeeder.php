<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();

        foreach ($roles as $role) {
            $user = User::create([
                'name' => $role->nama . ' User',
                'email' => strtolower($role->slug) . '@demo.com',
                'password' => Hash::make('password'),
                'role_id' => $role->id,
            ]);

            // Create Profile if needed
            if ($role->slug === 'dosen') {
                \App\Models\Dosen::create([
                    'user_id' => $user->id,
                    'nidn' => '12345678',
                ]);
            }

            if ($role->slug === 'mahasiswa') {
                // Find first prodi or create one
                $prodi = \App\Models\Prodi::first();
                if (!$prodi) {
                    $fakultas = \App\Models\Fakultas::create(['nama' => 'Teknik', 'kode' => 'FT']);
                    $prodi = \App\Models\Prodi::create(['fakultas_id' => $fakultas->id, 'nama' => 'Informatika', 'kode' => 'IF']);
                }

                \App\Models\Mahasiswa::create([
                    'user_id' => $user->id,
                    'prodi_id' => $prodi->id,
                    'nim' => '20240001',
                    'semester' => 4,
                ]);
            }
        }
    }
}
