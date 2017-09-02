<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
            ->insert([
                'company_id' => 1,
                'first_name' => 'example',
                'last_name'  => 'example',
                'email'      => 'info@example.com',
                'password'   => bcrypt('password'),
                'activated'  => true,
                'created_at' => Carbon\Carbon::now(),
                'updated_at' => Carbon\Carbon::now(),
            ]);
    }
}
