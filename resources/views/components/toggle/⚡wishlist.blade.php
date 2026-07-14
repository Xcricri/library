<?php

use Livewire\Component;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $book;

    public $isWishListed = false;

    public function mount($book)
    {
        $this->book = $book;

        if (Auth::check()) {
            $this->isWishListed = WishList::where('user_id', Auth::id())->where('book_id', $book->id)->exists();
        }
    }

    public function toggleList()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isWishListed) {
            Wishlist::where('user_id', Auth::id())->where('book_id', $this->book->id)->delete();
            $this->isWishListed = false;
        } else {
            WishList::create([
                'user_id' => Auth::id(),
                'book_id' => $this->book->id,
            ]);
            $this->isWishListed = true;
        }

        $this->dispatch('wishlist-updated');
    }

    public function render()
    {
        return $this->view();
    }
};
?>

<div>
    <flux:badge wire:click="toggleList" size="sm" color="gray">
        @if ($isWishListed)
            <!-- Filled Heart Icon -->
            <svg xmlns="http://w3.org" class="h-6 w-6 text-red-500 fill-current" viewBox="0 0 24 24">
                <path
                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
            </svg>
        @else
            <!-- Outline Heart Icon -->
            <svg xmlns="http://w3.org" class="h-6 w-6 text-gray-200 hover:text-red-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        @endif
    </flux:badge>
</div>
