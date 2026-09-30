<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Sidebar badge: opportunités apporteurs en attente d'arbitrage (doc §32)
        View::composer('layouts.admin', function ($view) {
            $count = 0;
            if (auth()->check() && auth()->user()->hasPermission('opportunities')) {
                $count = \App\Models\PartnerProspect::opportunities()->where('duplicate_status', 'flagged')->count();
            }
            $view->with('flaggedOpportunitiesCount', $count);

            // Sidebar badge : demandes de retrait des portefeuilles pro à traiter (doc §37-38)
            $withdrawals = 0;
            if (auth()->check() && auth()->user()->hasPermission('finance')) {
                $withdrawals = \App\Models\WithdrawalRequest::whereIn('status', ['requested', 'approved'])->count();
            }
            $view->with('pendingWithdrawalsCount', $withdrawals);

            // Sidebar badge : relances commerciales en retard (doc §43)
            $late = 0;
            if (auth()->check() && auth()->user()->hasPermission('crm')) {
                $late = \App\Models\CrmDeal::whereNotNull('next_action_at')->where('next_action_at', '<', now())
                    ->whereNotIn('stage', ['lost', 'loyalty'])->count();
            }
            $view->with('lateFollowUpsCount', $late);
        });
    }
}
