<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('cms:user:create')]
#[Description('Create a CMS user without exposing credentials in the shell history.')]
class CreateCmsAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = trim((string) $this->ask('Name'));
        $email = mb_strtolower(trim((string) $this->ask('Email')));
        $password = (string) $this->secret('Password (12 characters minimum)');
        $confirmation = (string) $this->secret('Confirm password');

        if ($name === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Enter a valid name and email address.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('An account already uses that email address.');

            return self::FAILURE;
        }

        if (mb_strlen($password) < 12 || ! hash_equals($password, $confirmation)) {
            $this->error('Use a password with at least 12 characters and confirm it exactly.');

            return self::FAILURE;
        }

        $role = User::query()->exists()
            ? $this->choice('Role', array_keys(User::CMS_ROLES), 'administrator')
            : 'super_admin';

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'is_active' => true,
        ]);

        $this->info("Created {$role} account for {$email}.");

        return self::SUCCESS;
    }
}
