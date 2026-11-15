<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {email?} {--name=}';

    protected $description = 'Create an administrator account from the trusted command line';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Administrator email');
        $name = $this->option('name') ?: $this->ask('Administrator name');
        $password = $this->secret('Administrator password (12+ characters)');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:12'],
            ],
        );

        if ($validator->fails()) {
            $this->components->error($validator->errors()->first());

            return self::FAILURE;
        }

        DB::transaction(function () use ($email, $name, $password): void {
            $user = User::query()->firstOrNew(['email' => $email]);
            $user->fill(['name' => $name, 'password' => Hash::make($password), 'is_admin' => true]);
            $user->save();

            if (config('session.driver') === 'database') {
                DB::table(config('session.table'))->where('user_id', $user->id)->delete();
            }
        });

        $this->components->info('Administrator account is ready.');

        return self::SUCCESS;
    }
}
