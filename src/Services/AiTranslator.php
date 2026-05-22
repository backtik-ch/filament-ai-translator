<?php

namespace Backtik\FilamentAiTranslator\Services;

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
        $translations = [];

        foreach ($targetLocales as $locale) {
            if ($locale === $sourceLocale) {
                $translations[$locale] = $text;

                continue;
            }

            $translations[$locale] = $this->translate($text, $sourceLocale, $locale);
        }

        return $translations;
    }
}
