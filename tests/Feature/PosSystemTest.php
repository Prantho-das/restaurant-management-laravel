<?php

use App\Livewire\PosSystem;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\PremadeStock;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can add items to cart and place an order', function () {
    $user = User::factory()->create();
    $outlet = Outlet::factory()->create();
    $category = Category::factory()->create(['name' => 'Royal Selection']);
    $item = MenuItem::factory()->create([
        'category_id' => $category->id,
        'outlet_id' => $outlet->id,
        'name' => 'Biryani',
        'base_price' => 500,
        'is_active' => true,
    ]);

    Livewire::actingAs($user)
        ->test(PosSystem::class)
        ->set('orderType', 'dine_in')
        ->set('tableNumber', '5')
        ->call('addToCart', $item->id)
        ->assertSet('cart.0.id', $item->id)
        ->call('placeOrder');

    expect(Order::count())->toBe(1);
    $order = Order::first();
    expect($order->order_type)->toBe('dine_in');
    expect($order->table_number)->toBe('5');
    expect((float) $order->total_amount)->toBe(500.0);
});

it('prevents adding items when stock is insufficient', function () {
    $user = User::factory()->create();
    $outlet = Outlet::factory()->create();
    $category = Category::factory()->create();

    // Create an item with a recipe that has an ingredient with 0 stock
    $ingredient = Ingredient::factory()->create(['current_stock' => 0]);
    $item = MenuItem::factory()->create([
        'category_id' => $category->id,
        'outlet_id' => $outlet->id,
        'name' => 'Out of Stock Item',
        'base_price' => 100,
    ]);

    Recipe::create([
        'menu_item_id' => $item->id,
        'ingredient_id' => $ingredient->id,
        'quantity' => 1,
    ]);

    Livewire::actingAs($user)
        ->test(PosSystem::class)
        ->call('addToCart', $item->id)
        ->assertCount('cart', 0);
});

it('uses premade prepared quantity when adding premade item to cart', function () {
    $user = User::factory()->create();
    $outlet = Outlet::factory()->create();
    $category = Category::factory()->create();

    $item = MenuItem::factory()->create([
        'category_id' => $category->id,
        'outlet_id' => $outlet->id,
        'name' => 'Prepared Sandwich',
        'base_price' => 150,
        'preparation_type' => 'premade',
        'is_active' => true,
    ]);

    PremadeStock::create([
        'menu_item_id' => $item->id,
        'available_quantity' => 2,
    ]);

    $test = Livewire::actingAs($user)
        ->test(PosSystem::class)
        ->call('addToCart', $item->id)
        ->call('addToCart', $item->id);

    $test->assertSet('cart.0.quantity', 2)
        ->call('addToCart', $item->id)
        ->assertSet('cart.0.quantity', 2);
});

it('deducts premade stock when a completed order is placed from pos', function () {
    $user = User::factory()->create();
    $outlet = Outlet::factory()->create();
    $category = Category::factory()->create();

    $item = MenuItem::factory()->create([
        'category_id' => $category->id,
        'outlet_id' => $outlet->id,
        'name' => 'Prepared Roll',
        'base_price' => 120,
        'preparation_type' => 'premade',
        'is_active' => true,
    ]);

    PremadeStock::create([
        'menu_item_id' => $item->id,
        'available_quantity' => 5,
    ]);

    Livewire::actingAs($user)
        ->test(PosSystem::class)
        ->call('addToCart', $item->id)
        ->call('addToCart', $item->id)
        ->call('placeOrder');

    expect((float) PremadeStock::where('menu_item_id', $item->id)->value('available_quantity'))->toBe(3.0);
});
