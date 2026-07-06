<?php

use Livewire\Component;
use App\Models\Book;

new class extends Component {
    public Book $book;

    public function mount($slug)
    {
        $this->book = Book::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return $this->view([
            'books' => $this->book,
        ])->layout('layouts::app');
    }
};
?>

<div class="max-w-sm mx-auto">
    <flux:card class="overflow-hidden rounded-2xl shadow-lg p-0">

        <!-- Cover -->
        <div class="aspect-2/3 overflow-hidden bg-zinc-100">
            <img src="{{ $book->cover ? Storage::url('covers/' . $book->cover) : 'https://via.placeholder.com/300x400' }}"
                alt="{{ $book->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>

        <!-- Content -->
        <div class="p-5 space-y-4">

            <div>
                <flux:heading size="lg">
                    {{ $book->title }}
                </flux:heading>

                <flux:text class="text-zinc-500">
                    {{ $book->author }}
                </flux:text>
            </div>

            <div class="flex items-center justify-between">

                @if ($book->stock > 0)
                    <flux:badge color="green">
                        Tersedia
                    </flux:badge>
                @else
                    <flux:badge color="red">
                        Habis
                    </flux:badge>
                @endif

                <flux:text class="text-sm text-zinc-500">
                    Stok: {{ $book->stock }}
                </flux:text>

            </div>

            <flux:text class="text-sm text-zinc-600 line-clamp-3">
                {{ $book->description }}
            </flux:text>

            <div class="grid grid-cols-2 gap-3 pt-2">

                <flux:button variant="ghost" href="{{ route('/') }}">
                    Kembali
                </flux:button>

                @auth
                    <flux:button variant="primary" href="{{ route('loans.create', $book->id) }}">
                        Pinjam
                    </flux:button>
                @endauth

            </div>

        </div>

    </flux:card>
</div>
