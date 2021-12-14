<?php

namespace Buxuhunao\CloudInfinite;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected bool $defer = true;

    public function register()
    {
        $this->app->singleton(InfiniteClient::class, function () {
            return new InfiniteClient(config('filesystems.disks.cos'));
        });
    }

    public function provides()
    {
        return [InfiniteClient::class];
    }
}
