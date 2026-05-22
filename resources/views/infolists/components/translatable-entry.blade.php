<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $translations = $getTranslations();
        $languages = $getLanguages();
        $isModal = $isModal();
        $sourceLocale = $isModal ? $getSourceLocale() : null;
        $firstLocale = $languages[0] ?? null;
        $remainingLanguages = array_slice($languages, 1);
    @endphp

    @if ($isModal)
        <div>
            <div style="font-size: 0.875rem; white-space: pre-line;">{{ $translations[$sourceLocale] ?? '—' }}</div>

            @if (count($languages) > 1)
                <div style="margin-top: 0.375rem;">
                    <x-filament::modal width="xl">
                        <x-slot name="trigger">
                            <x-filament::link size="sm" tag="button" icon="heroicon-o-language">
                                {{ __('filament-ai-translator::translations.view_translations') }}
                            </x-filament::link>
                        </x-slot>

                        <x-slot name="heading">
                            {{ __('filament-ai-translator::translations.modal_heading') }}
                        </x-slot>

                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @foreach ($languages as $locale)
                                <div style="display: flex; gap: 0.5rem;">
                                    <div style="flex-shrink: 0;">
                                        <x-filament::badge size="sm" color="gray">
                                            <span
                                                style="font-family: monospace; display: inline-block; text-align: center;">
                                                {{ strtoupper($locale) }}
                                            </span>
                                        </x-filament::badge>
                                    </div>
                                    <span
                                        style="font-size: 0.875rem; white-space: pre-line;">{{ $translations[$locale] ?? '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::modal>
                </div>
            @endif
        </div>
    @else
        <div x-data="{ expanded: false }" style="display: flex; flex-direction: column; gap: 0.375rem;">
            @if ($firstLocale)
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="flex-shrink: 0;">
                        <x-filament::badge size="sm" color="gray">
                            <span style="font-family: monospace; display: inline-block; text-align: center">
                                {{ strtoupper($firstLocale) }}
                            </span>
                        </x-filament::badge>
                    </div>
                    <span
                        style="font-size: 0.875rem; white-space: pre-line;">{{ $translations[$firstLocale] ?? '—' }}</span>
                </div>
            @endif

            @if (count($remainingLanguages))
                <div x-show="expanded" x-collapse>
                    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                        @foreach ($remainingLanguages as $locale)
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="flex-shrink: 0;">
                                    <x-filament::badge size="sm" color="gray">
                                        <span
                                            style="font-family: monospace; display: inline-block; text-align: center;">
                                            {{ strtoupper($locale) }}
                                        </span>
                                    </x-filament::badge>
                                </div>
                                <span
                                    style="font-size: 0.875rem; white-space: pre-line;">{{ $translations[$locale] ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-start;">
                    <x-filament::link size="sm" x-on:click="expanded = !expanded" tag="button">
                        <span
                            x-text="expanded ? '{{ __('filament-ai-translator::translations.show_less') }}' : '{{ __('filament-ai-translator::translations.show_more', ['count' => count($remainingLanguages)]) }}'"></span>
                    </x-filament::link>
                </div>
            @endif
        </div>
    @endif
</x-dynamic-component>
