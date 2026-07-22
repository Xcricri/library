<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

use App\Models\Review;
use App\Models\Book;

new class extends Component {
    use WithPagination;

    public Book $book;
    public $rating = 0;

    #[Validate('required|min:10|max:1000')]
    public $comment;

    public $user_id;
    public $book_id;

    // Get reviews
    #[Computed]
    public function reviews()
    {
        return Review::where('book_id', $this->book->id)->paginate(5);
    }

    // Mount book
    public function mount(Book $book)
    {
        $this->book = $book;
    }

    // Set rating
    public function ratingBook($ratingBook)
    {
        $this->rating = $ratingBook;
    }

    // Save review
    public function save()
    {
        $message = '';

        $this->validate();

        Review::updateOrcreate([
            'user_id' => auth()->id(),
            'book_id' => $this->book->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->reset('rating', 'comment');

        $message = 'Review successfully.';

        session()->flash('message', $message);

        $this->dispatch('review-saved', $message);
    }

    // Delete review
    public function delete($id)
    {
        Review::findOrFail($id)->delete();
    }

    public function render()
    {
        return $this->view([
            'reviews' => $this->reviews,
        ]);
    }
};
?>

<div class="mx-auto space-y-6 sm:space-y-8">
    {{-- Form Review --}}
    <flux:card class="p-4 sm:p-5">
        <div class="max-w-full space-y-5">
            {{-- Rating Stars --}}
            <div>
                <flux:heading size="sm" class="mb-2 font-medium tracking-wide text-zinc-500 dark:text-zinc-400">
                    Your Rating
                </flux:heading>

                <div class="flex flex-wrap items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <flux:icon.star variant="solid"
                            class="p-1 w-7 h-7 sm:w-8 sm:h-8 hover:text-yellow-300 {{ $rating >= $i ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}  stroke-current"
                            wire:click="ratingBook({{ $i }})" />
                    @endfor
                </div>
            </div>

            {{-- Textarea Comment --}}
            <div>
                <flux:textarea wire:model="comment" rows="4" placeholder="Write your review..." class="w-full" />
            </div>

            {{-- Action Button --}}
            <div class="flex justify-end pt-1">
                <flux:button variant="primary" wire:click="save" class="w-full shadow-sm sm:w-auto">
                    Submit Review
                </flux:button>
            </div>
        </div>
    </flux:card>

    {{-- List Review --}}
    <div class="space-y-4">
        <flux:heading size="lg" class="px-1 font-semibold">
            Reviews
        </flux:heading>

        @forelse ($reviews as $review)
            <flux:card class="space-y-3 p-4 shadow-sm sm:p-5">
                {{-- Review Header --}}
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-0.5">
                        <flux:heading size="sm"
                            class="flex items-center gap-2 font-medium text-zinc-900 dark:text-zinc-50">
                            <flux:avatar src="{{ url('avatars/' . $review->user->avatar) }}" size="sm" />
                            {{ $review->user->name }}
                        </flux:heading>
                        <flux:text size="xs" class="block text-zinc-400 dark:text-zinc-500">
                            {{ $review->created_at->diffForHumans() }}
                        </flux:text>
                    </div>

                    {{-- Star Rating --}}
                    <div class="flex shrink-0 gap-0.5 self-start sm:gap-1 sm:self-center">
                        @for ($i = 1; $i <= 5; $i++)
                            <flux:icon.star variant="solid"
                                class="w-5 h-5
                                {{ $review->rating >= $i ? 'text-yellow-400' : 'text-zinc-600 ' }} " />
                        @endfor

                        @auth
                            @if (Auth::id() === $review->user_id)
                                <flux:modal.trigger name="delete-review">
                                    <flux:icon.trash class="h-5 w-5 cursor-pointer text-zinc-400 hover:text-red-500" />
                                </flux:modal.trigger>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Review Text --}}
                <flux:text
                    class="pt-1 text-justify text-sm leading-7 wrap-break-word whitespace-pre-line text-zinc-600 sm:text-base dark:text-zinc-300">
                    {{ $review->comment }}
                </flux:text>
            </flux:card>

            {{-- Modal --}}
            <flux:modal name="delete-review" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Delete Review</flux:heading>
                        <flux:text class="mt-2">Are you sure you want to delete this review?
                        </flux:text>
                    </div>
                    <flux:button type="submit" variant="primary" color="red" class="w-full"
                        wire:click="delete({{ $review->id }})">Delete Review</flux:button>
                </div>
            </flux:modal>
        @empty
            {{-- Empty State --}}
            <div
                class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50/50 py-12 text-center dark:border-zinc-800 dark:bg-zinc-900/20">
                <flux:icon name="chat-bubble-left-right" class="mx-auto mb-2 size-8 text-zinc-300 dark:text-zinc-700" />
                <flux:text size="sm" class="text-zinc-400 dark:text-zinc-500">
                    There are no reviews for this book yet.
                </flux:text>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="pt-2">
            <flux:pagination :paginator="$reviews" />
        </div>

    </div>

</div>
