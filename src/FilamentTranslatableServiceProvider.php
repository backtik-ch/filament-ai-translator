<?php

namespace Backtik\FilamentTranslatable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTranslatableServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translatable';

    public static string $viewNamespace = 'filament-translatable';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile('filament-translatable')
            ->hasViews(static::$viewNamespace)
            ->hasTranslations();
    }
}
