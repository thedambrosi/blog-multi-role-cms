<?php

namespace App\Console\Commands;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('invite:create {email : O email da pessoa convidada} {--admin= : Email do admin responsável pelo convite}')]
#[Description('Gera um convite para um novo colaborador')]
class CreateInviteCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admin = $this->option('admin')
            ? User::where('role', 'admin')->where('email', $this->option('admin'))->first()
            : User::where('role', 'admin')->first();

        if (! $admin) {
            $this->error('Nenhum administrador encontrado. Crie um usuário com role admin antes de gerar convites.');

            return self::FAILURE;
        }

        $invite = Invite::create([
            'token' => Str::random(40),
            'email' => $this->argument('email'),
            'expires_at' => now()->addHours(48),
            'created_by' => $admin->id,
        ]);

        $this->info('Convite criado com sucesso.');
        $this->line(url("/convite/{$invite->token}"));

        return self::SUCCESS;
    }
}
