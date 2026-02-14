<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ===================== Category =====================
            ['name' => 'View Categories', 'slug' => 'category.view'],
            ['name' => 'Create Category', 'slug' => 'category.create'],
            ['name' => 'Update Category', 'slug' => 'category.update'],
            ['name' => 'Delete Category', 'slug' => 'category.delete'],

            // ===================== Product ======================
            ['name' => 'View Products', 'slug' => 'product.view'],
            ['name' => 'Create Product', 'slug' => 'product.create'],
            ['name' => 'Update Product', 'slug' => 'product.update'],
            ['name' => 'Delete Product', 'slug' => 'product.delete'],

            // ===================== Orders =======================
            ['name' => 'View Orders',       'slug' => 'order.view'],
            ['name' => 'Update Order',      'slug' => 'order.update'],
            ['name' => 'Delete Order',      'slug' => 'order.delete'],

            // ===================== Users ========================
            ['name' => 'View Users', 'slug' => 'user.view'],
             ['name' => 'Show User', 'slug' => 'user.show'],
            ['name' => 'Create Users', 'slug' => 'user.create'],
            ['name' => 'Update Users', 'slug' => 'user.update'],
            ['name' => 'Delete Users', 'slug' => 'user.delete'],

            // ===================== Coupons ======================
            ['name' => 'View Coupons',      'slug' => 'coupon.view'],
            ['name' => 'Create Coupon',     'slug' => 'coupon.create'],
            ['name' => 'Update Coupon',     'slug' => 'coupon.update'],
            ['name' => 'Delete Coupon',     'slug' => 'coupon.delete'],

            // ===================== Shipping =====================
            ['name' => 'View Shipping',     'slug' => 'shipping.view'],
            ['name' => 'Update Shipping',   'slug' => 'shipping.update'],

            // ===================== Reports ======================
            ['name' => 'View Reports',      'slug' => 'report.view'],

            // ===================== Settings =====================
            ['name' => 'Update Settings',   'slug' => 'settings.update'],
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
