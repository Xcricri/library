<?php

use Livewire\Component;
use App\Models\Book;
use App\Models\Review;

new class extends Component {
    public $books;
    public $ratings;

    // Mount data
    public function mount()
    {
        $this->books = Book::latest()->get();
        $this->ratings = [];
        foreach ($this->books as $book) {
            $this->ratings[$book->id] = Review::where('book_id', $book->id)->avg('rating');
        }
    }

    public function render()
    {
        return $this->view([
            'books' => $this->books,
            'ratings' => $this->ratings,
        ]);
    }
};
?>

<div class="mx-auto max-w-full px-4 py-6">
    <div
        class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6"
    >
        @forelse ($books as $book)
            <div class="group relative">
                {{-- Wishlist --}}
                <div class="absolute top-3 right-3 z-20">
                    <livewire:toggle.wishlist :book="$book" />
                </div>
                <a
                    href="{{ route('public.books.view', $book->slug) }}"
                    class="block overflow-hidden rounded-2xl bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    {{-- Cover --}}
                    <div class="relative aspect-9/16 overflow-hidden">
                        <img
                            src="{{ Storage::url('covers/' . $book->cover) }}"
                            alt="{{ $book->title }}"
                            class="h-full w-full object-cover"
                        />
                        {{-- Overlay --}}
                        <div
                            class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"
                        >
                            <div class="absolute bottom-0 w-full p-4">
                                <div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <!-- Book Info -->
                                    <div class="min-w-0 flex-1">
                                        <h3
                                            class="line-clamp-2 text-sm font-semibold text-white"
                                        >
                                            {{ $book->title }}
                                        </h3>

                                        <p class="mt-1 line-clamp-1 text-xs text-zinc-400">
                                            {{ $book->author }}
                                        </p>
                                    </div>

                                    <!-- Rating -->
                                    <div
                                        class="flex shrink-0 items-center gap-1"
                                    >
                                        <flux:icon.star
                                            variant="solid"
                                            class="h-4 w-4
                                            {{ $ratings[$book->id] ?? 0 > 0 ? 'text-yellow-400' : 'text-zinc-600 ' }} "
                                        />
                                        <span
                                            class="text-sm font-semibold text-white"
                                        >
                                            {{ number_format($ratings[$book->id] ?? 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div
                                    class="mt-3 flex items-center justify-between"
                                >
                                    @if ($book->stock > 0)
                                        <flux:badge
                                            color="green"
                                            size="sm"
                                            class="text-lime-100"
                                        >
                                            Available
                                        </flux:badge>
                                    @else
                                        <flux:badge
                                            color="red"
                                            size="sm"
                                            class="text-red-100"
                                        >
                                            Not Available
                                        </flux:badge>
                                    @endif
                                    <flux:badge
                                        variant="solid"
                                        color="zinc"
                                        class="text-[10px]"
                                    >
                                        {{ $book->stock }} pcs
                                    </flux:badge>
                                </div>
                                <button
                                    class="mt-4 w-full rounded-lg bg-white py-2 text-xs font-semibold text-zinc-900 transition hover:bg-zinc-200"
                                >
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
