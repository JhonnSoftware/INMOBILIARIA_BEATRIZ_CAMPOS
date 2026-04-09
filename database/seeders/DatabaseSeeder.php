<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $ownerRole = Role::query()->where('slug', 'dueno')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Administrador BC',
                'username' => 'dueno',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $ownerRole->id,
                'is_active' => true,
                'paginas_permitidas' => null,
            ]
        );

        $this->call([
            ProyectosSeeder::class,
        ]);
    }
}
