<?php

use Livewire\Component;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

new class extends Component {
    public Book $book;

    public function mount($slug)
    {
        $this->book = Book::with(['categories', 'genres'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->book->published_at = Carbon::parse($this->book->published_at);
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('View Book');
    }
};
?>

<div class="mx-auto max-w-7xl py-8">
    <flux:card class="overflow-hidden">
        <div class="grid gap-10 p-8 lg:grid-cols-3">
            {{-- Cover --}}
            <div class="space-y-5 lg:col-span-1">
                <div class="overflow-hidden rounded-2xl shadow-lg">
                    <img
                        src="{{ Storage::url('covers/' . $book->cover) }}"
                        alt="{{ $book->title }}"
                        class="aspect-3/4 w-full object-cover transition duration-300 hover:scale-105"
                    />
                </div>
            </div>

            {{-- Detail --}}
            <div class="space-y-8 lg:col-span-2">
                <div class="space-y-2">
                    <flux:heading size="xl"> {{ $book->title }} </flux:heading>

                    <flux:text class="text-primary font-medium">
                        {{ $book->author }}
                    </flux:text>

                    <flux:text class="mt-1 font-semibold">
                        {{ $book->publisher_name }}
                    </flux:text>

                    <flux:text class="mt-1 font-semibold">
                        {{ $book->published_at->format('Y') }}
                    </flux:text>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach ($book->categories as $category)
                        <flux:badge variant="neutral">
                            {{ $category->name }}
                        </flux:badge>
                    @endforeach

                    @foreach ($book->genres as $genre)
                        <flux:badge variant="brand">
                            {{ $genre->name }}
                        </flux:badge>
                    @endforeach
                </div>

                <flux:separator />

                <div class="space-y-4">
                    <flux:heading size="lg"> Synopsis </flux:heading>

                    <flux:text
                        class="leading-8 whitespace-pre-line text-zinc-600 dark:text-zinc-300"
                    >
                        {{ $book->description }}
                    </flux:text>
                </div>
            </div>
        </div>
    </flux:card>
</div>
