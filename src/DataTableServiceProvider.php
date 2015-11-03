<?php

namespace Code4\DataTable;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider4 extends ServiceProvider {

    public function register() {

        $this->app['datatable'] = $this->app->share(
            function ($app) {
                $html = $app->make('Illuminate\Html\HtmlBuilder');
                return new DataTableFactory($html);
            }
        );
        $this->app['view']->addNamespace('DataTable', __DIR__ . '/../views');
        $this->registerAliases();
    }

    public function boot()
    {
        //$this->publishes([__DIR__ . '/../config/datatable.php' => base_path('config/datatable.php')], 'config');
        //$this->loadViewsFrom(__DIR__ . '/../views', 'DataTable');
    }


    private function registerAliases() {
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('DataTable', Facades\DataTable::class);
    }

}