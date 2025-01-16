<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create admin role
        $adminRoleId = DB::table('roles')->insertGetID([
            'name' => 'admin',
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        // create treasurer id
        $treasurerRoleid = DB::table('roles')->insertGetID([
            'name' => 'treasurer',
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        // create donator role
        $donatorRoleId = DB::table('roles')->insertGetID([
            'name'=> 'donator',
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        // manually create two fix admin for tabeam systemm
        $admin1= User::create([
            'name'=> 'Administrator 1',
            'email'=>'admin1@tabeam.com',
            'password'=> Hash::make('admin123'),
            'contact_number'=> '0123456789'
        ]);
        $admin2 = User::create([
            'name'=> 'Administrator 2',
            'email'=>'admin2@tabeam.com',
            'password'=> Hash::make('admin123'),
            'contact_number'=> '01098765432'
        ]);

        // assign user to admin role
        $admin1->roles()->attach($adminRoleId,['created_at'=>now(),'updated_at'=>now()]);
        $admin2->roles()->attach($adminRoleId,['created_at'=>now(),'updated_at'=>now()]);
    }
}
