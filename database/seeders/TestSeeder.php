<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliveryPartner;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\InventoryLog;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Outlet;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Recipe;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Table as RestaurantTable;
use App\Models\User;
use App\Models\Wastage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds with comprehensive test data.
     * This seeder creates all data needed to test the full project.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive test data seeding...');

        // 1. Create Roles and Permissions
        $this->createRolesAndPermissions();

        // 2. Create Users
        $users = $this->createUsers();

        // 3. Create Outlets
        $outlets = $this->createOutlets();

        // 4. Create Categories
        $categories = $this->createCategories();

        // 5. Create Menu Items
        $menuItems = $this->createMenuItems($categories, $outlets);

        // 6. Create Ingredients with Recipes
        $ingredients = $this->createIngredients();

        // 7. Link Recipes to Menu Items
        $this->createRecipes($menuItems, $ingredients);

        // 8. Create Suppliers
        $suppliers = $this->createSuppliers();

        // 9. Create Customers
        $customers = $this->createCustomers();

        // 10. Create Tables
        $tables = $this->createTables($outlets);

        // 11. Create Reservations
        $this->createReservations($customers, $users);

        // 12. Create Orders (Dine-in, Takeaway, Delivery)
        $this->createOrders($menuItems, $customers, $users, $tables);

        // 13. Create Purchases
        $this->createPurchases($ingredients, $suppliers, $users);

        // 14. Create Inventory Logs
        $this->createInventoryLogs($ingredients, $users);

        // 15. Create Expenses
        $this->createExpenses($users);

        // 16. Create Payroll
        $this->createPayroll($users);

        // 17. Create Delivery Partners
        $this->createDeliveryPartners();

        // 18. Create Reviews
        $this->createReviews($customers);

        // 19. Create Settings
        $this->createSettings();

        // 20. Create Wastages
        $this->createWastages($ingredients, $menuItems, $users);

        $this->command->info('Comprehensive test data seeding completed!');
        $this->command->info('========================================');
        $this->command->info('Test Credentials:');
        $this->command->info('  Admin: admin@test.com / password');
        $this->command->info('  Manager: manager@test.com / password');
        $this->command->info('  Cashier: cashier@test.com / password');
        $this->command->info('  Waiter: waiter@test.com / password');
        $this->command->info('========================================');
    }

    protected function createRolesAndPermissions()
    {
        $this->command->info('Creating roles and permissions...');

        $permissions = [
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_roles', 'manage_roles',
            'view_orders', 'create_orders', 'edit_orders', 'delete_orders', 'view_order_details',
            'view_menu', 'create_menu', 'edit_menu', 'delete_menu',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers',
            'view_inventory', 'create_inventory', 'edit_inventory',
            'view_ingredients', 'create_ingredients', 'edit_ingredients', 'delete_ingredients',
            'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers',
            'view_purchases', 'create_purchases', 'edit_purchases',
            'view_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses',
            'view_payroll', 'create_payroll', 'edit_payroll',
            'view_reports', 'export_reports',
            'view_settings', 'manage_settings',
            'view_tables', 'manage_tables',
            'view_reservations', 'create_reservations', 'edit_reservations', 'delete_reservations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin' => ['*'],
            'manager' => [
                'view_users', 'view_roles', 'view_orders', 'create_orders', 'edit_orders', 'view_order_details',
                'view_menu', 'view_categories', 'view_customers', 'view_inventory', 'create_inventory', 'edit_inventory',
                'view_ingredients', 'view_suppliers', 'view_purchases', 'create_purchases', 'edit_purchases',
                'view_expenses', 'create_expenses', 'edit_expenses',
                'view_payroll', 'create_payroll', 'edit_payroll',
                'view_reports', 'export_reports',
                'view_settings', 'manage_settings',
                'view_tables', 'manage_tables',
                'view_reservations', 'create_reservations', 'edit_reservations',
            ],
            'cashier' => [
                'view_orders', 'create_orders', 'edit_orders', 'view_order_details',
                'view_menu', 'view_categories',
                'view_customers', 'create_customers',
            ],
            'waiter' => [
                'view_orders', 'view_menu', 'view_categories',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            if ($perms !== ['*']) {
                $role->syncPermissions($perms);
            }
        }
    }

    protected function createUsers()
    {
        $this->command->info('Creating users...');

        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'position' => 'General Manager',
                'base_salary' => 85000,
                'join_date' => '2024-01-15',
                'status' => 1,
            ]
        );
        $admin->assignRole('super_admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('password'),
                'position' => 'Restaurant Manager',
                'base_salary' => 45000,
                'join_date' => '2024-02-01',
                'status' => 1,
            ]
        );
        $manager->assignRole('manager');

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@test.com'],
            [
                'name' => 'Test Cashier',
                'password' => Hash::make('password'),
                'position' => 'Cashier',
                'base_salary' => 18000,
                'join_date' => '2024-03-01',
                'status' => 1,
            ]
        );
        $cashier->assignRole('cashier');

        $waiter = User::firstOrCreate(
            ['email' => 'waiter@test.com'],
            [
                'name' => 'Test Waiter',
                'password' => Hash::make('password'),
                'position' => 'Waiter',
                'base_salary' => 15000,
                'join_date' => '2024-03-15',
                'status' => 1,
            ]
        );
        $waiter->assignRole('waiter');

        $chef = User::firstOrCreate(
            ['email' => 'chef@test.com'],
            [
                'name' => 'Test Chef',
                'password' => Hash::make('password'),
                'position' => 'Head Chef',
                'base_salary' => 35000,
                'join_date' => '2024-01-20',
                'status' => 1,
            ]
        );
        $chef->assignRole('manager');

        return [$admin, $manager, $cashier, $waiter, $chef];
    }

    protected function createOutlets()
    {
        $this->command->info('Creating outlets...');

        $outlet1 = Outlet::firstOrCreate(
            ['name' => 'Test Main Restaurant'],
            [
                'address' => '123 Test Road, Dhaka 1205',
                'phone' => '+8801234567890',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
                'is_active' => 1,
            ]
        );

        $outlet2 = Outlet::firstOrCreate(
            ['name' => 'Test Branch - Gulshan'],
            [
                'address' => '45 Gulshan Avenue, Dhaka 1212',
                'phone' => '+8801234567891',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
                'is_active' => 1,
            ]
        );

        return [$outlet1, $outlet2];
    }

    protected function createCategories()
    {
        $this->command->info('Creating categories...');

        $categories = [
            ['name' => 'Appetizers', 'priority_order' => 1, 'is_active' => 1],
            ['name' => 'Main Course', 'priority_order' => 2, 'is_active' => 1],
            ['name' => 'Biryani', 'priority_order' => 3, 'is_active' => 1],
            ['name' => 'Pizza', 'priority_order' => 4, 'is_active' => 1],
            ['name' => 'Burgers', 'priority_order' => 5, 'is_active' => 1],
            ['name' => 'Beverages', 'priority_order' => 6, 'is_active' => 1],
            ['name' => 'Desserts', 'priority_order' => 7, 'is_active' => 1],
            ['name' => 'Fast Food', 'priority_order' => 8, 'is_active' => 1],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[] = Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'priority_order' => $cat['priority_order'],
                    'is_active' => $cat['is_active'],
                ]
            );
        }

        return $categoryModels;
    }

    protected function createMenuItems($categories, $outlets)
    {
        $this->command->info('Creating menu items...');

        $menuItems = [
            // Appetizers
            ['name' => 'Chicken Spring Rolls', 'category' => 0, 'base_price' => 250, 'tax_rate' => 5, 'preparation_type' => 'fry'],
            ['name' => 'Vegetable Pakora', 'category' => 0, 'base_price' => 150, 'tax_rate' => 5, 'preparation_type' => 'fry'],
            ['name' => 'Fish Fry', 'category' => 0, 'base_price' => 350, 'tax_rate' => 5, 'preparation_type' => 'fry'],
            ['name' => 'Chicken Wings', 'category' => 0, 'base_price' => 300, 'tax_rate' => 5, 'preparation_type' => 'fry'],

            // Main Course
            ['name' => 'Chicken Curry', 'category' => 1, 'base_price' => 450, 'tax_rate' => 5, 'preparation_type' => 'cook'],
            ['name' => 'Beef Bhuna', 'category' => 1, 'base_price' => 550, 'tax_rate' => 5, 'preparation_type' => 'cook'],
            ['name' => 'Fish Curry', 'category' => 1, 'base_price' => 500, 'tax_rate' => 5, 'preparation_type' => 'cook'],
            ['name' => 'Chicken Mughal', 'category' => 1, 'base_price' => 600, 'tax_rate' => 5, 'preparation_type' => 'cook'],
            ['name' => 'Vegetable Curry', 'category' => 1, 'base_price' => 350, 'tax_rate' => 5, 'preparation_type' => 'cook'],

            // Biryani
            ['name' => 'Chicken Biryani', 'category' => 2, 'base_price' => 350, 'tax_rate' => 5, 'preparation_type' => 'cook'],
            ['name' => 'Beef Biryani', 'category' => 2, 'base_price' => 450, 'tax_rate' => 5, 'preparation_type' => 'cook'],
            ['name' => 'Mutton Biryani', 'category' => 2, 'base_price' => 550, 'tax_rate' => 5, 'preparation_type' => 'cook'],

            // Pizza
            ['name' => 'Margherita Pizza', 'category' => 3, 'base_price' => 800, 'tax_rate' => 5, 'preparation_type' => 'bake'],
            ['name' => 'Chicken Pizza', 'category' => 3, 'base_price' => 950, 'tax_rate' => 5, 'preparation_type' => 'bake'],
            ['name' => 'Veggie Supreme', 'category' => 3, 'base_price' => 750, 'tax_rate' => 5, 'preparation_type' => 'bake'],

            // Burgers
            ['name' => 'Classic Beef Burger', 'category' => 4, 'base_price' => 450, 'tax_rate' => 5, 'preparation_type' => 'assemble'],
            ['name' => 'Chicken Burger', 'category' => 4, 'base_price' => 400, 'tax_rate' => 5, 'preparation_type' => 'assemble'],
            ['name' => 'Double Cheese Burger', 'category' => 4, 'base_price' => 550, 'tax_rate' => 5, 'preparation_type' => 'assemble'],

            // Beverages
            ['name' => 'Cold Drink', 'category' => 5, 'base_price' => 50, 'tax_rate' => 0, 'preparation_type' => 'serve'],
            ['name' => 'Fresh Lemonade', 'category' => 5, 'base_price' => 80, 'tax_rate' => 0, 'preparation_type' => 'serve'],
            ['name' => 'Mango Shake', 'category' => 5, 'base_price' => 150, 'tax_rate' => 0, 'preparation_type' => 'serve'],
            ['name' => 'Coffee', 'category' => 5, 'base_price' => 100, 'tax_rate' => 0, 'preparation_type' => 'serve'],
            ['name' => 'Tea', 'category' => 5, 'base_price' => 30, 'tax_rate' => 0, 'preparation_type' => 'serve'],

            // Desserts
            ['name' => 'Ice Cream', 'category' => 6, 'base_price' => 150, 'tax_rate' => 0, 'preparation_type' => 'serve'],
            ['name' => 'Gulab Jamun', 'category' => 6, 'base_price' => 100, 'tax_rate' => 0, 'preparation_type' => 'serve'],
            ['name' => 'Rasmalai', 'category' => 6, 'base_price' => 200, 'tax_rate' => 0, 'preparation_type' => 'serve'],
        ];

        $menuItemModels = [];
        foreach ($menuItems as $item) {
            $categoryId = $categories[$item['category']]->id;
            $outletId = $outlets[0]->id;

            $menuItemModels[] = MenuItem::firstOrCreate(
                [
                    'name' => $item['name'],
                    'outlet_id' => $outletId,
                ],
                [
                    'category_id' => $categoryId,
                    'slug' => Str::slug($item['name']),
                    'description' => 'Delicious ' . $item['name'] . ' made with fresh ingredients',
                    'base_price' => $item['base_price'],
                    'discount_price' => null,
                    'tax_rate' => $item['tax_rate'],
                    'is_active' => 1,
                    'preparation_type' => $item['preparation_type'],
                    'sku' => 'SKU-' . strtoupper(Str::slug($item['name'], '-')),
                ]
            );
        }

        return $menuItemModels;
    }

    protected function createIngredients()
    {
        $this->command->info('Creating ingredients...');

        $ingredients = [
            ['name' => 'Basmati Rice', 'category' => 'Grains', 'unit' => 'kg', 'current_stock' => 50, 'alert_threshold' => 10, 'unit_cost' => 150],
            ['name' => 'Chicken', 'category' => 'Proteins', 'unit' => 'kg', 'current_stock' => 20, 'alert_threshold' => 5, 'unit_cost' => 350],
            ['name' => 'Beef', 'category' => 'Proteins', 'unit' => 'kg', 'current_stock' => 15, 'alert_threshold' => 5, 'unit_cost' => 650],
            ['name' => 'Mutton', 'category' => 'Proteins', 'unit' => 'kg', 'current_stock' => 10, 'alert_threshold' => 3, 'unit_cost' => 850],
            ['name' => 'Fish', 'category' => 'Proteins', 'unit' => 'kg', 'current_stock' => 12, 'alert_threshold' => 4, 'unit_cost' => 500],
            ['name' => 'Vegetable Oil', 'category' => 'Oils', 'unit' => 'liter', 'current_stock' => 25, 'alert_threshold' => 5, 'unit_cost' => 180],
            ['name' => 'Onion', 'category' => 'Vegetables', 'unit' => 'kg', 'current_stock' => 30, 'alert_threshold' => 8, 'unit_cost' => 60],
            ['name' => 'Garlic', 'category' => 'Vegetables', 'unit' => 'kg', 'current_stock' => 5, 'alert_threshold' => 2, 'unit_cost' => 250],
            ['name' => 'Ginger', 'category' => 'Vegetables', 'unit' => 'kg', 'current_stock' => 4, 'alert_threshold' => 2, 'unit_cost' => 300],
            ['name' => 'Tomato', 'category' => 'Vegetables', 'unit' => 'kg', 'current_stock' => 20, 'alert_threshold' => 5, 'unit_cost' => 80],
            ['name' => 'Potato', 'category' => 'Vegetables', 'unit' => 'kg', 'current_stock' => 40, 'alert_threshold' => 10, 'unit_cost' => 40],
            ['name' => 'Flour', 'category' => 'Grains', 'unit' => 'kg', 'current_stock' => 30, 'alert_threshold' => 8, 'unit_cost' => 45],
            ['name' => 'Cheese', 'category' => 'Dairy', 'unit' => 'kg', 'current_stock' => 5, 'alert_threshold' => 2, 'unit_cost' => 650],
            ['name' => 'Milk', 'category' => 'Dairy', 'unit' => 'liter', 'current_stock' => 15, 'alert_threshold' => 5, 'unit_cost' => 80],
            ['name' => 'Butter', 'category' => 'Dairy', 'unit' => 'kg', 'current_stock' => 3, 'alert_threshold' => 1, 'unit_cost' => 450],
            ['name' => 'Egg', 'category' => 'Dairy', 'unit' => 'piece', 'current_stock' => 100, 'alert_threshold' => 20, 'unit_cost' => 15],
            ['name' => 'Spices Mix', 'category' => 'Spices', 'unit' => 'kg', 'current_stock' => 8, 'alert_threshold' => 2, 'unit_cost' => 500],
            ['name' => 'Salt', 'category' => 'Spices', 'unit' => 'kg', 'current_stock' => 10, 'alert_threshold' => 3, 'unit_cost' => 30],
            ['name' => 'Sugar', 'category' => 'Spices', 'unit' => 'kg', 'current_stock' => 15, 'alert_threshold' => 5, 'unit_cost' => 55],
            ['name' => 'Bread', 'category' => 'Grains', 'unit' => 'piece', 'current_stock' => 30, 'alert_threshold' => 10, 'unit_cost' => 25],
        ];

        $ingredientModels = [];
        foreach ($ingredients as $ing) {
            $ingredientModels[] = Ingredient::firstOrCreate(
                ['name' => $ing['name']],
                [
                    'category' => $ing['category'],
                    'unit' => $ing['unit'],
                    'current_stock' => $ing['current_stock'],
                    'alert_threshold' => $ing['alert_threshold'],
                    'unit_cost' => $ing['unit_cost'],
                ]
            );
        }

        return $ingredientModels;
    }

    protected function createRecipes($menuItems, $ingredients)
    {
        $this->command->info('Creating recipes...');

        $recipes = [
            'Chicken Biryani' => ['Basmati Rice' => 0.3, 'Chicken' => 0.2, 'Vegetable Oil' => 0.05, 'Onion' => 0.05, 'Spices Mix' => 0.02],
            'Beef Biryani' => ['Basmati Rice' => 0.3, 'Beef' => 0.2, 'Vegetable Oil' => 0.05, 'Onion' => 0.05, 'Spices Mix' => 0.02],
            'Mutton Biryani' => ['Basmati Rice' => 0.3, 'Mutton' => 0.2, 'Vegetable Oil' => 0.05, 'Onion' => 0.05, 'Spices Mix' => 0.02],
            'Chicken Curry' => ['Chicken' => 0.25, 'Vegetable Oil' => 0.03, 'Onion' => 0.05, 'Tomato' => 0.05, 'Spices Mix' => 0.02],
            'Beef Bhuna' => ['Beef' => 0.25, 'Vegetable Oil' => 0.03, 'Onion' => 0.05, 'Tomato' => 0.05, 'Spices Mix' => 0.02],
            'Fish Curry' => ['Fish' => 0.25, 'Vegetable Oil' => 0.03, 'Onion' => 0.05, 'Tomato' => 0.05, 'Spices Mix' => 0.02],
            'Margherita Pizza' => ['Flour' => 0.2, 'Cheese' => 0.1, 'Tomato' => 0.05, 'Butter' => 0.02],
            'Chicken Pizza' => ['Flour' => 0.2, 'Cheese' => 0.1, 'Chicken' => 0.1, 'Tomato' => 0.05, 'Butter' => 0.02],
            'Classic Beef Burger' => ['Bread' => 1, 'Beef' => 0.15, 'Cheese' => 0.02, 'Onion' => 0.02, 'Potato' => 0.1],
            'Chicken Burger' => ['Bread' => 1, 'Chicken' => 0.15, 'Cheese' => 0.02, 'Onion' => 0.02, 'Potato' => 0.1],
            'Chicken Wings' => ['Chicken' => 0.2, 'Flour' => 0.05, 'Vegetable Oil' => 0.1, 'Spices Mix' => 0.01],
            'Chicken Spring Rolls' => ['Flour' => 0.1, 'Chicken' => 0.1, 'Onion' => 0.03, 'Vegetable Oil' => 0.15],
        ];

        foreach ($recipes as $menuItemName => $ingredientQuantities) {
            $menuItem = collect($menuItems)->firstWhere('name', $menuItemName);
            if (!$menuItem) continue;

            foreach ($ingredientQuantities as $ingredientName => $quantity) {
                $ingredient = collect($ingredients)->firstWhere('name', $ingredientName);
                if (!$ingredient) continue;

                Recipe::firstOrCreate(
                    [
                        'menu_item_id' => $menuItem->id,
                        'ingredient_id' => $ingredient->id,
                    ],
                    ['quantity' => $quantity]
                );
            }
        }
    }

    protected function createSuppliers()
    {
        $this->command->info('Creating suppliers...');

        $suppliers = [
            ['name' => 'Fresh Meat Co.', 'contact_person' => 'Mr. Rahim', 'phone' => '+8801711111111', 'email' => 'rahim@freshmeat.com', 'address' => 'Karwan Bazar, Dhaka'],
            ['name' => 'Organic Vegetables', 'contact_person' => 'Mrs. Karim', 'phone' => '+8801711111112', 'email' => 'karim@organicveg.com', 'address' => 'Mirpur, Dhaka'],
            ['name' => 'Daily Dairy Supply', 'contact_person' => 'Mr. Hassan', 'phone' => '+8801711111113', 'email' => 'hassan@dailydairy.com', 'address' => 'Uttara, Dhaka'],
            ['name' => 'Rice & Grain Traders', 'contact_person' => 'Mr. Ali', 'phone' => '+8801711111114', 'email' => 'ali@ricegrain.com', 'address' => 'Fulbari, Dhaka'],
            ['name' => 'Spice Masters', 'contact_person' => 'Mr. Khan', 'phone' => '+8801711111115', 'email' => 'khan@spicemasters.com', 'address' => 'Narayanganj'],
        ];

        $supplierModels = [];
        foreach ($suppliers as $sup) {
            $supplierModels[] = Supplier::firstOrCreate(
                ['name' => $sup['name']],
                [
                    'contact_person' => $sup['contact_person'],
                    'phone' => $sup['phone'],
                    'email' => $sup['email'],
                    'address' => $sup['address'],
                    'is_active' => 1,
                ]
            );
        }

        return $supplierModels;
    }

    protected function createCustomers()
    {
        $this->command->info('Creating customers...');

        $customers = [
            ['name' => 'Ahmed Hassan', 'phone' => '+8801811111111', 'email' => 'ahmed@example.com', 'address' => 'Gulshan, Dhaka'],
            ['name' => 'Sarah Rahman', 'phone' => '+8801811111112', 'email' => 'sarah@example.com', 'address' => 'Banani, Dhaka'],
            ['name' => 'Mohammad Ali', 'phone' => '+8801811111113', 'email' => 'ali@example.com', 'address' => 'Dhanmondi, Dhaka'],
            ['name' => 'Fatema Begum', 'phone' => '+8801811111114', 'email' => 'fatema@example.com', 'address' => 'Mirpur, Dhaka'],
            ['name' => 'Kamal Hossain', 'phone' => '+8801811111115', 'email' => 'kamal@example.com', 'address' => 'Uttara, Dhaka'],
            ['name' => 'Nusrat Jahan', 'phone' => '+8801811111116', 'email' => 'nusrat@example.com', 'address' => 'Baridhara, Dhaka'],
            ['name' => 'Rashid Ahmed', 'phone' => '+8801811111117', 'email' => 'rashid@example.com', 'address' => 'Mohakhali, Dhaka'],
            ['name' => 'Priya Das', 'phone' => '+8801811111118', 'email' => 'priya@example.com', 'address' => 'Gulshan 2, Dhaka'],
        ];

        $customerModels = [];
        foreach ($customers as $cust) {
            $customerModels[] = Customer::firstOrCreate(
                ['phone' => $cust['phone']],
                [
                    'name' => $cust['name'],
                    'email' => $cust['email'],
                    'address' => $cust['address'],
                ]
            );
        }

        return $customerModels;
    }

    protected function createTables($outlets)
    {
        $this->command->info('Creating tables...');

        $tables = [
            ['name' => 'Table 1', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 2', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 3', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 4', 'capacity' => 4, 'status' => 'occupied'],
            ['name' => 'Table 5', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 6', 'capacity' => 6, 'status' => 'reserved'],
            ['name' => 'Table 7', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Table 8', 'capacity' => 8, 'status' => 'available'],
            ['name' => 'VIP 1', 'capacity' => 10, 'status' => 'available'],
            ['name' => 'VIP 2', 'capacity' => 10, 'status' => 'occupied'],
        ];

        $tableModels = [];
        $outletId = $outlets[0]->id;
        foreach ($tables as $table) {
            $tableModels[] = RestaurantTable::firstOrCreate(
                [
                    'name' => $table['name'],
                    'outlet_id' => $outletId,
                ],
                [
                    'slug' => Str::slug($table['name']),
                    'capacity' => $table['capacity'],
                    'status' => $table['status'],
                ]
            );
        }

        return $tableModels;
    }

    protected function createReservations($customers, $users)
    {
        $this->command->info('Creating reservations...');

        $reservations = [
            ['customer' => 0, 'date' => now()->addDays(1)->toDateString(), 'guests' => '4', 'status' => 'confirmed'],
            ['customer' => 1, 'date' => now()->addDays(2)->toDateString(), 'guests' => '2', 'status' => 'confirmed'],
            ['customer' => 2, 'date' => now()->addDays(3)->toDateString(), 'guests' => '6', 'status' => 'pending'],
            ['customer' => 3, 'date' => now()->addDays(1)->toDateString(), 'guests' => '10', 'status' => 'confirmed'],
            ['customer' => 4, 'date' => now()->addDays(5)->toDateString(), 'guests' => '4', 'status' => 'cancelled'],
        ];

        foreach ($reservations as $res) {
            Reservation::firstOrCreate(
                [
                    'phone' => $customers[$res['customer']]->phone,
                    'date' => $res['date'],
                ],
                [
                    'name' => $customers[$res['customer']]->name,
                    'guests' => $res['guests'],
                    'status' => $res['status'],
                    'arrangement' => 'Standard',
                ]
            );
        }
    }

    protected function createOrders($menuItems, $customers, $users, $tables)
    {
        $this->command->info('Creating orders...');

        $orderCount = 1;
        $orderStatuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'pending', 'processing'];
        $orderTypes = ['dine_in', 'dine_in', 'dine_in', 'takeaway', 'takeaway', 'delivery', 'delivery'];
        $paymentStatuses = ['paid', 'paid', 'paid', 'paid', 'paid', 'pending', 'pending'];
        $paymentMethods = ['cash', 'cash', 'bKash', 'cash', 'cash', 'bKash', 'card'];

        for ($i = 0; $i < 7; $i++) {
            $orderType = $orderTypes[$i];
            $customerName = $orderType === 'delivery' ? $customers[array_rand($customers)]->name : null;
            $customerPhone = $orderType === 'delivery' ? $customers[array_rand($customers)]->phone : null;
            $tableNumber = $orderType === 'dine_in' ? ($tables[array_rand($tables)]->name) : null;

            $order = Order::firstOrCreate(
                ['order_number' => 'ORD-' . str_pad($orderCount++, 4, '0', STR_PAD_LEFT)],
                [
                    'status' => $orderStatuses[$i],
                    'order_type' => $orderType,
                    'subtotal_amount' => 0,
                    'discount_amount' => 0,
                    'discount_type' => 'percentage',
                    'total_amount' => 0,
                    'payment_status' => $paymentStatuses[$i],
                    'payment_method' => $paymentMethods[$i],
                    'table_number' => $tableNumber,
                    'customer_name' => $customerName,
                    'customer_phone' => $customerPhone,
                    'guest_count' => rand(1, 6),
                    'user_id' => $users[array_rand([0, 1, 2, 3])]->id,
                    'is_stock_deducted' => ($orderStatuses[$i] === 'completed' ? 1 : 0),
                ]
            );

            // Add order items
            $itemCount = rand(1, 4);
            $totalAmount = 0;
            for ($j = 0; $j < $itemCount; $j++) {
                $menuItem = $menuItems[array_rand($menuItems)];
                $quantity = rand(1, 3);
                $price = $menuItem->base_price;
                $itemTotal = $price * $quantity;
                $totalAmount += $itemTotal;

                OrderItem::firstOrCreate(
                    [
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItem->id,
                    ],
                    [
                        'quantity' => $quantity,
                        'price' => $price,
                    ]
                );
            }

            // Update order totals
            $discount = $totalAmount > 1000 ? ($totalAmount * 0.1) : 0;
            $order->update([
                'subtotal_amount' => $totalAmount,
                'discount_amount' => $discount,
                'total_amount' => $totalAmount - $discount,
            ]);

            // Add payment if paid
            if ($paymentStatuses[$i] === 'paid') {
                OrderPayment::firstOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $paymentMethods[$i],
                        'amount' => $totalAmount - $discount,
                        'reference_no' => 'REF-' . Str::random(8),
                    ]
                );
            }
        }
    }

    protected function createPurchases($ingredients, $suppliers, $users)
    {
        $this->command->info('Creating purchases...');

        $purchases = [
            ['supplier' => 0, 'ingredients' => ['Chicken' => 10, 'Beef' => 5], 'status' => 'completed'],
            ['supplier' => 1, 'ingredients' => ['Onion' => 20, 'Tomato' => 15, 'Potato' => 25], 'status' => 'completed'],
            ['supplier' => 2, 'ingredients' => ['Milk' => 20, 'Cheese' => 5, 'Butter' => 3], 'status' => 'completed'],
            ['supplier' => 3, 'ingredients' => ['Basmati Rice' => 30, 'Flour' => 20], 'status' => 'completed'],
            ['supplier' => 4, 'ingredients' => ['Spices Mix' => 5, 'Salt' => 10], 'status' => 'pending'],
        ];

        foreach ($purchases as $purchase) {
            $purchaseRecord = Purchase::firstOrCreate(
                ['reference_no' => 'PUR-' . Str::random(6)],
                [
                    'user_id' => $users[0]->id,
                    'supplier_id' => $suppliers[$purchase['supplier']]->id,
                    'purchase_date' => now()->subDays(rand(1, 10))->toDateString(),
                    'status' => $purchase['status'],
                    'total_amount' => 0,
                ]
            );

            $total = 0;
            foreach ($purchase['ingredients'] as $ingName => $qty) {
                $ingredient = collect($ingredients)->firstWhere('name', $ingName);
                if (!$ingredient) continue;

                $unitPrice = $ingredient->unit_cost;
                $subtotal = $qty * $unitPrice;
                $total += $subtotal;

                PurchaseItem::firstOrCreate(
                    [
                        'purchase_id' => $purchaseRecord->id,
                        'ingredient_id' => $ingredient->id,
                    ],
                    [
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]
                );
            }

            $purchaseRecord->update(['total_amount' => $total]);
        }
    }

    protected function createInventoryLogs($ingredients, $users)
    {
        $this->command->info('Creating inventory logs...');

        foreach ($ingredients as $ingredient) {
            InventoryLog::firstOrCreate(
                [
                    'ingredient_id' => $ingredient->id,
                    'type' => 'addition',
                ],
                [
                    'quantity' => rand(5, 20),
                    'note' => 'Stock replenishment',
                    'user_id' => $users[0]->id,
                ]
            );
        }
    }

    protected function createExpenses($users)
    {
        $this->command->info('Creating expenses...');

        $expenses = [
            ['category' => 'Utilities', 'title' => 'Electricity Bill', 'amount' => 15000, 'payment_method' => 'bank_transfer'],
            ['category' => 'Utilities', 'title' => 'Gas Bill', 'amount' => 8000, 'payment_method' => 'bank_transfer'],
            ['category' => 'Utilities', 'title' => 'Internet Bill', 'amount' => 3000, 'payment_method' => 'cash'],
            ['category' => 'Rent', 'title' => 'Monthly Rent', 'amount' => 100000, 'payment_method' => 'bank_transfer'],
            ['category' => 'Maintenance', 'title' => 'AC Repair', 'amount' => 5000, 'payment_method' => 'cash'],
            ['category' => 'Marketing', 'title' => 'Advertisement', 'amount' => 15000, 'payment_method' => 'bank_transfer'],
        ];

        foreach ($expenses as $expense) {
            Expense::firstOrCreate(
                [
                    'title' => $expense['title'],
                    'date' => now()->subDays(rand(1, 30))->toDateString(),
                ],
                [
                    'category' => $expense['category'],
                    'amount' => $expense['amount'],
                    'payment_method' => $expense['payment_method'],
                    'reference_no' => 'EXP-' . Str::random(6),
                    'user_id' => $users[0]->id,
                ]
            );
        }
    }

    protected function createPayroll($users)
    {
        $this->command->info('Creating payroll records...');

        foreach ($users as $user) {
            Payroll::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'month' => now()->format('F'),
                    'year' => now()->year,
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'base_salary' => $user->base_salary,
                    'bonus_amount' => rand(0, 5000),
                    'deduction_amount' => rand(0, 2000),
                    'advance_amount' => 0,
                    'net_paid' => $user->base_salary + rand(0, 3000) - rand(0, 1000),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                ]
            );
        }
    }

    protected function createDeliveryPartners()
    {
        $this->command->info('Creating delivery partners...');

        $partners = [
            ['name' => 'Delivery Team Alpha', 'phone' => '+8801911111111'],
            ['name' => 'Delivery Team Beta', 'phone' => '+8801911111112'],
            ['name' => 'Express Delivery', 'phone' => '+8801911111113'],
        ];

        foreach ($partners as $partner) {
            DeliveryPartner::firstOrCreate(
                ['name' => $partner['name']],
                [
                    'phone' => $partner['phone'],
                    'is_active' => 1,
                ]
            );
        }
    }

    protected function createReviews($customers)
    {
        $this->command->info('Creating reviews...');

        $reviews = [
            ['customer' => 0, 'rating' => 5, 'comment' => 'Excellent food and service!'],
            ['customer' => 1, 'rating' => 4, 'comment' => 'Great taste, will visit again.'],
            ['customer' => 2, 'rating' => 5, 'comment' => 'Best biryani in town!'],
            ['customer' => 3, 'rating' => 3, 'comment' => 'Food was good, but delivery was slow.'],
            ['customer' => 4, 'rating' => 5, 'comment' => 'Amazing experience! Highly recommended.'],
        ];

        foreach ($reviews as $review) {
            Review::firstOrCreate(
                ['customer_name' => $customers[$review['customer']]->name],
                [
                    'customer_image' => null,
                    'rating' => $review['rating'],
                    'comment' => $review['comment'],
                    'is_active' => 1,
                ]
            );
        }
    }

    protected function createSettings()
    {
        $this->command->info('Creating settings...');

        $settings = [
            // General Settings
            ['key' => 'restaurant_name', 'value' => 'Test Restaurant', 'group' => 'general'],
            ['key' => 'restaurant_tagline', 'value' => 'Best Food in Town', 'group' => 'general'],
            ['key' => 'tax_percentage', 'value' => '5', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'BDT', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Asia/Dhaka', 'group' => 'general'],

            // Payment Settings
            ['key' => 'payment_bkash_enabled', 'value' => '1', 'group' => 'payment'],
            ['key' => 'payment_bkash_sandbox', 'value' => '1', 'group' => 'payment'],
            ['key' => 'payment_sslcommerze_enabled', 'value' => '1', 'group' => 'payment'],
            ['key' => 'payment_cash_enabled', 'value' => '1', 'group' => 'payment'],

            // Order Settings
            ['key' => 'order_prefix', 'value' => 'ORD', 'group' => 'order'],
            ['key' => 'auto_stock_deduction', 'value' => '1', 'group' => 'order'],
            ['key' => 'default_discount', 'value' => '10', 'group' => 'order'],

            // Marketing Settings
            ['key' => 'fb_pixel_id', 'value' => 'TEST_PIXEL_ID', 'group' => 'marketing'],
            ['key' => 'fb_test_event_code', 'value' => 'TEST123', 'group' => 'marketing'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type' => 'string',
                ]
            );
        }
    }

    protected function createWastages($ingredients, $menuItems, $users)
    {
        $this->command->info('Creating wastage records...');

        $wastages = [
            ['ingredient' => 1, 'quantity' => 2, 'reason' => 'Expired'],
            ['ingredient' => 7, 'quantity' => 1, 'reason' => 'Spoiled'],
            ['ingredient' => 9, 'quantity' => 3, 'reason' => 'Overcooked'],
        ];

        foreach ($wastages as $wastage) {
            Wastage::firstOrCreate(
                [
                    'ingredient_id' => $ingredients[$wastage['ingredient']]->id,
                    'date' => now()->subDays(rand(1, 7))->toDateString(),
                ],
                [
                    'quantity' => $wastage['quantity'],
                    'unit' => $ingredients[$wastage['ingredient']]->unit,
                    'reason' => $wastage['reason'],
                    'user_id' => $users[0]->id,
                ]
            );
        }
    }
}