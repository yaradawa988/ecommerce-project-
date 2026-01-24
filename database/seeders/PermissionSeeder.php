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
            ['name' => 'Create Product', 'slug' => 'product.create'],
            ['name' => 'Update Product', 'slug' => 'product.update'],
            ['name' => 'Delete Product', 'slug' => 'product.delete'],
            ['name' => 'View Orders', 'slug' => 'order.view'],
            ['name' => 'Update Order Status', 'slug' => 'order.update'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
