# Filament AI Translator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backtik-ch/filament-ai-translator.svg?style=flat-square)](https://packagist.org/packages/backtik-ch/filament-ai-translator)
[![Total Downloads](https://img.shields.io/packagist/dt/backtik-ch/filament-ai-translator.svg?style=flat-square)](https://packagist.org/packages/backtik-ch/filament-ai-translator)

A Filament 4 plugin that provides translatable form fields and infolist entries with AI-powered translation generation via [laravel/ai](https://github.com/laravel/ai). Designed to work with [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable).

## Features

- **TranslatableInput** — A form field that displays the source locale value with a button to open a translation modal
- **TranslatableEntry** — An infolist entry that displays all translations at a glance
- **AI Translation** — Generate translations from a source language to all configured languages using any AI provider supported by laravel/ai
- **Configurable** — Set languages, source locale, AI provider/model globally or per-panel

## Requirements

- PHP ^8.3
- Laravel ^12.0 | ^13.0
- Filament ^4.0
- laravel/ai ^0.7

## Installation

```bash
composer require backtik-ch/filament-ai-translator
```

Publish the config file:

```bash
php artisan vendor:publish --tag="filament-ai-translator-config"
```

## Configuration

```php
// config/ai-translator.php

return [
    'languages' => ['fr', 'de', 'it', 'en'],
    'source_locale' => 'fr',
    'ai' => [
        'provider' => env('AI_TRANSLATOR_PROVIDER', 'anthropic'),
        'model' => env('AI_TRANSLATOR_MODEL', 'claude-haiku-4-5-20251001'),
        'prompt' => 'Translate the following text from :source_language to :target_language. Return only the translation, nothing else.',
    ],
];
```

### API Keys

API keys are managed by `laravel/ai`. Add the relevant key to your `.env`:

```env
# Anthropic
ANTHROPIC_API_KEY=sk-ant-...

# OpenAI
OPENAI_API_KEY=sk-...
```

See the [laravel/ai documentation](https://github.com/laravel/ai) for all supported providers.

## Panel Plugin Setup

Register the plugin in your `PanelProvider`:

```php
use Backtik\FilamentAiTranslator\FilamentAiTranslatorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            FilamentAiTranslatorPlugin::make()
                ->languages(['fr', 'de', 'en'])
                ->sourceLocale('fr')
                ->aiProvider('openai')
                ->aiModel('gpt-4o-mini')
        );
}
```

All plugin methods are optional — values fall back to the config file.

## Usage

### Model Setup

Your model must use `spatie/laravel-translatable` and the translatable columns must be `json` in the database:

```php
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description'];
}
```

### Form Field

```php
use Backtik\FilamentAiTranslator\Forms\Components\TranslatableInput;

// Simple text input
TranslatableInput::make('title')

// Textarea for longer content
TranslatableInput::make('description')->inputType('textarea')
```

The field displays the source locale value as a readonly preview. Clicking the translate button opens a modal where you can:

1. Edit translations for each configured language
2. Select a source language
3. Click "Generate translations" to auto-translate using AI

When you close the modal, the translations are stored in the field state. The form saves everything when submitted (standard Filament behavior).

### Infolist Entry

```php
use Backtik\FilamentAiTranslator\Infolists\Components\TranslatableEntry;

TranslatableEntry::make('title')
```

Displays all configured languages with their values (or "—" if empty).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [SimonMeia](https://github.com/backtik-ch)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
