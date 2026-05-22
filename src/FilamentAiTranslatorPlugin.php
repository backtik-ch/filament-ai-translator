<?php

namespace Backtik\FilamentAiTranslator;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAiTranslatorPlugin implements Plugin
{
    protected array | Closure | null $languages = null;

    protected string | Closure | null $sourceLocale = null;

    protected string | Closure | null $aiProvider = null;

    protected string | Closure | null $aiModel = null;

    public function getId(): string
    {
        return 'filament-ai-translator';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        if ($this->languages !== null) {
            config()->set('ai-translator.languages', value($this->languages));
        }

        if ($this->sourceLocale !== null) {
            config()->set('ai-translator.source_locale', value($this->sourceLocale));
        }

        if ($this->aiProvider !== null) {
            config()->set('ai-translator.ai.provider', value($this->aiProvider));
        }

        if ($this->aiModel !== null) {
            config()->set('ai-translator.ai.model', value($this->aiModel));
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function languages(array | Closure $languages): static
    {
        $this->languages = $languages;

        return $this;
    }

    public function sourceLocale(string | Closure $locale): static
    {
        $this->sourceLocale = $locale;

        return $this;
    }

    public function aiProvider(string | Closure $provider): static
    {
        $this->aiProvider = $provider;

        return $this;
    }

    public function aiModel(string | Closure $model): static
    {
        $this->aiModel = $model;

        return $this;
    }
}
