<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUsernameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')
            ->whereNotNull('email')  // Make sure the email is not null
            ->update(['username' => DB::raw('email')]);

        // Optional: You can print the result to verify the update
        $this->command->info('Username column has been updated with the email values.');
    }
}
