<?php

namespace Backtik\FilamentAiTranslator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAiTranslatorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-ai-translator';

    public static string $viewNamespace = 'filament-ai-translator';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile('ai-translator')
            ->hasViews(static::$viewNamespace)
            ->hasTranslations();
    }
}
