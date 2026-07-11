@if (auth()->user()->hasRole('admin'))
    <x-layouts::app.sidebar-admin :title="$title ?? null">
        <flux:main> {{ $slot }} </flux:main>
    </x-layouts::app.sidebar-admin>
@elseif (auth()->user()->hasRole('member'))
    <x-layouts::app.sidebar :title="$title ?? null">
        <flux:main> {{ $slot }} </flux:main>
    </x-layouts::app.sidebar>
@elseif (auth()->user()->hasRole('staff'))
    <x-layouts::app.sidebar-staff :title="$title ?? null">
        <flux:main> {{ $slot }} </flux:main>
    </x-layouts::app.sidebar-staff>
@endif
