<?php

namespace Backtik\FilamentTranslatable\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;

class TranslatableEntry extends Entry
{
    protected string $view = 'filament-translatable::infolists.components.translatable-entry';

    protected bool | Closure $isModal = false;

    protected int | Closure | null $lineClamp = null;

    public function modal(bool | Closure $condition = true): static
    {
        $this->isModal = $condition;

        return $this;
    }

    public function lineClamp(int | Closure | null $lines): static
    {
        $this->lineClamp = $lines;

        return $this;
    }

    public function getLineClamp(): ?int
    {
        return $this->evaluate($this->lineClamp);
    }

    public function isModal(): bool
    {
        return (bool) $this->evaluate($this->isModal);
    }

    public function getSourceLocale(): string
    {
        return config('filament-translatable.source_locale', 'fr');
    }

    public function getLanguages(): array
    {
        return config('filament-translatable.languages', []);
    }

    public function getTranslations(): array
    {
        $record = $this->getRecord();
        $name = $this->getName();

        if ($record && method_exists($record, 'getTranslations')) {
            return $record->getTranslations($name);
        }

        $state = $this->getState();

        if (is_string($state)) {
            $state = json_decode($state, true);
        }

        return is_array($state) ? $state : [];
    }
}
