<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

use App\Models\Borrowing;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $statusFiltered = 'borrowed';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFiltered()
    {
        $this->resetPage();
    }

    // Search book
    #[Computed]
    public function borrowings()
    {
        $query = Borrowing::with(['book.genres'])->where('user_id', auth()->id());

        $query->when($this->search, function ($q) {
            $q->whereHas('book', fn($q) => $q->where('title', 'like', '%' . $this->search . '%'));
        });

        $query->when($this->statusFiltered && $this->statusFiltered !== 'all', function ($q) {
            $q->where('status', $this->statusFiltered);
        });

        return $query->paginate(10);
    }

    public function render()
    {
        return $this->view([
            'borrowings' => $this->borrowings(),
        ])
            ->layout('layouts::dashboard')
            ->title('Borrowed Books');
    }
};
?>

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table borrowed

            <flux:text> List of Borrowed Books </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div
        class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
    >
        <div class="w-full md:max-w-sm">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Cari buku..."
                size="sm"
            />
        </div>

        <div class="w-full sm:w-48">
            <flux:select
                wire:model.live="statusFiltered"
                placeholder="Pilih Status"
                size="sm"
            >
                <flux:select.option value="all">All</flux:select.option>
                <flux:select.option value="borrowed">
                    Borrowed</flux:select.option
                >
                <flux:select.option value="returned">
                    Returned</flux:select.option
                >
                <flux:select.option value="overdue">Overdue</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Flash message --}}
    @if (session()->has('message'))
        <flux:callout variant="success">
            {{ session('message') }}
        </flux:callout>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Number</flux:table.column>
                <flux:table.column>Cover</flux:table.column>
                <flux:table.column>Book</flux:table.column>
                <flux:table.column>Genre</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($borrowings as $borrowing)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $loop->iteration }}</flux:table.cell
                        >
                        <flux:table.cell>
                            <img
                                src="{{ Storage::url('covers/' . $borrowing->book->cover) }}"
                                alt="{{ $borrowing->book->title }}"
                                class="aspect-2/3 w-9 rounded"
                            />
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $borrowing->book->title }}</flux:table.cell
                        >
                        <flux:table.cell>
                            @foreach ($borrowing->book->genres as $genre)
                                {{ $genre->name }}
                            @endforeach
                        </flux:table.cell>
                        <flux:table.cell class="py-0">
                            @if ($borrowing->status === 'borrowed')
                                <flux:button
                                    variant="danger"
                                    class="pointer"
                                    size="sm"
                                    href="{{ route('user.books.return', $borrowing->id) }}"
                                    >Return Book
                                </flux:button>
                            @elseif ($borrowing->status === 'overdue')
                                <flux:badge color="red" size="sm"
                                    >Book is overdue</flux:badge
                                >
                            @elseif ($borrowing->status === 'returned')
                                <flux:badge color="green" size="sm"
                                    >Book has been returned</flux:badge
                                >
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            No books found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
