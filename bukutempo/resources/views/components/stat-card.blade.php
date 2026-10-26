@props(['title', 'note', 'icon', 'iconClass' => 'bg-slate-100 text-slate-500', 'noteClass' => 'text-ink-soft'])

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex items-center justify-between">
        <p class="text-sm font-semibold">{{ $title }}</p>
        <span class="grid size-8 place-items-center rounded-lg {{ $iconClass }}">
            <x-icon :name="$icon" class="size-4" />
        </span>
    </div>
    <p class="mt-4 text-[26px] font-semibold tracking-tight">{{ $slot }}</p>
    <p class="mt-2 text-xs {{ $noteClass }}">{{ $note }}</p>
</div>
