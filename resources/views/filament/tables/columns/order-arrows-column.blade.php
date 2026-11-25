@php
    $record = $getRecord();
    $recordKey = $getRecordKey();
    $modelClass = $record ? get_class($record) : null;
    $isFirst = $record && $modelClass ? ! $modelClass::query()->where('position', '<', $record->position)->exists() : true;
    $isLast = $record && $modelClass ? ! $modelClass::query()->where('position', '>', $record->position)->exists() : true;
@endphp

<div class="inline-flex items-center gap-0.5 py-1">
    <button
        type="button"
        wire:click.stop="mountTableAction('moveUp', '{{ $recordKey }}')"
        wire:loading.attr="disabled"
        @disabled($isFirst)
        title="Move up"
        aria-label="Move up"
        class="p-0.5 rounded text-gray-400 hover:text-primary-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-800/80 disabled:opacity-20 disabled:pointer-events-none transition"
    >
        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd" />
        </svg>
    </button>
    <button
        type="button"
        wire:click.stop="mountTableAction('moveDown', '{{ $recordKey }}')"
        wire:loading.attr="disabled"
        @disabled($isLast)
        title="Move down"
        aria-label="Move down"
        class="p-0.5 rounded text-gray-400 hover:text-primary-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-primary-400 dark:hover:bg-gray-800/80 disabled:opacity-20 disabled:pointer-events-none transition"
    >
        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd" />
        </svg>
    </button>
</div>
