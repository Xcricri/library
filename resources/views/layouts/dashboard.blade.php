@if (auth()->check() && auth()->user()->role === 'admin')
    <x-layouts::app.sidebar-admin :title="$title ?? null">
        <flux:main> {{ $slot }} </flux:main>
    </x-layouts::app.sidebar-admin>
@elseif (auth()->check() && auth()->user()->role === 'user')
    <x-layouts::app.sidebar :title="$title ?? null">
        <flux:main> {{ $slot }} </flux:main>
    </x-layouts::app.sidebar>
@endif
