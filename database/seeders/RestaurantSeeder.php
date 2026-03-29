<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\InventoryLog;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Payroll;
use App\Models\Reservation;
use App\Models\Supplier;
use App\Models\Table as RestaurantTable;
use App\Models\User;
use App\Models\Wastage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive restaurant data.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@royaldine.com'],
            [
                'name' => 'Rafiq Ahmed',
                'password' => Hash::make('password'),
                'position' => 'General Manager',
                'base_salary' => 85000,
                'join_date' => '2020-01-15',
                'status' => 1,
            ]
        );

        // 2. Create Staff Users
        $staffUsers = [
            [
                'name' => 'Karim Hassan',
                'email' => 'karim@royaldine.com',
                'password' => Hash::make('password'),
                'position' => 'Head Chef',
                'base_salary' => 55000,
                'join_date' => '2021-03-10',
                'status' => 1,
            ],
            [
                'name' => 'Rahim Uddin',
                'email' => 'rahim@royaldine.com',
                'password' => Hash::make('password'),
                'position' => 'Sous Chef',
                'base_salary' => 40000,
                'join_date' => '2021-06-20',
                'status' => 1,
            ],
            [
                'name' => 'Salam Khan',
                'email' => 'salam@royaldine.com',
                'password' => Hash::make('password'),
                'position' => 'Senior Waiter',
                'base_salary' => 25000,
                'join_date' => '2022-01-05',
                'status' => 1,
            ],
            [
                'name' => 'Babul Mia',
                'email' => 'babul@royaldine.com',
                'password' => Hash::make('password'),
                'position' => 'Waiter',
                'base_salary' => 20000,
                'join_date' => '2022-08-15',
                'status' => 1,
            ],
            [
                'name' => 'Jamal Hossain',
                'email' => 'jamal@royaldine.com',
                'password' => Hash::make('password'),
                'position' => 'Kitchen Assistant',
                'base_salary' => 18000,
                'join_date' => '2023-02-01',
                'status' => 1,
            ],
        ];

        $createdUsers = [$admin];
        foreach ($staffUsers as $userData) {
            $createdUsers[] = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // 3. Create Outlets
        $outlets = [
            [
                'name' => 'Royal Dine - Banani',
                'address' => 'House 12, Road 5, Block F, Banani, Dhaka 1213',
                'phone' => '01712345678',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
                'is_active' => 1,
            ],
            [
                'name' => 'Royal Dine - Gulshan',
                'address' => 'House 45, Road 11, Block G, Gulshan-2, Dhaka 1212',
                'phone' => '01712345679',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
                'is_active' => 1,
            ],
            [
                'name' => 'Royal Dine - Dhanmondi',
                'address' => 'Road 8, Dhanmondi 32, Dhaka 1209',
                'phone' => '01712345680',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'BDT',
                'is_active' => 1,
            ],
        ];

        $createdOutlets = [];
        foreach ($outlets as $outletData) {
            $createdOutlets[] = Outlet::firstOrCreate(
                ['name' => $outletData['name']],
                $outletData
            );
        }

        // 4. Create Tables for Banani outlet
        $bananiTables = [
            ['name' => 'Table 1', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 2', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Table 3', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 4', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Table 5', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'VIP Room 1', 'capacity' => 10, 'status' => 'available'],
            ['name' => 'VIP Room 2', 'capacity' => 12, 'status' => 'available'],
            ['name' => 'Outdoor 1', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Outdoor 2', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Bar Counter', 'capacity' => 6, 'status' => 'available'],
        ];

        $createdTables = [];
        foreach ($bananiTables as $tableData) {
            $createdTables[] = RestaurantTable::firstOrCreate(
                ['name' => $tableData['name'], 'outlet_id' => $createdOutlets[0]->id],
                array_merge($tableData, ['outlet_id' => $createdOutlets[0]->id, 'slug' => Str::slug($tableData['name'])])
            );
        }

        // 5. Create Categories
        $categories = [
            ['name' => 'Royal Biryani', 'slug' => 'royal-biryani', 'priority_order' => 1],
            ['name' => 'Heritage Curries', 'slug' => 'heritage-curries', 'priority_order' => 2],
            ['name' => 'Artisan Snacks', 'slug' => 'artisan-snacks', 'priority_order' => 3],
            ['name' => 'Sweet Traditions', 'slug' => 'sweet-traditions', 'priority_order' => 4],
            ['name' => 'Beverages', 'slug' => 'beverages', 'priority_order' => 5],
            ['name' => 'Starters', 'slug' => 'starters', 'priority_order' => 6],
            ['name' => 'Tandoor Specials', 'slug' => 'tandoor-specials', 'priority_order' => 7],
            ['name' => 'Desserts', 'slug' => 'desserts', 'priority_order' => 8],
        ];

        $createdCategories = [];
        foreach ($categories as $catData) {
            $createdCategories[] = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                array_merge($catData, ['is_active' => 1])
            );
        }

        // 6. Create Menu Items
        $menuItems = [
            // Royal Biryani
            [
                'category_id' => $createdCategories[0]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Shahi Mutton Kacchi',
                'slug' => 'shahi-mutton-kacchi',
                'description' => 'Old Dhaka style slow-cooked mutton with saffron infused basmati rice',
                'base_price' => 950,
                'tax_rate' => 5,
                'sku' => 'RB-001',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Chicken Tehari',
                'slug' => 'chicken-tehari',
                'description' => 'Traditional Bengali tehari with tender chicken pieces',
                'base_price' => 450,
                'tax_rate' => 5,
                'sku' => 'RB-002',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Special Beef Biryani',
                'slug' => 'special-beef-biryani',
                'description' => 'Fragrant rice with succulent beef pieces and aromatic spices',
                'base_price' => 850,
                'tax_rate' => 5,
                'sku' => 'RB-003',
                'is_active' => 1,
            ],
            // Heritage Curries
            [
                'category_id' => $createdCategories[1]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Heritage Beef Bhuna',
                'slug' => 'heritage-beef-bhuna',
                'description' => 'A robust, slow-simmered beef curry with caramelized onions and hand-ground spices',
                'base_price' => 720,
                'tax_rate' => 5,
                'sku' => 'HC-001',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[1]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Chittagong Mezban',
                'slug' => 'chittagong-mezban',
                'description' => 'Spicy and authentic slow-cooked beef with ground whole spices',
                'base_price' => 850,
                'tax_rate' => 5,
                'sku' => 'HC-002',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[1]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Mutton Kosha',
                'slug' => 'mutton-kosha',
                'description' => 'Rich and spicy mutton curry with potato pieces',
                'base_price' => 780,
                'tax_rate' => 5,
                'sku' => 'HC-003',
                'is_active' => 1,
            ],
            // Artisan Snacks
            [
                'category_id' => $createdCategories[2]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Boutique Fuchka',
                'slug' => 'boutique-fuchka',
                'description' => 'Artisan street-snack serve with premium tamarind infusion',
                'base_price' => 180,
                'tax_rate' => 5,
                'sku' => 'AS-001',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[2]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Samosa Platter',
                'slug' => 'samosa-platter',
                'description' => 'Crispy samosas served with tamarind chutney and green chutney',
                'base_price' => 250,
                'tax_rate' => 5,
                'sku' => 'AS-002',
                'is_active' => 1,
            ],
            // Beverages
            [
                'category_id' => $createdCategories[4]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Fresh Lime Soda',
                'slug' => 'fresh-lime-soda',
                'description' => 'Refreshing lime with soda and a hint of black salt',
                'base_price' => 80,
                'tax_rate' => 5,
                'sku' => 'BV-001',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[4]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Mango Lassi',
                'slug' => 'mango-lassi',
                'description' => 'Creamy yogurt-based mango smoothie',
                'base_price' => 150,
                'tax_rate' => 5,
                'sku' => 'BV-002',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[4]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Masala Chai',
                'slug' => 'masala-chai',
                'description' => 'Traditional spiced tea with cardamom and ginger',
                'base_price' => 60,
                'tax_rate' => 5,
                'sku' => 'BV-003',
                'is_active' => 1,
            ],
            // Desserts
            [
                'category_id' => $createdCategories[7]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Rossogolla',
                'slug' => 'rossogolla',
                'description' => 'Soft and spongy Bengali cottage cheese balls in sugar syrup',
                'base_price' => 120,
                'tax_rate' => 5,
                'sku' => 'DS-001',
                'is_active' => 1,
            ],
            [
                'category_id' => $createdCategories[7]->id,
                'outlet_id' => $createdOutlets[0]->id,
                'name' => 'Mishti Doi',
                'slug' => 'mishti-doi',
                'description' => 'Sweet fermented yogurt with caramelized sugar',
                'base_price' => 100,
                'tax_rate' => 5,
                'sku' => 'DS-002',
                'is_active' => 1,
            ],
        ];

        $createdMenuItems = [];
        foreach ($menuItems as $itemData) {
            $createdMenuItems[] = MenuItem::firstOrCreate(
                ['slug' => $itemData['slug']],
                $itemData
            );
        }

        // 7. Create Ingredients
        $ingredients = [
            ['name' => 'Basmati Rice', 'unit' => 'kg', 'current_stock' => 50, 'alert_threshold' => 10],
            ['name' => 'Mutton', 'unit' => 'kg', 'current_stock' => 15, 'alert_threshold' => 5],
            ['name' => 'Beef', 'unit' => 'kg', 'current_stock' => 20, 'alert_threshold' => 5],
            ['name' => 'Chicken', 'unit' => 'kg', 'current_stock' => 25, 'alert_threshold' => 8],
            ['name' => 'Onions', 'unit' => 'kg', 'current_stock' => 30, 'alert_threshold' => 10],
            ['name' => 'Garlic', 'unit' => 'kg', 'current_stock' => 5, 'alert_threshold' => 2],
            ['name' => 'Ginger', 'unit' => 'kg', 'current_stock' => 4, 'alert_threshold' => 1],
            ['name' => 'Turmeric Powder', 'unit' => 'kg', 'current_stock' => 3, 'alert_threshold' => 1],
            ['name' => 'Red Chili Powder', 'unit' => 'kg', 'current_stock' => 2, 'alert_threshold' => 0.5],
            ['name' => 'Cumin Seeds', 'unit' => 'kg', 'current_stock' => 2, 'alert_threshold' => 0.5],
            ['name' => 'Ghee', 'unit' => 'kg', 'current_stock' => 8, 'alert_threshold' => 2],
            ['name' => 'Vegetable Oil', 'unit' => 'liter', 'current_stock' => 20, 'alert_threshold' => 5],
            ['name' => 'Yogurt', 'unit' => 'kg', 'current_stock' => 10, 'alert_threshold' => 3],
            ['name' => 'Milk', 'unit' => 'liter', 'current_stock' => 15, 'alert_threshold' => 5],
            ['name' => 'Saffron', 'unit' => 'gram', 'current_stock' => 50, 'alert_threshold' => 10],
            ['name' => 'Bay Leaves', 'unit' => 'kg', 'current_stock' => 1, 'alert_threshold' => 0.2],
            ['name' => 'Green Chili', 'unit' => 'kg', 'current_stock' => 3, 'alert_threshold' => 1],
            ['name' => 'Coriander Leaves', 'unit' => 'kg', 'current_stock' => 2, 'alert_threshold' => 0.5],
            ['name' => 'Lemon', 'unit' => 'kg', 'current_stock' => 5, 'alert_threshold' => 2],
            ['name' => 'Sugar', 'unit' => 'kg', 'current_stock' => 15, 'alert_threshold' => 5],
        ];

        $createdIngredients = [];
        foreach ($ingredients as $ingData) {
            $createdIngredients[] = Ingredient::firstOrCreate(
                ['name' => $ingData['name']],
                $ingData
            );
        }

        // 8. Create Suppliers
        $suppliers = [
            [
                'name' => 'Fresh Meat Suppliers Ltd.',
                'contact_person' => 'Abdul Karim',
                'phone' => '01812345601',
                'email' => 'abdul@freshmeat.com',
                'address' => 'Mymensingh Road, Dhaka',
                'is_active' => 1,
            ],
            [
                'name' => 'Dhaka Rice & Spices Co.',
                'contact_person' => 'Mohammad Hasan',
                'phone' => '01812345602',
                'email' => 'hasan@ricespices.com',
                'address' => 'Mohammadpur, Dhaka',
                'is_active' => 1,
            ],
            [
                'name' => 'Green Vegetables Market',
                'contact_person' => 'Rahim Uddin',
                'phone' => '01812345603',
                'email' => 'rahim@greenveg.com',
                'address' => 'Kawran Bazar, Dhaka',
                'is_active' => 1,
            ],
            [
                'name' => 'Premium Dairy Products',
                'contact_person' => 'Fatema Begum',
                'phone' => '01812345604',
                'email' => 'fatema@premiumdairy.com',
                'address' => 'Uttara, Dhaka',
                'is_active' => 1,
            ],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $supData) {
            $createdSuppliers[] = Supplier::firstOrCreate(
                ['name' => $supData['name']],
                $supData
            );
        }

        // 9. Create Customers
        $customers = [
            ['name' => 'Tanvir Ahmed', 'phone' => '01512345601', 'email' => 'tanvir@gmail.com', 'address' => 'Gulshan, Dhaka'],
            ['name' => 'Sadia Rahman', 'phone' => '01512345602', 'email' => 'sadia@yahoo.com', 'address' => 'Banani, Dhaka'],
            ['name' => 'Mahmud Hasan', 'phone' => '01512345603', 'email' => 'mahmud@gmail.com', 'address' => 'Dhanmondi, Dhaka'],
            ['name' => 'Nusrat Jahan', 'phone' => '01512345604', 'email' => 'nusrat@hotmail.com', 'address' => 'Mirpur, Dhaka'],
            ['name' => 'Kamal Uddin', 'phone' => '01512345605', 'email' => 'kamal@gmail.com', 'address' => 'Uttara, Dhaka'],
            ['name' => 'Fatema Begum', 'phone' => '01512345606', 'email' => 'fatema@gmail.com', 'address' => 'Mohammadpur, Dhaka'],
            ['name' => 'Rahim Khan', 'phone' => '01512345607', 'email' => 'rahimk@gmail.com', 'address' => 'Bashundhara, Dhaka'],
            ['name' => 'Jakia Akter', 'phone' => '01512345608', 'email' => 'jaki@yahoo.com', 'address' => 'Nikunja, Dhaka'],
        ];

        $createdCustomers = [];
        foreach ($customers as $custData) {
            $createdCustomers[] = Customer::firstOrCreate(
                ['phone' => $custData['phone']],
                $custData
            );
        }

        // 10. Create Orders with Order Items
        $orderStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'];
        $paymentMethods = ['cash', 'card', 'mobile_banking', 'online'];
        $orderTypes = ['dine_in', 'takeaway', 'delivery'];

        for ($i = 1; $i <= 15; $i++) {
            $orderType = $orderTypes[array_rand($orderTypes)];
            $status = $i <= 12 ? $orderStatuses[array_rand(['preparing', 'ready', 'served', 'completed'])] : $orderStatuses[array_rand($orderStatuses)];
            $subtotal = rand(500, 3000);
            $discount = rand(0, 1) ? ($subtotal * 0.1) : 0;
            $total = $subtotal - $discount;

            $order = Order::create([
                'order_number' => 'ORD-'.now()->timestamp.'-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => $status,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'discount_type' => $discount > 0 ? 'percentage' : 'none',
                'total_amount' => $total,
                'order_type' => $orderType,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'customer_name' => $createdCustomers[array_rand($createdCustomers)]->name,
                'customer_phone' => $createdCustomers[array_rand($createdCustomers)]->phone,
                'table_number' => $orderType === 'dine_in' ? $createdTables[array_rand($createdTables)]->name : null,
                'guest_count' => rand(1, 6),
                'notes' => $i % 3 === 0 ? 'Extra spicy please' : null,
                'user_id' => $createdUsers[array_rand($createdUsers)]->id,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 12)),
            ]);

            // Add 1-4 items per order
            $itemCount = rand(1, 4);
            $selectedIndices = array_rand($createdMenuItems, min($itemCount, count($createdMenuItems)));

            foreach ((array) $selectedIndices as $index) {
                $menuItem = $createdMenuItems[$index];
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => rand(1, 3),
                    'price' => $menuItem->base_price,
                ]);
            }
        }

        // 11. Create Inventory Logs
        for ($i = 0; $i < 20; $i++) {
            InventoryLog::create([
                'ingredient_id' => $createdIngredients[array_rand($createdIngredients)]->id,
                'type' => rand(0, 1) ? 'add' : 'remove',
                'quantity' => rand(1, 10),
                'note' => rand(0, 1) ? 'Supplier delivery' : 'Used in kitchen',
                'user_id' => $createdUsers[array_rand($createdUsers)]->id,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // 12. Create Expenses
        $expenseCategories = ['rent', 'utilities', 'supplies', 'maintenance', 'marketing', 'salaries', 'insurance', 'miscellaneous'];
        $expenseTitles = [
            'rent' => 'Monthly Outlet Rent - Banani',
            'utilities' => 'Electricity Bill',
            'supplies' => 'Kitchen Supplies Purchase',
            'maintenance' => 'AC Servicing',
            'marketing' => 'Facebook Advertisement',
            'salaries' => 'Staff Salaries - March',
            'insurance' => 'Business Insurance Premium',
            'miscellaneous' => 'Office Stationery',
        ];

        for ($i = 0; $i < 15; $i++) {
            $category = $expenseCategories[array_rand($expenseCategories)];
            Expense::create([
                'category' => $category,
                'title' => $expenseTitles[$category].' - '.($i + 1),
                'description' => 'Payment for '.strtolower($expenseTitles[$category]),
                'amount' => rand(1000, 50000),
                'date' => now()->subDays(rand(0, 60)),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'reference_no' => 'EXP-'.now()->timestamp.'-'.str_pad(($i + 1), 4, '0', STR_PAD_LEFT),
                'user_id' => $admin->id,
                'created_at' => now()->subDays(rand(0, 60)),
            ]);
        }

        // 13. Create Payrolls for staff
        foreach ($createdUsers as $user) {
            if ($user->id !== $admin->id) {
                for ($month = 1; $month <= 3; $month++) {
                    $baseSalary = $user->base_salary;
                    $bonus = rand(0, 1) ? rand(500, 3000) : 0;
                    $deduction = rand(0, 1) ? rand(100, 500) : 0;
                    Payroll::create([
                        'user_id' => $user->id,
                        'payment_date' => now()->subMonths($month)->endOfMonth()->format('Y-m-d'),
                        'month' => now()->subMonths($month)->format('F'),
                        'year' => now()->subMonths($month)->format('Y'),
                        'base_salary' => $baseSalary,
                        'bonus_amount' => $bonus,
                        'deduction_amount' => $deduction,
                        'net_paid' => $baseSalary + $bonus - $deduction,
                        'payment_method' => $paymentMethods[array_rand(['cash', 'bank_transfer'])],
                        'status' => 'paid',
                        'notes' => 'Monthly salary for '.now()->subMonths($month)->format('F Y'),
                        'created_at' => now()->subMonths($month)->endOfMonth(),
                    ]);
                }
            }
        }

        // 14. Create Reservations
        $reservationStatuses = ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'];
        $arrangements = ['birthday', 'anniversary', 'business', 'family_gathering', 'casual', 'date_night'];

        for ($i = 0; $i < 10; $i++) {
            $date = now()->addDays(rand(-7, 30));
            Reservation::create([
                'name' => $createdCustomers[array_rand($createdCustomers)]->name,
                'phone' => $createdCustomers[array_rand($createdCustomers)]->phone,
                'date' => $date->format('Y-m-d'),
                'guests' => rand(2, 10),
                'arrangement' => $arrangements[array_rand($arrangements)],
                'notes' => $i % 2 === 0 ? 'Please arrange a corner table' : null,
                'status' => $reservationStatuses[array_rand($reservationStatuses)],
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // 15. Create Wastages
        $wastageReasons = ['expired', 'damaged', 'preparation_waste', 'customer_return', 'quality_issue'];

        for ($i = 0; $i < 8; $i++) {
            Wastage::create([
                'ingredient_id' => $createdIngredients[array_rand($createdIngredients)]->id,
                'menu_item_id' => $createdMenuItems[array_rand($createdMenuItems)]->id,
                'quantity' => rand(0.5, 5),
                'unit' => 'kg',
                'reason' => $wastageReasons[array_rand($wastageReasons)],
                'date' => now()->subDays(rand(0, 30)),
                'estimated_cost' => rand(100, 1000),
                'notes' => $i % 2 === 0 ? 'Logged for inventory adjustment' : null,
                'user_id' => $createdUsers[array_rand($createdUsers)]->id,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Restaurant seeder completed successfully!');
    }
}
