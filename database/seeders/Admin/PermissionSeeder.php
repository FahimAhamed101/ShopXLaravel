<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array(
            array('id' => '1', 'name' => 'KYC Management', 'guard_name' => 'admin', 'group_name' => 'KYC Management', 'created_at' => '2025-06-24 03:54:55', 'updated_at' => '2025-06-24 03:54:55'),
            array('id' => '2', 'name' => 'Role Management', 'guard_name' => 'admin', 'group_name' => 'Access Management', 'created_at' => '2025-06-24 04:12:51', 'updated_at' => '2025-06-24 04:12:51'),
            array('id' => '3', 'name' => 'Role User Management', 'guard_name' => 'admin', 'group_name' => 'Access Management', 'created_at' => '2025-06-24 04:13:05', 'updated_at' => '2025-06-24 04:13:05'),
            array('id' => '5', 'name' => 'Category Management', 'guard_name' => 'admin', 'group_name' => 'Product Categoires', 'created_at' => '2025-06-30 09:43:43', 'updated_at' => '2025-06-30 09:43:43'),
            array('id' => '6', 'name' => 'Tags Management', 'guard_name' => 'admin', 'group_name' => 'Product Tags', 'created_at' => '2025-06-30 09:44:13', 'updated_at' => '2025-06-30 09:44:13'),
            array('id' => '7', 'name' => 'Brand Management', 'guard_name' => 'admin', 'group_name' => 'Product Brands', 'created_at' => '2025-06-30 11:21:22', 'updated_at' => '2025-06-30 11:21:22'),
            array('id' => '8', 'name' => 'Product Management', 'guard_name' => 'admin', 'group_name' => 'Products', 'created_at' => '2025-07-20 06:47:31', 'updated_at' => '2025-07-20 06:47:31'),
            array('id' => '9', 'name' => 'Order Management', 'guard_name' => 'admin', 'group_name' => 'Order', 'created_at' => '2025-09-01 09:38:54', 'updated_at' => '2025-09-01 09:38:54'),
            array('id' => '10', 'name' => 'Ecommerce Management', 'guard_name' => 'admin', 'group_name' => 'Ecommerce', 'created_at' => '2025-09-01 09:42:59', 'updated_at' => '2025-09-01 09:42:59'),
            array('id' => '11', 'name' => 'Section Management', 'guard_name' => 'admin', 'group_name' => 'Home Sections', 'created_at' => '2025-09-01 09:48:55', 'updated_at' => '2025-09-01 09:48:55'),
            array('id' => '12', 'name' => 'Subscriber Management', 'guard_name' => 'admin', 'group_name' => 'Subscribers', 'created_at' => '2025-09-01 10:48:30', 'updated_at' => '2025-09-01 10:48:30'),
            array('id' => '13', 'name' => 'Withdraw Management', 'guard_name' => 'admin', 'group_name' => 'Withdraw', 'created_at' => '2025-09-01 10:50:41', 'updated_at' => '2025-09-01 10:50:41'),
            array('id' => '14', 'name' => 'Page Management', 'guard_name' => 'admin', 'group_name' => 'Page Builder', 'created_at' => '2025-09-01 10:52:48', 'updated_at' => '2025-09-01 10:52:48'),
            array('id' => '15', 'name' => 'Advertisement Management', 'guard_name' => 'admin', 'group_name' => 'Advertisement', 'created_at' => '2025-09-01 10:54:32', 'updated_at' => '2025-09-01 10:54:32'),
            array('id' => '16', 'name' => 'Contact Management', 'guard_name' => 'admin', 'group_name' => 'Contact', 'created_at' => '2025-09-01 10:56:12', 'updated_at' => '2025-09-01 10:56:12'),
            array('id' => '17', 'name' => 'Payment Setting', 'guard_name' => 'admin', 'group_name' => 'Payment Setting', 'created_at' => '2025-09-01 10:58:41', 'updated_at' => '2025-09-01 10:58:41'),
            array('id' => '18', 'name' => 'Settings Management', 'guard_name' => 'admin', 'group_name' => 'Site Settings', 'created_at' => '2025-09-01 11:00:32', 'updated_at' => '2025-09-01 11:00:32')
        );

        DB::table('permissions')->insert($permissions);
    }
}
