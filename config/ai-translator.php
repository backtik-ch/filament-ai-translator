<?php

// config for Backtik/FilamentAiTranslator
return [

    /*
    |--------------------------------------------------------------------------
    | Available Languages
    |--------------------------------------------------------------------------
    |
    | List of locale codes available for translation.
    |
    */
    'languages' => ['fr', 'de', 'it', 'en'],

    /*
    |--------------------------------------------------------------------------
    | Source Locale
    |--------------------------------------------------------------------------
    |
    | The locale displayed in the main form field (readonly preview).
    |
    */
    'source_locale' => 'fr',

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the AI translation provider (uses laravel/ai).
    |
    */
    'ai' => [
        'provider' => env('AI_TRANSLATOR_PROVIDER', 'anthropic'),
        'model' => env('AI_TRANSLATOR_MODEL', 'claude-haiku-4-5-20251001'),
        'prompt' => 'Translate the following text from :source_language to :target_language. Return only the translation, nothing else.',
    ],

];
