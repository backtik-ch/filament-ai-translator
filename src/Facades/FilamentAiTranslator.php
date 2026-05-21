<?php

namespace Backtik\FilamentAiTranslator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Backtik\FilamentAiTranslator\FilamentAiTranslator
 */
class FilamentAiTranslator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Backtik\FilamentAiTranslator\FilamentAiTranslator::class;
    }
}
