<?php

namespace app\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $models = array(
            'User',
            'Shop',
            'Branch',
            'Department',
            'Position',
            'Shift',
            'ClockHistory',
            'UserShift',
            'Nguoidung',
            'Phong'
        );

        foreach ($models as $model) {
            $this->app->bind("App\\Api\\Repositories\\Contracts\\{$model}Repository", "App\\Api\\Repositories\\Eloquent\\{$model}RepositoryEloquent");
        }
    }
}
