<?php

namespace Backtik\FilamentTranslatable\Forms\Components;

use Backtik\FilamentTranslatable\Services\AiTranslator;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;

class TranslatableInput extends Field
{
    protected string $view = 'filament-translatable::forms.components.translatable-input';

    protected string | Closure $inputType = 'text';

    protected bool | Closure $isInline = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(function (self $component, $state): void {
            if ($component->isInline()) {
                return;
            }

            if (is_string($state)) {
                $decoded = json_decode($state, true);
                $component->state(is_array($decoded) ? $decoded : []);
            } elseif (! is_array($state)) {
                $component->state([]);
            }
        });

        $this->dehydrateStateUsing(function ($state) {
            if ($this->isInline()) {
                return $state;
            }

            return is_array($state) ? $state : [];
        });

        $this->registerActions([
            $this->getTranslateAction(),
        ]);

        $this->childComponents(fn () => $this->isInline() ? $this->buildLocaleFields() : []);
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
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
        return config('filament-translatable.languages', []);
    }

    public function getSourceLocale(): string
    {
        return config('filament-translatable.source_locale', 'fr');
    }

    public function getDisplayValue(): string
    {
        $state = $this->getState();

        if (! is_array($state)) {
            return '';
        }

        return $state[$this->getSourceLocale()] ?? '';
    }

    /**
     * @return array<Component>
     */
    private function buildLocaleFields(): array
    {
        $fields = [];

        foreach ($this->getLanguages() as $locale) {
            $prefix = new HtmlString('<span style="font-family: monospace; display: inline-block; width: 1.5rem; text-align: center;">' . strtoupper($locale) . '</span>');

            $field = $this->getInputType() === 'textarea'
                ? Textarea::make($locale)->label(strtoupper($locale))->rows(3)
                : TextInput::make($locale)->hiddenLabel()->prefix($prefix);

            $fields[] = $field;
        }

        if (config('filament-translatable.ai.enabled', true)) {
            $fields[] = $this->buildAiSection();
        }

        return $fields;
    }

    private function buildAiSection(): Section
    {
        return Section::make(__('filament-translatable::translations.ai_section'))
            ->secondary()
            ->description(__('filament-translatable::translations.ai_section_description'))
            ->compact()
            ->schema([
                Flex::make([
                    Select::make('source_locale')
                        ->label(__('filament-translatable::translations.source_language'))
                        ->options(array_combine($this->getLanguages(), array_map('strtoupper', $this->getLanguages())))
                        ->default($this->getSourceLocale())
                        ->required()
                        ->dehydrated(false),
                    SchemaActions::make([
                        Action::make('generate')
                            ->label(__('filament-translatable::translations.generate'))
                            ->icon('heroicon-o-sparkles')
                            ->color('warning')
                            ->action(function (Action $action, Get $get, Set $set) {
                                $translatableInput = $this;

                                $sourceLocale = $get('source_locale') ?? $translatableInput->getSourceLocale();
                                $sourceText = $get($sourceLocale) ?? '';

                                if (trim($sourceText) === '') {
                                    Notification::make()
                                        ->title(__('filament-translatable::translations.empty_source'))
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $targetLocales = array_filter(
                                    $translatableInput->getLanguages(),
                                    fn (string $locale) => $locale !== $sourceLocale,
                                );

                                try {
                                    $translator = app(AiTranslator::class);
                                    $translations = $translator->translateToAll($sourceText, $sourceLocale, $targetLocales);

                                    foreach ($translations as $locale => $translation) {
                                        $set($locale, $translation);
                                    }
                                } catch (\Throwable $e) {
                                    Notification::make()
                                        ->title(__('filament-translatable::translations.error'))
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ])->grow(false),
                ])->verticallyAlignEnd(),
            ]);
    }

    public function getTranslateAction(): Action
    {
        return Action::make('translate')
            ->label(__('filament-translatable::translations.translate'))
            ->icon('heroicon-o-language')
            ->modalHeading(__('filament-translatable::translations.modal_heading'))
            ->modalWidth('xl')
            ->visible(fn (): bool => ! $this->isInline())
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
            ->schema(fn (): array => $this->buildLocaleFields())
            ->action(function (array $data, self $component): void {
                $state = [];

                foreach ($component->getLanguages() as $locale) {
                    $state[$locale] = $data[$locale] ?? '';
                }

                $component->state($state);
            });
    }
}
