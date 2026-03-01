<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar as roles existentes
        $adminRole = Role::where('name', 'admin')->first();
        $editorRole = Role::where('name', 'editor')->first();
        $authorRole = Role::where('name', 'author')->first();
        $developerRole = Role::where('name', 'developer')->first();

        // Criar usuário Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Criar usuário Editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'João Editor',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $editor->assignRole($editorRole);

        // Criar usuário Author
        $author = User::firstOrCreate(
            ['email' => 'author@example.com'],
            [
                'name' => 'Maria Autora',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $author->assignRole($authorRole);

        // Criar usuário Developer
        $developer = User::firstOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Carlos Desenvolvedor',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $developer->assignRole($developerRole);

        // Criar usuários adicionais para testes
        $this->createAdditionalUsers($adminRole, $editorRole, $authorRole, $developerRole);
    }

    /**
     * Criar usuários adicionais para cada role
     */
    private function createAdditionalUsers($adminRole, $editorRole, $authorRole, $developerRole): void
    {
        // Mais admins
        $admins = [
            ['name' => 'Ana Administradora', 'email' => 'ana.admin@example.com'],
            ['name' => 'Pedro Admin', 'email' => 'pedro.admin@example.com'],
        ];

        foreach ($admins as $adminData) {
            $user = User::firstOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($adminRole);
        }

        // Mais editores
        $editors = [
            ['name' => 'Clara Editora', 'email' => 'clara.editora@example.com'],
            ['name' => 'Roberto Revisor', 'email' => 'roberto.revisor@example.com'],
            ['name' => 'Paula Publicadora', 'email' => 'paula.publicadora@example.com'],
        ];

        foreach ($editors as $editorData) {
            $user = User::firstOrCreate(
                ['email' => $editorData['email']],
                [
                    'name' => $editorData['name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($editorRole);
        }

        // Mais autores
        $authors = [
            ['name' => 'Lucas Escritor', 'email' => 'lucas.escritor@example.com'],
            ['name' => 'Beatriz Redatora', 'email' => 'beatriz.redatora@example.com'],
            ['name' => 'Fernando Cronista', 'email' => 'fernando.cronista@example.com'],
            ['name' => 'Carla Colunista', 'email' => 'carla.colunista@example.com'],
        ];

        foreach ($authors as $authorData) {
            $user = User::firstOrCreate(
                ['email' => $authorData['email']],
                [
                    'name' => $authorData['name'],
                    'password' => Hash::make('password'),
                    'status' => rand(0, 1) ? 'active' : 'inactive', // alguns inativos para teste
                    'email_verified_at' => rand(0, 1) ? now() : null, // alguns não verificados
                ]
            );
            $user->assignRole($authorRole);
        }

        // Mais developers
        $developers = [
            ['name' => 'Tiago Programador', 'email' => 'tiago.dev@example.com'],
            ['name' => 'Vanessa Engenheira', 'email' => 'vanessa.eng@example.com'],
        ];

        foreach ($developers as $devData) {
            $user = User::firstOrCreate(
                ['email' => $devData['email']],
                [
                    'name' => $devData['name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($developerRole);
        }

        // Criar um usuário inativo para teste
        User::firstOrCreate(
            ['email' => 'inativo@example.com'],
            [
                'name' => 'Usuário Inativo',
                'password' => Hash::make('password'),
                'status' => 'inactive',
                'email_verified_at' => null,
            ]
        );
    }
}