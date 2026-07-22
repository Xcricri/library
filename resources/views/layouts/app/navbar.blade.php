<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    @include ('partials.head')
</head>

<body>
    <flux:header>
        @if (Route::has('login'))
            <flux:navbar>
                @auth
                    @if (auth()->user()->hasRole('admin'))
                        <flux:navbar.item
                            href="{{ route('admin.dashboard') }}"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            wire:navigate
                        >
                            Dashboard
                        </flux:navbar.item>
                    @elseif (auth()->user()->hasRole('member'))
                        <flux:navbar.item
                            href="{{ route('member.dashboard') }}"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            wire:navigate
                        >
                            Dashboard
                        </flux:navbar.item>
                    @elseif (auth()->user()->hasRole('staff'))
                        <flux:navbar.item
                            href="{{ route('staff.dashboard') }}"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            wire:navigate
                        >
                            Dashboard
                        </flux:navbar.item>
                    @endif
                @else
                    <flux:navbar.item
                        href="{{ route('login') }}"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                        wire:navigate
                    >
                        Log in
                    </flux:navbar.item>

                    @if (Route::has('register'))
                        <flux:navbar.item
                            href="{{ route('register') }}"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            wire:navigate
                        >
                            Register
                        </flux:navbar.item>
                    @endif
                @endauth
            </flux:navbar>
        @endif
    </flux:header>

    {{ $slot }}

    @livewireScripts
    @fluxScripts
</body>
</html>
