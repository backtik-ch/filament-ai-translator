<?php

namespace Backtik\FilamentAiTranslator\Infolists\Components;

use Filament\Infolists\Components\Entry;

class TranslatableEntry extends Entry
{
    protected string $view = 'filament-ai-translator::infolists.components.translatable-entry';

    public function getLanguages(): array
    {
        return config('ai-translator.languages', []);
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
