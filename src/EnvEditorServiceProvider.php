<?php

namespace Jncinet\LaravelEnvEditor;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class EnvEditorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [EnvEditor::class, 'env-editor'];
    }

    public function register()
    {
        $this->app->singleton(EnvEditor::class, function ($app, $path = null) {
            return new EnvEditor($path);
        });

        $this->app->alias(EnvEditor::class, 'env-editor');
    }
}