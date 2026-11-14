<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
        $password = (string) $this->secret('Password (16+ characters, mixed case, number, symbol)');
        $confirmation = (string) $this->secret('Confirm password');

        if ($name === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Enter a valid name and email address.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('An account already uses that email address.');

            return self::FAILURE;
        }

        $isStrongPassword = Validator::make(['password' => $password], [
            'password' => [Password::min(16)->mixedCase()->numbers()->symbols()],
        ])->passes();

        if (! $isStrongPassword || ! hash_equals($password, $confirmation)) {
            $this->error('Use a strong password with at least 16 characters and confirm it exactly.');

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
            'must_change_password' => true,
        ]);

        $this->info("Created {$role} account for {$email}.");

        return self::SUCCESS;
    }
}
