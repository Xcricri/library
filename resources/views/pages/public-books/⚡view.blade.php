<?php

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

use App\Models\Book;

new class extends Component {
    public Book $book;

    // Mount data
    public function mount($slug)
    {
        $this->book = Book::with(['categories', 'genres'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->book->published_at = Carbon::parse($this->book->published_at);
    }

    #[On('borrowed')]
    public function refreshBook($message)
    {
        $this->book = Book::with(['categories', 'genres'])
            ->where('slug', $this->book->slug)
            ->firstOrFail();

        session()->flash('message', $message);
    }

    public function render()
    {
        return $this->view()->layout('layouts::app')->title('View Book');
    }
};
?>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    {{-- Flash message --}}
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-6 shadow-sm animate-fade-in">
            {{ session('message') }}
        </flux:callout>
    @endif

    <flux:card class="overflow-hidden shadow-md border border-zinc-100 dark:border-zinc-800">
        <div class="grid gap-8 p-6 sm:p-10 lg:grid-cols-4">

            {{-- Bagian Kiri: Cover & Aksi --}}
            <div class="space-y-6 lg:col-span-1">
                <div class="overflow-hidden rounded-xl shadow-lg ">

                    <livewire:toggle.wishlist :book="$book" />
                    <img src="{{ Storage::url('covers/' . $book->cover) }}" alt="{{ $book->title }}"
                        class="aspect-2/3 w-full object-cover" />
                </div>

                <div class="pt-2">
                    @auth
                        @if (auth()->user()->hasRole('member'))
                            @if (auth()->user()->hasBorrowed($book))
                                <div
                                    class="flex items-center justify-center gap-2 rounded-lg bg-red-50 p-3 text-sm font-medium text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                    <flux:icon name="exclamation-triangle" class="size-4 shrink-0" />
                                    <span>You have borrowed this book.</span>
                                </div>
                            @else
                                <livewire:modal.borrowing :book="$book" />
                            @endif
                        @endif
                    @else
                        <flux:button variant="primary" icon="book-open" class="w-full shadow-sm"
                            href="{{ route('login') }}">
                            Login to Borrow
                        </flux:button>
                    @endauth
                </div>
            </div>

            {{-- Bagian Kanan: Detail Informasi --}}
            <div class="space-y-6 lg:col-span-3 flex flex-col justify-between">
                <div class="space-y-5">
                    {{-- Judul & Penulis --}}
                    <div class="space-y-1">
                        <flux:heading size="2xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                            {{ $book->title }}
                        </flux:heading>

                        <flux:text size="lg" class="text-primary font-semibold tracking-wide">
                            {{ $book->author }}
                        </flux:text>
                    </div>

                    {{-- Metadata Buku (Penerbit & Tahun) --}}
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="font-medium">{{ $book->publisher_name }}</span>
                        <span class="text-zinc-300 dark:text-zinc-700">•</span>
                        <span>{{ Carbon::parse($book->published_at)->format('Y') }}</span>
                    </div>

                    {{-- Grup Badge: Genre & Kategori Terpisah --}}
                    <div class="space-y-3 pt-1">
                        {{-- Baris Atas: Genre --}}
                        @if ($book->genres->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 w-20 shrink-0">Genre:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($book->genres as $genre)
                                        <flux:badge variant="brand" size="sm" class="rounded-md">
                                            {{ $genre->name }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Baris Bawah: Kategori --}}
                        @if ($book->categories->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 w-20 shrink-0">Category:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($book->categories as $category)
                                        <flux:badge variant="neutral" size="sm" class="rounded-md">
                                            {{ $category->name }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <flux:separator class="my-4" />

                    {{-- Sinopsis --}}
                    <div class="space-y-3">
                        <flux:heading size="lg" class="font-semibold text-zinc-800 dark:text-zinc-200">
                            Synopsis
                        </flux:heading>

                        <flux:text
                            class="leading-7 whitespace-pre-line text-zinc-600 dark:text-zinc-300 text-sm sm:text-base">
                            {{ $book->description }}
                        </flux:text>
                    </div>
                </div>
            </div>

        </div>
    </flux:card>
</div>
