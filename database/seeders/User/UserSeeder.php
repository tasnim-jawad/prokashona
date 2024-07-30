<?php

namespace Database\Seeders\User;

use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserRole::truncate();
        DB::table('user_user_role')->truncate();
        DB::table('user_user_permission')->truncate();

        $user_role = new UserRole();
        $user_role->id = 10;
        $user_role->name = 'super_admin';
        $user_role->role_serial = 1;
        $user_role->save();

        $user_role = new UserRole();
        $user_role->id = 20;
        $user_role->name = 'admin';
        $user_role->role_serial = 2;
        $user_role->save();

        $user_role = new UserRole();
        $user_role->id = 30;
        $user_role->name = 'production';
        $user_role->role_serial = 3;
        $user_role->save();

        $user_role = new UserRole();
        $user_role->id = 40;
        $user_role->name = 'branch';
        $user_role->role_serial = 4;
        $user_role->save();

        $user_role = new UserRole();
        $user_role->id = 50;
        $user_role->name = 'user';
        $user_role->role_serial = 5;
        $user_role->save();

        $user_role = new UserRole();
        $user_role->id = 51;
        $user_role->name = 'accountant';
        $user_role->role_serial = 6;
        $user_role->save();

        $user_role = new UserRole();
        $user_role->id = 52;
        $user_role->name = 'employee';
        $user_role->role_serial = 7;
        $user_role->save();

        UserPermission::truncate();

        $permission = new UserPermission();
        $permission->id = 10;
        $permission->title = 'can_create';
        $permission->permission_serial = 1;
        $permission->save();

        $permission = new UserPermission();
        $permission->id = 20;
        $permission->title = 'can_edit';
        $permission->permission_serial = 2;
        $permission->save();

        $permission = new UserPermission();
        $permission->id = 30;
        $permission->title = 'can_delete';
        $permission->permission_serial = 3;
        $permission->save();

        User::truncate();

        $user = new User();
        $user->first_name = 'super';
        $user->last_name = 'admin';
        $user->user_name = 'super admin';
        $user->telegram_id = '812239513';
        // $user->role_id = 1;
        $user->mobile_number = '016123';
        $user->email = 'superadmin@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([1]);
        $user->permissions()->attach([1, 2, 3]);

        $user = new User();
        $user->first_name = 'mr';
        $user->last_name = 'admin';
        $user->user_name = 'admin';
        $user->telegram_id = '664398905'; // parvej vai
        // $user->role_id = 2;
        $user->mobile_number = '016124';
        $user->email = 'admin@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([2]);
        $user->permissions()->attach([1, 2, 3]);

        $user = new User();
        $user->first_name = 'mr';
        $user->last_name = 'production';
        $user->user_name = 'production';
        $user->telegram_id = '812239513';
        // $user->role_id = 3;
        $user->mobile_number = '016125678';
        $user->email = 'production@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([3]);
        $user->permissions()->attach([1, 2, 3]);

        $user = new User();
        $user->first_name = 'mr';
        $user->last_name = 'accountant';
        $user->user_name = 'accountant';
        $user->telegram_id = '812239513';
        // $user->role_id = 3;
        $user->mobile_number = '0161256789';
        $user->email = 'accountant@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([6]);
        $user->permissions()->attach([1, 2, 3]);

        $user = new User();
        $user->first_name = 'mr';
        $user->last_name = 'branch1';
        $user->user_name = 'branch1';
        $user->telegram_id = '812239513';
        // $user->role_id = 3;
        $user->mobile_number = '016125';
        $user->email = 'branch1@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([4]);
        $user->permissions()->attach([1, 2, 3]);

        $user = new User();
        $user->first_name = 'mr';
        $user->last_name = 'branch2';
        $user->user_name = 'branch2';
        $user->telegram_id = '812239513';
        // $user->role_id = 3;
        $user->mobile_number = '0161256';
        $user->email = 'branch2@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([4]);


        $user = new User();
        $user->first_name = 'mr';
        $user->last_name = 'user';
        $user->user_name = 'user';
        $user->telegram_id = '812239513';
        // $user->role_id = 3;
        $user->mobile_number = '01612567';
        $user->email = 'user@gmail.com';
        $user->password = Hash::make('12345678');
        $user->save();
        $user->roles()->attach([5]);

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->first_name = 'mr';
            $user->last_name = 'employee';
            $user->user_name = 'employee'.$i;
            $user->telegram_id = '812239513';
            // $user->role_id = 3;
            $user->mobile_number = '0161256e'.$i;
            $user->email = 'e'.$i.'@gmail.com';
            $user->password = Hash::make('12345678');
            $user->save();
            $user->roles()->attach([7]); // employee
        }
    }
}
