<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="flex items-center gap-2">
        <input type="text" readonly value="{{ $getDisplayValue() }}"
            placeholder="{{ __('filament-ai-translator::translations.placeholder') }}"
            class="fi-input block w-full rounded-lg border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6" />

        <div class="shrink-0">
            {{ $getAction('translate') }}
        </div>
    </div>
</x-dynamic-component>
