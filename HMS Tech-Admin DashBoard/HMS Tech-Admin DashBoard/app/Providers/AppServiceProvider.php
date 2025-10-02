<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Salary;
use App\Observers\SalaryObserver;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();
        Relation::enforceMorphMap([
        // keys must match khata_accounts.party_type values
        'clients'              => \App\Models\Client::class,
        'partners'             => \App\Models\Partner::class,
        'developers'           => \App\Models\Developer::class,
        'team_managers'        => \App\Models\TeamManager::class,
        'business_developers'  => \App\Models\BusinessDeveloper::class,

        // manual/other aren't linked; mapping not used but okay to keep type values consistent
        'manual'               => \App\Models\KhataAccount::class,
        'other'                => \App\Models\KhataAccount::class,
    ]);
    Salary::observe(SalaryObserver::class);
    }
}