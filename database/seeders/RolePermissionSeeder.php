<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $admin   = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $customer = Role::where('slug', 'customer')->first();

        $permissions = Permission::all();

        // ====================== Admin: كل الصلاحيات ======================
        $admin->permissions()->sync($permissions->pluck('id'));

        // ====================== Manager: صلاحيات محدودة ======================
        $managerPermissions = $permissions->whereIn('slug', [
            'category.view',
            'product.view',
            'product.create',
            'product.update',

            'order.view',
            'order.update',

            'coupon.view',
            'coupon.create',

            'report.view',
        ]);

        $manager->permissions()->sync($managerPermissions->pluck('id'));

        // ====================== Customer: بلا صلاحيات ======================
        $customer->permissions()->sync([]); 
    }
}