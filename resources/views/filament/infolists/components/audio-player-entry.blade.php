<x-dynamic-component :component="$entry->getEntryWrapperView()" :entry="$entry">
    <div class="flex flex-col gap-y-2 w-full">
        @if ($getState())
            <audio controls class="w-full rounded-lg" style="width: 100%;">
                <source src="{{ getFileUrl($getState()) }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        @else
            <div class="text-sm text-gray-500 dark:text-gray-400">
                No audio file available.
            </div>
        @endif
    </div>
</x-dynamic-component>
