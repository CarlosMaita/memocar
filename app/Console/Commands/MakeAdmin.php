<?php

namespace App\Console\Commands;

use App\Actions\SeedMaintenanceCatalog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeAdmin extends Command
{
    protected $signature = 'memocar:admin {email} {--name=Administrador} {--password= : Si se omite se genera una aleatoria}';

    protected $description = 'Crea un usuario administrador (o promueve uno existente) y fija su contraseña';

    public function handle(): int
    {
        $password = $this->option('password') ?: Str::password(16, symbols: false);

        $user = User::firstOrNew(['email' => $this->argument('email')]);
        $user->forceFill([
            'name' => $user->exists ? $user->name : $this->option('name'),
            'password' => $password,
            'email_verified_at' => $user->email_verified_at ?? now(),
            'is_admin' => true,
            'is_demo' => false,
        ])->save();
        (new SeedMaintenanceCatalog)($user);

        $this->info("Administrador listo: {$user->email}");
        $this->line("Contraseña: {$password}");

        return self::SUCCESS;
    }
}
