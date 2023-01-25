<?php

namespace Location\Location\Laravel;

use Illuminate\Support\ServiceProvider as ServiceProviderBase;
use Location\Location\Interfaces\LocationInterface;
use Location\Location\Location;

class ServiceProvider extends ServiceProviderBase
{
    public function register(): void
    {
        $this->app->bind(LocationInterface::class, Location::class);
    }

    public function boot()
    {
    }
}
