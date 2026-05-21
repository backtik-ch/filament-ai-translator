<?php

namespace Backtik\FilamentAiTranslator\Commands;

use Illuminate\Console\Command;

class FilamentAiTranslatorCommand extends Command
{
    public $signature = 'filament-ai-translator';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
