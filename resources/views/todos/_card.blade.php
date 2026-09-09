@php
    $priority = $todo->priorityBadge();
    $catBadge = $todo->categoryBadge();
    $isOverdue = !$todo->is_completed && $todo->due_date && $todo->due_date->isPast() && !$todo->due_date->isToday();
    $showCompleted = $showCompleted ?? false;

    // Inner vertical accent bar color
    $accentColor = match($todo->priority) {
        'high' => 'bg-rose-500',
        'low' => 'bg-slate-400 dark:bg-slate-500',
        default => 'bg-amber-500',
    };
    if ($todo->is_completed) {
        $accentColor = 'bg-emerald-500';
    }

    // Category SVG icons & themes
    $catIcons = [
        'kerja' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v1.081m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>',
        'pribadi' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>',
        'belanja' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>',
        'keuangan' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'default' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>',
    ];
    $catStyle = match($todo->category) {
        'kerja' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200/70 dark:border-indigo-800/40',
        'pribadi' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200/70 dark:border-purple-800/40',
        'belanja' => 'bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border-teal-200/70 dark:border-teal-800/40',
        'keuangan' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200/70 dark:border-emerald-800/40',
        default => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
    };
    $iconSvg = $catIcons[$todo->category] ?? $catIcons['default'];

    // Priority SVG icons
    $priorityIcons = [
        'high' => '<svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /></svg>',
        'medium' => '<svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>',
        'low' => '<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>',
    ];
    $priorityStyle = match($todo->priority) {
        'high' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200/70 dark:border-rose-800/40',
        'low' => 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        default => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200/70 dark:border-amber-800/40',
    };
@endphp

<div class="relative bg-white dark:bg-slate-800/95 rounded-2xl p-4 pl-4.5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all space-y-3 overflow-hidden {{ $todo->is_completed ? 'opacity-70 bg-slate-50/60 dark:bg-slate-800/60' : '' }}">
    <!-- Vertical Accent Bar on the left -->
    <span class="absolute left-0 top-3.5 bottom-3.5 w-1 rounded-r-full {{ $accentColor }}" aria-hidden="true"></span>

    {{-- Header Row: Checkbox, Title, Description & Action Buttons --}}
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-start gap-3 min-w-0 flex-1">
            {{-- Smooth Interactive Checkbox --}}
            <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="shrink-0 mt-0.5">
                @csrf
                @method('PATCH')
                <button type="submit"
                    aria-label="{{ $todo->is_completed ? 'Tandai belum selesai' : 'Tandai selesai' }}"
                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all cursor-pointer shrink-0
                        {{ $todo->is_completed
                            ? 'bg-emerald-500 border-emerald-500 text-white shadow-xs shadow-emerald-500/30'
                            : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 hover:border-emerald-500 hover:scale-105 active:scale-90' }}">
                    @if($todo->is_completed)
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    @endif
                </button>
            </form>

            {{-- Title & Description --}}
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-snug break-words {{ $todo->is_completed ? 'line-through text-slate-400 dark:text-slate-500' : '' }}">
                    {{ $todo->title }}
                </h3>
                @if($todo->description)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed font-normal">
                        {{ $todo->description }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Action Buttons (Edit & Delete) with sleek icon buttons --}}
        <div class="flex items-center gap-1 shrink-0 -mr-1">
            @if(!$todo->is_completed)
                <button type="button"
                    onclick="openEditModal(this)"
                    data-id="{{ $todo->id }}"
                    data-title="{{ $todo->title }}"
                    data-priority="{{ $todo->priority }}"
                    data-category="{{ $todo->category ?? '' }}"
                    data-due-date="{{ $todo->due_date ? $todo->due_date->format('Y-m-d') : '' }}"
                    data-description="{{ $todo->description ?? '' }}"
                    aria-label="Edit aktivitas"
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-400 flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                </button>
            @endif

            <form action="{{ route('todos.destroy', $todo) }}" method="POST"
                onsubmit="return confirm('Hapus aktivitas ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    aria-label="Hapus aktivitas"
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Footer Row: Category & Priority Badges (Left) vs Deadline (Right) --}}
    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/60 flex-wrap sm:flex-nowrap">
        <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
            @if($todo->category)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $catStyle }}">
                    {!! $iconSvg !!}
                    <span>{{ $catBadge['label'] }}</span>
                </span>
            @endif
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $priorityStyle }}">
                {!! $priorityIcons[$todo->priority] ?? $priorityIcons['medium'] !!}
                <span>{{ $priority['label'] }}</span>
            </span>
            @if($todo->is_completed && $todo->completed_at)
                <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
                    ✓ Selesai {{ $todo->completed_at->diffForHumans() }}
                </span>
            @endif
        </div>

        {{-- Tanggal Deadline Slot (Refined Pill) --}}
        @if($todo->due_date && !$showCompleted)
            <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border ml-auto shrink-0
                {{ $isOverdue 
                    ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/40 font-bold' 
                    : ($todo->due_date->isToday() 
                        ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/40 font-bold' 
                        : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/40') }}">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <span>
                    @if($todo->due_date->isToday())
                        Hari ini
                    @elseif($todo->due_date->isTomorrow())
                        Besok
                    @elseif($isOverdue)
                        Lewat {{ $todo->due_date->translatedFormat('d M') }}
                    @else
                        {{ $todo->due_date->translatedFormat('d M Y') }}
                    @endif
                </span>
            </div>
        @elseif(!$todo->due_date && !$showCompleted)
            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 shrink-0 ml-auto">Tanpa tenggat</span>
        @endif
    </div>
</div>
