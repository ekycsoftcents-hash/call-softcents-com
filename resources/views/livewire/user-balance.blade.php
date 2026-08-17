<div wire:poll.5s
    class="fi-input-wrp flex items-center gap-x-2 px-3 py-2 rounded-lg shadow-sm ring-1 ring-gray-950/10 dark:ring-white/10 bg-white dark:bg-gray-900">
    <span class="text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
        Balance:
    </span>
    <div
        class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 py-1 bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-white/10 dark:text-white dark:ring-white/20">
        <span>
            {{ number_format(auth()->user()->balance, 2) }} BDT
        </span>
    </div>
</div>