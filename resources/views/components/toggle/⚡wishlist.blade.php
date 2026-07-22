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
            <flux:icon.heart
                variant="solid"
                class="h-6 w-6 fill-current text-red-500"
            />
        @else
            <!-- Outline Heart Icon -->
            <flux:icon.heart
                variant="solid"
                class="h-6 w-6 text-gray-200 hover:text-red-500"
            />
        @endif
    </flux:badge>
</div>
