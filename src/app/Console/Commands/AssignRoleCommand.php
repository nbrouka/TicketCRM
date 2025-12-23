<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email {$email} not found.");

            return;
        }

        $role = Role::where('name', $roleName)->first();

        if (! $role) {
            $this->error("Role {$roleName} not found.");

            return;
        }

        $user->assignRole($role);

        $this->info("Role {$roleName} assigned to user {$email} successfully.");
    }
}
