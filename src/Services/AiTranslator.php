<?php

namespace Backtik\FilamentAiTranslator\Services;

use Illuminate\Contracts\JsonSchema\JsonSchema;

use function Laravel\Ai\agent;

class AiTranslator
{
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        if (trim($text) === '') {
            return '';
        }

        $prompt = str_replace(
            [':source_language', ':target_language'],
            [$sourceLocale, $targetLocale],
            config('ai-translator.ai.prompt'),
        );

        $response = agent(instructions: $prompt)
            ->prompt(
                prompt: $text,
                provider: config('ai-translator.ai.provider'),
                model: config('ai-translator.ai.model'),
            );

        return trim((string) $response);
    }

    public function translateToAll(string $text, string $sourceLocale, array $targetLocales): array
    {
        if (trim($text) === '') {
            return array_fill_keys($targetLocales, '');
        }

        $targetLocales = array_values(array_filter(
            $targetLocales,
            fn (string $locale) => $locale !== $sourceLocale,
        ));

        if (empty($targetLocales)) {
            return [$sourceLocale => $text];
        }

        $languages = implode(', ', $targetLocales);
        $instructions = "Translate the following text from {$sourceLocale} to each of these languages: {$languages}. Return only the translations.";

        $response = agent(
            instructions: $instructions,
            schema: fn (JsonSchema $schema) => collect($targetLocales)
                ->mapWithKeys(fn (string $locale) => [
                    $locale => $schema->string()->description("Translation in {$locale}")->required(),
                ])
                ->all(),
        )->prompt(
            prompt: $text,
            provider: config('ai-translator.ai.provider'),
            model: config('ai-translator.ai.model'),
        );

        $translations = [];
        foreach ($targetLocales as $locale) {
            $translations[$locale] = $response[$locale] ?? '';
        }

        return $translations;
    }
}
