<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Wastage;
use App\Observers\OrderObserver;
use App\Observers\PurchaseItemObserver;
use App\Observers\PurchaseObserver;
use App\Observers\WastageObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helperPath = app_path('Helpers/report_helpers.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Order::observe(OrderObserver::class);
        Purchase::observe(PurchaseObserver::class);
        PurchaseItem::observe(PurchaseItemObserver::class);
        Wastage::observe(WastageObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
