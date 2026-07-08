<?php

use Livewire\Component;
use App\Models\Book;

new class extends Component {
    public $books;

    // Mount data
    public function mount()
    {
        $this->books = Book::latest()->get();
    }

    public function render()
    {
        return $this->view([
            'books' => $this->books,
        ]);
    }
};
?>

<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">

        @forelse ($books as $book)
            <div class="group relative">

                {{-- Wishlist --}}
                <div class="absolute right-3 top-3 z-20">
                    <livewire:toggle.wishlist :book="$book" />
                </div>

                <a href="{{ route('public.books.view', $book->slug) }}"
                    class="block overflow-hidden rounded-2xl bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                    {{-- Cover --}}
                    <div class="relative aspect-9/16 overflow-hidden">

                        <img src="{{ Storage::url('covers/' . $book->cover) }}" alt="{{ $book->title }}"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

                        {{-- Overlay --}}
                        <div
                            class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-transparent opacity-0 transition duration-300 group-hover:opacity-100">

                            <div class="absolute bottom-0 w-full p-4">

                                <h3 class="line-clamp-2 text-sm font-semibold text-white">
                                    {{ $book->title }}
                                </h3>

                                <p class="mt-1 line-clamp-1 text-xs text-zinc-300">
                                    {{ $book->author }}
                                </p>

                                <div class="mt-3 flex items-center justify-between">

                                    @if ($book->stock > 0)
                                        <flux:badge color="green" size="sm">
                                            Available
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">
                                            Not Available
                                        </flux:badge>
                                    @endif

                                    <flux:badge color="gray" class="text-[10px]">
                                        {{ $book->stock }} pcs
                                    </flux:badge>

                                </div>

                                <button
                                    class="mt-4 w-full rounded-lg bg-white py-2 text-xs font-semibold text-zinc-900 transition hover:bg-zinc-200">
                                    View Detail
                                </button>

                            </div>
                        </div>
                    </div>
                </a>

            </div>

        @empty

            <div class="col-span-full py-20 text-center text-zinc-500">
                Tidak ada buku.
            </div>
        @endforelse

    </div>
</div>
