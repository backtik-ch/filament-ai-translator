# Filament Translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backtik-ch/filament-translatable.svg?style=flat-square)](https://packagist.org/packages/backtik-ch/filament-translatable)
[![Total Downloads](https://img.shields.io/packagist/dt/backtik-ch/filament-translatable.svg?style=flat-square)](https://packagist.org/packages/backtik-ch/filament-translatable)

A Filament 4 & 5 plugin that provides translatable form fields and infolist entries with AI-powered translation generation via [laravel/ai](https://github.com/laravel/ai). Designed to work with [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable).

<!-- ![Screenshot](screenshots/hero.png) -->

## Features

- **TranslatableInput** — A form field with two display modes: modal (default) or inline
- **TranslatableEntry** — An infolist entry with two display modes: inline (expandable) or modal
- **AI Translation** — Generate all translations in a single API call using structured output
- **Configurable** — Set languages, source locale, AI provider/model globally or per-panel
- **Disable AI** — Optionally disable the AI section while keeping manual translation editing

## Requirements

- PHP ^8.3
- Laravel ^12.0 | ^13.0
- Filament ^4.0 | ^5.0

## Installation

```bash
composer require backtik-ch/filament-translatable
```

Publish the config file:

```bash
php artisan vendor:publish --tag="filament-translatable-config"
```

## Configuration

```php
// config/filament-translatable.php

return [
    'languages' => ['fr', 'de', 'it', 'en'],
    'source_locale' => 'fr',
    'ai' => [
        'enabled' => env('FILAMENT_TRANSLATABLE_AI_ENABLED', true),
        'provider' => env('FILAMENT_TRANSLATABLE_AI_PROVIDER', 'anthropic'),
        'model' => env('FILAMENT_TRANSLATABLE_AI_MODEL', 'claude-haiku-4-5-20251001'),
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

# Disable AI (keep manual editing only)
FILAMENT_TRANSLATABLE_AI_ENABLED=false
```

See the [laravel/ai documentation](https://github.com/laravel/ai) for all supported providers.

## Panel Plugin Setup

Register the plugin in your `PanelProvider`:

```php
use Backtik\FilamentTranslatable\FilamentTranslatablePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            FilamentTranslatablePlugin::make()
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
use Backtik\FilamentTranslatable\Forms\Components\TranslatableInput;

// Simple text input (short content like titles)
TranslatableInput::make('title')

// Textarea for longer content
TranslatableInput::make('description')->inputType('textarea')

// Inline mode — all locale fields rendered directly in the form
TranslatableInput::make('title')->inline()

// Inline textarea
TranslatableInput::make('description')->inline()->inputType('textarea')
```

#### Modal mode (default)

The field displays the source locale value as a readonly preview. Clicking the translate button opens a modal where you can:

1. Edit translations for each configured language
2. Select a source language
3. Click "Generate translations" to auto-translate all languages in one AI call

<!-- ![Form field](screenshots/form-field.png) -->
<!-- ![Translation modal](screenshots/translation-modal.png) -->

#### Inline mode

All locale fields are rendered directly in the form without requiring a modal. The AI generation section is also displayed inline. Best suited for forms where you want all translations visible at a glance.

<!-- ![Inline form field](screenshots/form-inline.png) -->

### Infolist Entry

```php
use Backtik\FilamentTranslatable\Infolists\Components\TranslatableEntry;

// Inline mode (default) — shows all translations with expand/collapse
TranslatableEntry::make('title')

// Modal mode — shows source text, click to view all translations in a modal
TranslatableEntry::make('description')->modal()

// Truncate text to a specific number of lines
TranslatableEntry::make('description')->lineClamp(3)
TranslatableEntry::make('description')->modal()->lineClamp(2)
```

#### Inline mode (default)

Displays the first language inline with an expandable section to reveal the remaining translations.

<!-- ![Inline entry](screenshots/infolist-inline.png) -->

#### Modal mode

Displays the source locale text directly. A "View translations" link opens a Filament modal showing all translations. Best for longer text content.

<!-- ![Modal entry](screenshots/infolist-modal.png) -->

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
