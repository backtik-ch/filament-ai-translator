<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $translations = $getTranslations();
        $languages = $getLanguages();
    @endphp

    <div class="flex flex-col gap-1.5">
        @foreach ($languages as $locale)
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-400/20">
                    {{ strtoupper($locale) }}
                </span>
                <span class="text-sm text-gray-950 dark:text-white">
                    {{ $translations[$locale] ?? '—' }}
                </span>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
