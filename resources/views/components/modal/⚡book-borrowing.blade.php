<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Models\Book;
use App\Models\BookBorrowing;

new class extends Component {
    public Book $book;
    public bool $showBorrowModal = false;
    public int $duration = 1;

    // Open Modal
    public function openBorrowModal()
    {
        $this->showBorrowModal = true;
    }

    // Save borrowing
    public function save()
    {
        $message = '';

        // Create borrowing record and decrement stock
        DB::transaction(function () use (&$message) {
            // Lock the book row for update
            $book = Book::lockForUpdate()->findOrFail($this->book->id);

            // If book already borrowed
            $alreadyBorrowed = BookBorrowing::where('user_id', auth()->id())
                ->where('book_id', $this->book->id)
                ->where('status', 'borrowed')
                ->exists();

            if ($alreadyBorrowed) {
                $message = 'You have already borrowed this book.';
                return;
            }

            // If Book is not available
            if ($book->stock <= 0) {
                $message = 'Book is not available.';
                return;
            }

            // Create Borrowing
            BookBorrowing::create([
                'user_id' => auth()->id(),
                'book_id' => $this->book->id,
                'borrowed_at' => now(),
                'returned_at' => null,
                'due_date' => now()->addDays($this->duration),
                'status' => 'borrowed',
            ]);

            // Decrement stock
            $book->decrement('stock');

            $message = 'Book borrowed successfully.';
        });

        $this->showBorrowModal = false;

        session()->flash('message', $message);

        // Event
        $this->dispatch('borrowed', $message);
    }

    public function render()
    {
        return $this->view();
    }
};
?>

<div>
    {{-- Button --}}
    <flux:button
        variant="primary"
        icon="book-open"
        class="w-full"
        wire:click="openBorrowModal"
    >
        Borrow Book
    </flux:button>

    {{-- Modal --}}
    <flux:modal wire:model="showBorrowModal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg"> Borrow Book </flux:heading>

            <flux:text> Borrow <strong>{{ $book->title }}</strong> </flux:text>

            <flux:select label="Borrow Duration" wire:model="duration">
                <option value="1">1 Days</option>
                <option value="3">3 Days</option>
                <option value="7">7 Days</option>
            </flux:select>

            <div class="flex justify-end gap-3">
                <flux:button
                    variant="ghost"
                    wire:click="$set('showBorrowModal', false)"
                >
                    Cancel
                </flux:button>
                <flux:button variant="primary" wire:click="save">
                    Confirm Borrow
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
