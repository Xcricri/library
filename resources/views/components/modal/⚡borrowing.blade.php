<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Models\Book;
use App\Models\Borrowing;

new class extends Component {
    public Book $book;
    public bool $showBorrowModal = false;
    public int $duration = 14;

    // Open Modal
    public function openBorrowModal()
    {
        $this->showBorrowModal = true;
    }

    public function save()
    {
        // Create borrowing record and decrement stock
        DB::transaction(function () use (&$message) {
            // Lock the book row for update
            $book = Book::lockForUpdate()->findOrFail($this->book->id);

            // If book already borrowed
            $alreadyBorrowed = Borrowing::where('user_id', auth()->id())
                ->where('book_id', $this->book->id)
                ->where('status', 'borrowed')
                ->exists();

            if ($alreadyBorrowed) {
                $message = 'You have already borrowed this book.';
                return;
            }

            if ($book->stock <= 0) {
                $message = 'Book is not available.';
                return;
            }

            Borrowing::create([
                'user_id' => auth()->id(),
                'book_id' => $this->book->id,
                'borrowed_at' => now(),
                'returned_at' => null,
                'due_date' => now()->addDays($this->duration),
                'status' => 'borrowed',
            ]);

            $book->decrement('stock');

            $message = 'Book borrowed successfully.';
        });

        $this->showBorrowModal = false;

        session()->flash('message', $message);

        $this->dispatch('borrowed', $message);
    }

    public function render()
    {
        return $this->view();
    }
};
?>

<div>
    <flux:button variant="primary" icon="book-open" class="w-full" wire:click="openBorrowModal">
        Borrow Book
    </flux:button>

    <flux:modal wire:model="showBorrowModal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg"> Borrow Book </flux:heading>

            <flux:text> Borrow <strong>{{ $book->title }}</strong> </flux:text>

            <flux:select label="Borrow Duration" wire:model="duration">
                <option value="1">1 Days</option>
                <option value="7">7 Days</option>
                <option value="14">14 Days</option>
                <option value="30">30 Days</option>
            </flux:select>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$set('showBorrowModal', false)">
                    Cancel
                </flux:button>
                <flux:button variant="primary" wire:click="save">
                    Confirm Borrow
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
