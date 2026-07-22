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
        $this->book = Book::with(['genres', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->book->published_at = Carbon::parse($this->book->published_at);
    }

    // Refresh book data after borrowing
    #[On('borrowed')]
    #[On('review-saved')]
    public function refreshBook($message)
    {
        $this->book = Book::with(['genres', 'category'])
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

<div class="mx-auto max-w-full">
    {{-- Flash message --}}
    @if (session()->has('message'))
        <flux:callout variant="success" class="animate-fade-in mb-6 shadow-sm">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div
        class="overflow-hidden rounded-xl border border-zinc-100 bg-zinc-100 shadow-md dark:border-zinc-800 dark:bg-zinc-800"
    >
        <div
            class="grid grid-cols-1 gap-6 p-5 sm:p-8 md:grid-cols-3 lg:grid-cols-4 lg:gap-10"
        >
            {{-- Left side: Cover & Actions --}}
            <div class="md:col-span-1">
                {{-- Cover --}}
                <div class="mx-auto w-full max-w-xs sm:max-w-sm lg:max-w-full">
                    <div
                        class="group relative overflow-hidden rounded-xl shadow-lg"
                    >
                        <img
                            src="{{ Storage::url('covers/' . $book->cover) }}"
                            alt="{{ $book->title }}"
                            class="aspect-2/3 w-full object-cover"
                        />
                    </div>
                </div>

                {{-- Borrowing Action --}}
                <div class="mt-5 w-full">
                    @auth
                        @if (auth()->user()->hasRole('member'))
                            @if (auth()->user()->hasBorrowed($book))
                                <div
                                    class="flex items-center justify-center gap-2 rounded-lg border border-red-100 bg-red-50 p-3 text-sm font-medium text-red-600 dark:border-red-900/30 dark:bg-red-950/30 dark:text-red-400"
                                >
                                    <flux:icon
                                        name="exclamation-triangle"
                                        class="size-4 shrink-0"
                                    />

                                    <span class="text-center">
                                        You have borrowed this book.
                                    </span>
                                </div>
                            @else
                                <livewire:modal.book-borrowing :book="$book" />
                            @endif
                        @endif
                    @else
                        <flux:button
                            variant="primary"
                            icon="book-open"
                            class="w-full shadow-sm"
                            href="{{ route('login') }}"
                        >
                            Login to Borrow
                        </flux:button>
                    @endauth
                </div>
            </div>

            {{-- Right side: Information Details --}}
            <div class="md:col-span-2 lg:col-span-3">
                <div class="space-y-5">
                    {{-- Title & Author --}}
                    <div class="space-y-1">
                        <flux:heading
                            size="lg"
                            class="font-bold tracking-tight text-zinc-900 dark:text-zinc-50"
                        >
                            {{ $book->title }}
                        </flux:heading>

                        <flux:text
                            class="small"
                            class="text-primary text-base font-semibold tracking-wide sm:text-lg"
                        >
                            {{ $book->author }}
                        </flux:text>
                    </div>

                    {{-- Grup Badge --}}
                    <div class="space-y-3 pt-1 text-sm">
                        {{-- Category --}}
                        @if ($book->category)
                            <div
                                class="grid grid-cols-[80px_1fr] items-center gap-2"
                            >
                                <span
                                    class="shrink-0 text-xs font-semibold tracking-wider uppercase"
                                >
                                    Category:
                                </span>
                                <div>
                                    <flux:badge
                                        variant="neutral"
                                        size="sm"
                                        class="rounded-md"
                                    >
                                        {{ $book->category->name }}
                                    </flux:badge>
                                </div>
                            </div>
                        @endif

                        {{-- Genres --}}
                        @if ($book->genres->isNotEmpty())
                            <div
                                class="grid grid-cols-[80px_1fr] items-start gap-2"
                            >
                                <span
                                    class="shrink-0 pt-0.5 text-xs font-semibold tracking-wider uppercase"
                                >
                                    Genre:
                                </span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($book->genres as $genre)
                                        <flux:badge
                                            variant="brand"
                                            size="sm"
                                            class="rounded-md"
                                        >
                                            {{ $genre->name }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div
                            class="grid grid-cols-[80px_1fr] items-start gap-2"
                        >
                            <span
                                class="shrink-0 pt-0.5 text-xs font-semibold tracking-wider uppercase"
                            >
                                Isbn:
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                <flux:badge
                                    variant="brand"
                                    size="sm"
                                    class="rounded-md"
                                >
                                    {{ $book->isbn }}
                                </flux:badge>
                            </div>
                        </div>

                        {{-- Publisher & Publication Date --}}
                        <div
                            class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 sm:text-sm dark:text-zinc-400"
                        >
                            <span
                                class="font-medium"
                                >{{ $book->publisher_name }}</span
                            >
                            <span class="text-zinc-300 dark:text-zinc-700"
                                >•</span
                            >
                            <span
                                >{{ Carbon::parse($book->published_at)->format('Y') }}</span
                            >
                        </div>
                    </div>

                    <flux:separator class="my-4" />

                    {{-- Synopsis --}}
                    <div class="space-y-4">
                        <flux:heading
                            size="lg"
                            class="font-semibold text-zinc-800 dark:text-zinc-200"
                        >
                            Synopsis
                        </flux:heading>

                        <flux:text
                            class="align-justify text-justify text-sm leading-7 whitespace-pre-line text-zinc-600 sm:text-base dark:text-zinc-300"
                        >
                            {{ $book->description }}
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>

        {{-- Review Section --}}
        <div class="space-y-4 p-5 sm:p-8">
            <livewire:card.reviews :book="$book" />
        </div>
    </div>
</div>
