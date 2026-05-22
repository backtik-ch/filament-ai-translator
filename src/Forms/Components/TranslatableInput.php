<?php

namespace Backtik\FilamentAiTranslator\Forms\Components;

use Backtik\FilamentAiTranslator\Services\AiTranslator;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class TranslatableInput extends Field
{
    protected string $view = 'filament-ai-translator::forms.components.translatable-input';

    protected string | Closure $inputType = 'text';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(function (self $component, $state): void {
            if (is_string($state)) {
                $decoded = json_decode($state, true);
                $component->state(is_array($decoded) ? $decoded : []);
            } elseif (! is_array($state)) {
                $component->state([]);
            }
        });

        $this->dehydrateStateUsing(function ($state) {
            return is_array($state) ? $state : [];
        });

        $this->registerActions([
            $this->getTranslateAction(),
        ]);
    }

    public function inputType(string | Closure $type): static
    {
        $this->inputType = $type;

        return $this;
    }

    public function getInputType(): string
    {
        return $this->evaluate($this->inputType);
    }

    public function getLanguages(): array
    {
        return config('ai-translator.languages', []);
    }

    public function getSourceLocale(): string
    {
        return config('ai-translator.source_locale', 'fr');
    }

    public function getDisplayValue(): string
    {
        $state = $this->getState();

        if (! is_array($state)) {
            return '';
        }

        return $state[$this->getSourceLocale()] ?? '';
    }

    public function getTranslateAction(): Action
    {
        return Action::make('translate')
            ->label(__('filament-ai-translator::translations.translate'))
            ->icon('heroicon-o-language')
            ->modalHeading(__('filament-ai-translator::translations.modal_heading'))
            ->modalWidth('xl')
            ->fillForm(function (): array {
                $state = $this->getState();

                if (! is_array($state)) {
                    $state = [];
                }

                $data = [];
                foreach ($this->getLanguages() as $locale) {
                    $data[$locale] = $state[$locale] ?? '';
                }

                $data['source_locale'] = $this->getSourceLocale();

                return $data;
            })
            ->schema(function (): array {
                $fields = [];

                $fields[] = Select::make('source_locale')
                    ->label(__('filament-ai-translator::translations.source_language'))
                    ->options(array_combine($this->getLanguages(), array_map('strtoupper', $this->getLanguages())))
                    ->default($this->getSourceLocale())
                    ->required();

                foreach ($this->getLanguages() as $locale) {
                    $field = $this->getInputType() === 'textarea'
                        ? Textarea::make($locale)->label(strtoupper($locale))->rows(3)
                        : TextInput::make($locale)->label(strtoupper($locale));

                    $fields[] = $field;
                }

                return $fields;
            })
            ->extraModalFooterActions(fn (): array => [
                Action::make('generate')
                    ->label(__('filament-ai-translator::translations.generate'))
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->action(function (Action $action, self $component) {
                        $livewire = $action->getLivewire();
                        $parentIndex = $action->getParentAction()->getNestingIndex();
                        $data = $livewire->mountedActions[$parentIndex]['data'] ?? [];

                        $sourceLocale = $data['source_locale'] ?? $component->getSourceLocale();
                        $sourceText = $data[$sourceLocale] ?? '';

                        if (trim($sourceText) === '') {
                            Notification::make()
                                ->title(__('filament-ai-translator::translations.empty_source'))
                                ->danger()
                                ->send();

                            return;
                        }

                        $targetLocales = array_filter(
                            $component->getLanguages(),
                            fn (string $locale) => $locale !== $sourceLocale,
                        );

                        try {
                            $translator = app(AiTranslator::class);
                            $translations = $translator->translateToAll($sourceText, $sourceLocale, $targetLocales);

                            $formData = $data;
                            foreach ($translations as $locale => $translation) {
                                $formData[$locale] = $translation;
                            }

                            $livewire->mountedActions[$parentIndex]['data'] = $formData;
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title(__('filament-ai-translator::translations.error'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->action(function (array $data, self $component): void {
                $state = [];

                foreach ($component->getLanguages() as $locale) {
                    $state[$locale] = $data[$locale] ?? '';
                }

                $component->state($state);
            });
    }
}
