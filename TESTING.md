# Testing Guide

This document provides instructions for running the test seeder and testing the restaurant management project.

## Quick Start

### 1. Run the Test Seeder

To populate the database with test data, run:

```bash
php artisan db:seed
```

Or to refresh and seed:

```bash
php artisan migrate:fresh --seed
```

### 2. Test Credentials

After running the seeder, you can log in with these test accounts:

| Role | Email | Password | Position |
|------|-------|----------|-----------|
| Super Admin | `admin@test.com` | `password` | General Manager |
| Manager | `manager@test.com` | `password` | Restaurant Manager |
| Cashier | `cashier@test.com` | `password` | Cashier |
| Waiter | `waiter@test.com` | `password` | Waiter |
| Chef | `chef@test.com` | `password` | Head Chef |

## Test Data Overview

The TestSeeder creates the following data:

### Users & Authentication
- 5 users with different roles (Super Admin, Manager, Cashier, Waiter, Chef)
- Roles and permissions configured

### Restaurant Setup
- 2 Outlets (Main Restaurant, Gulshan Branch)
- 10 Tables with various capacities
- 8 Menu Categories (Appetizers, Main Course, Biryani, Pizza, Burgers, Beverages, Desserts, Fast Food)

### Menu Items
- 24+ menu items with prices, tax rates, and preparation types
- Includes various cuisine types and price ranges

### Inventory Management
- 20 ingredients with stock levels and alert thresholds
- Recipes linking ingredients to menu items
- 5 suppliers for ingredient procurement

### Orders & Sales
- 7 test orders with various statuses (completed, pending, processing)
- Different order types: Dine-in, Takeaway, Delivery
- Order items with quantities and prices
- Payment records (cash, bKash, card)

### Other Data
- 8 customers with contact information
- 5 reservations (confirmed, pending, cancelled)
- 6 expense records (utilities, rent, maintenance, marketing)
- 5 reviews from customers
- 3 delivery partners
- Payroll records for all employees
- Wastage records for inventory tracking

## Testing Different Features

### 1. Admin Panel (Filament)

Access the admin panel at:
```
http://localhost/admin
```

**Test Admin Features:**
- View all orders
- Manage menu items and categories
- View inventory and stock levels
- Process purchases from suppliers
- Manage employees and payroll
- View reports and analytics
- Configure settings

### 2. POS (Point of Sale)

Access POS at:
```
http://localhost/pos
```

**Test POS Features:**
- Create new orders (dine-in, takeaway, delivery)
- Add menu items to orders
- Apply discounts
- Process payments (cash, bKash, card)
- View table statuses
- Manage order statuses

### 3. Customer Portal

Access customer pages at:
```
http://localhost/
```

**Test Customer Features:**
- View menu
- Make reservations
- Leave reviews

### 4. Landing Page

The public landing page includes:
- Menu display
- About section
- Contact information
- Social media links

## Testing Workflows

### Order Creation Flow
1. Login as Cashier or Waiter
2. Go to POS
3. Select order type (Dine-in/Takeaway/Delivery)
4. Add menu items with quantities
5. Apply discount if needed
6. Select payment method
7. Complete the order
8. Verify stock is deducted

### Inventory Management Flow
1. Login as Manager
2. Go to Ingredients
3. View current stock levels
4. Create purchase order
5. Add ingredients to purchase
6. Complete purchase
7. Verify inventory is updated

### Reservation Flow
1. Go to public reservation page
2. Fill reservation form
3. Submit reservation
4. Login as Manager
5. View and manage reservations

## API Testing

The application includes API endpoints. Test using:

```bash
# Login to get token
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}'
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/OrderTest.php

# Run with coverage
php artisan test --coverage
```

## Troubleshooting

### Common Issues

1. **Database not seeded**: Run `php artisan db:seed`
2. **Login not working**: Clear cache with `php artisan cache:clear`
3. **Permission errors**: Refresh permissions with `php artisan permission:cache:clear`
4. **Assets not loading**: Run `php artisan storage:link`

### Reset Test Data

To reset and reseed:

```bash
php artisan migrate:fresh --seed
```

## Project Structure

```
restaurantManagment/
├── app/
│   ├── Filament/          # Admin panel resources
│   ├── Http/              # Controllers and middleware
│   └── Models/            # Eloquent models
├── database/
│   ├── seeders/           # Database seeders
│   └── migrations/        # Database migrations
├── routes/                # Route definitions
├── resources/             # Views and assets
└── tests/                 # Test files
```

## Support

For issues or questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Filament Documentation: https://filamentphp.com/docs