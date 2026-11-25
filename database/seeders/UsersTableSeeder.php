<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('users')->delete();

        \DB::table('users')->insert([
            0 => [
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@csj.com',
                'email_verified_at' => null,
                'password' => '$2y$12$q3C7uZb1WubjBBYtn1/nDuAeTtw/he2OE/dCEv2wmEU46LwZp8h7q',
                'remember_token' => 'iLJnTu6NVwxt3uKZXt98zbovkfEWMs4ftc7CfAhuhKG2LTfRDm7iPDVsw9cP',
                'created_at' => '2026-09-28 09:53:51',
                'updated_at' => '2026-11-20 09:02:31',
                'is_admin' => 1,
            ],
        ]);

    }
}
