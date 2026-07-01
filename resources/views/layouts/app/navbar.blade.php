<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('partials.head')
</head>

<body>
    <header>
        @if (Route::has('login'))
            <flux:navbar>
                @auth
                    <flux:navbar.item href="{{ route('dashboard') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        wire:navigate>
                        Dashboard
                    </flux:navbar.item>
                @else
                    <flux:navbar.item href="{{ route('login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        wire:navigate>
                        Log in
                    </flux:navbar.item>

                    @if (Route::has('register'))
                        <flux:navbar.item href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                            wire:navigate>
                            Register
                        </flux:navbar.item>
                    @endif
                @endauth
            </flux:navbar>
        @endif
    </header>
    {{ $slot }}

    @livewireScripts
    @fluxScripts
</body>

</html>
