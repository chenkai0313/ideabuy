<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminsSeeder::class);
        $this->call(EncryptTokenSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(RoleAdminSeeder::class);
        $this->call(SystemBankInfoSeeder::class);
        $this->call(SystemMsgTemplateSeeder::class);
        $this->call(SystemMsgTemplateInfoSeeder::class);
        $this->call(SystemAdDataSeeder::class);
        $this->call(UserBaseDataSeeder::class);
        $this->call(UserWalletInfoSeeder::class);
        $this->call(SystemfileSeeder::class);
        $this->call(SystemRegionSeeder::class);
    }


}
