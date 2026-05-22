<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div style="flex: 1; min-width: 0;">
            <x-filament::input.wrapper :prefix="strtoupper($getSourceLocale())">
                <x-filament::input type="text" readonly disabled :value="$getDisplayValue()" :placeholder="__('filament-translatable::translations.placeholder')" />
            </x-filament::input.wrapper>
        </div>

        {{ $getAction('translate') }}
    </div>
</x-dynamic-component>
