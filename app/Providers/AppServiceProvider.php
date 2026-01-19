<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::morphMap([
            'OwnershipClaim' => \App\Models\OwnershipClaim::class,
            'Project' => \App\Models\Project::class,
            'BusinessLicense' => \App\Models\BusinessLicense::class,
            'Apartment' => \App\Models\Apartment::class,
            'Organization' => \App\Models\Organization::class,
            'ServiceRequest' => \App\Models\ServiceRequest::class,
        ]);
    }
}
