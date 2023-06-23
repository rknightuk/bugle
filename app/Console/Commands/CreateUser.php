<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('What is your email?');
        $password = $this->ask('Password?');
        $confirmPassword = $this->ask('Confirm password');

        if ($password !== $confirmPassword)
        {
            return "Passwords don't match";
        }

        $user = User::create([
            'name' => '',
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
