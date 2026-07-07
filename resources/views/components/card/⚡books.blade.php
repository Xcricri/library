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
    <div
        class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6"
    >
        @forelse ($books as $book)
            <a
                href="{{ route('public.books.view', $book->slug) }}"
                class="group relative overflow-hidden rounded-xl shadow-md"
            >
                <!-- Cover -->
                <div class="aspect-9/16">
                    <img
                        src="{{ Storage::url('covers/' . $book->cover) }}"
                        alt="{{ $book->title }}"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                </div>

                <!-- Overlay -->
                <div
                    class="absolute inset-0 flex flex-col justify-end bg-black/70 p-4 opacity-0 transition duration-300 group-hover:opacity-100"
                >
                    <h3 class="line-clamp-2 text-sm font-semibold text-white">
                        {{ $book->title }}
                    </h3>

                    <p class="line-clamp-1 text-xs text-zinc-300">
                        {{ $book->author }}
                    </p>

                    <div class="mt-3 flex items-center justify-between">
                        @if ($book->stock > 0)
                            <flux:badge
                                color="green"
                                class="rounded-full px-2 py-1 text-[10px]"
                            >
                                Available
                            </flux:badge>
                        @else
                            <flux:badge
                                color="red"
                                class="rounded-full px-2 py-1 text-[10px]"
                            >
                                Not Available
                            </flux:badge>
                        @endif

                        <flux:badge
                            color="blue"
                            class="rounded-full px-2 py-1 text-[10px]"
                        >
                            Stock: {{ $book->stock }}
                        </flux:badge>
                    </div>

                    <flux:button
                        variant="primary"
                        color="white"
                        size="sm"
                        class="mt-3"
                    >
                        View Detail
                    </flux:button>
                </div>
            </a>

        @empty
            <flux:heading size="lg" class="col-span-full py-10 text-center">
                Tidak ada buku.
            </flux:heading>
        @endforelse
    </div>
</div>
