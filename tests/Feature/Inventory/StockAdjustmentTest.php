<?php

use App\Models\Ingredient;
use App\Models\InventoryLog;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can process stock adjustment addition', function () {
    $user = User::factory()->create();
    $ingredient = Ingredient::factory()->create(['current_stock' => 10]);

    $adjustment = StockAdjustment::create([
        'reference_no' => 'SA-001',
        'adjustment_date' => now(),
        'status' => 'draft',
        'user_id' => $user->id,
    ]);

    StockAdjustmentItem::create([
        'stock_adjustment_id' => $adjustment->id,
        'ingredient_id' => $ingredient->id,
        'type' => 'addition',
        'quantity' => 5,
    ]);

    $service = app(InventoryService::class);
    $service->processStockAdjustment($adjustment);

    $ingredient->refresh();
    expect((float) $ingredient->current_stock)->toEqual(15.0);

    $log = InventoryLog::where('ingredient_id', $ingredient->id)->first();
    expect($log)->not->toBeNull()
        ->and($log->type)->toBe('stock_adjustment')
        ->and((float) $log->quantity)->toEqual(5.0);
});

test('it can process stock adjustment subtraction', function () {
    $user = User::factory()->create();
    $ingredient = Ingredient::factory()->create(['current_stock' => 10]);

    $adjustment = StockAdjustment::create([
        'reference_no' => 'SA-002',
        'adjustment_date' => now(),
        'status' => 'draft',
        'user_id' => $user->id,
    ]);

    StockAdjustmentItem::create([
        'stock_adjustment_id' => $adjustment->id,
        'ingredient_id' => $ingredient->id,
        'type' => 'subtraction',
        'quantity' => 3,
    ]);

    $service = app(InventoryService::class);
    $service->processStockAdjustment($adjustment);

    $ingredient->refresh();
    expect((float) $ingredient->current_stock)->toEqual(7.0);
});

test('it can process multiple items in one adjustment', function () {
    $user = User::factory()->create();
    $ingredient1 = Ingredient::factory()->create(['current_stock' => 10]);
    $ingredient2 = Ingredient::factory()->create(['current_stock' => 20]);

    $adjustment = StockAdjustment::create([
        'reference_no' => 'SA-003',
        'adjustment_date' => now(),
        'status' => 'draft',
        'user_id' => $user->id,
    ]);

    StockAdjustmentItem::create([
        'stock_adjustment_id' => $adjustment->id,
        'ingredient_id' => $ingredient1->id,
        'type' => 'addition',
        'quantity' => 10,
    ]);

    StockAdjustmentItem::create([
        'stock_adjustment_id' => $adjustment->id,
        'ingredient_id' => $ingredient2->id,
        'type' => 'subtraction',
        'quantity' => 5,
    ]);

    $service = app(InventoryService::class);
    $service->processStockAdjustment($adjustment);

    expect((float) $ingredient1->refresh()->current_stock)->toEqual(20.0);
    expect((float) $ingredient2->refresh()->current_stock)->toEqual(15.0);
});
