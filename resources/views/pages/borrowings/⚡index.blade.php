<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

use App\Models\Borrowing;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $statusFiltered = 'all';

    #[Url]
    public $date = '';

    public function search()
    {
        $this->resetPage();
    }

    public function statusFiltered()
    {
        $this->resetPage();
    }

    // Search borrowings
    #[Computed]
    public function borrowings()
    {
        return Borrowing::query()
            ->with('book')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFiltered !== 'all', function ($query) {
                $query->where('status', $this->statusFiltered);
            })
            ->when($this->date, function ($query) {
                $query->whereDate('borrowed_at', $this->date);
            })
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return $this->view([
            'borrowings' => $this->borrowings(),
        ])
            ->layout('layouts::dashboard')
            ->title('List Borrowings');
    }
};
?>

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table Borrowings

            <flux:text> List of Borrowings in the Library </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="w-full md:max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari buku..."
                size="sm" />
        </div>

        <div class="flex w-full items-center gap-4 md:max-w-sm">
            <flux:select wire:model.live="statusFiltered" placeholder="Choose status..." size="sm">
                <flux:select.option value="all">All</flux:select.option>
                <flux:select.option value="borrowed">
                    Borrowed</flux:select.option>
                <flux:select.option value="returned">
                    Returned</flux:select.option>
            </flux:select>

            <div class="w-full sm:w-44">
                <flux:input type="date" wire:model.live.debounce.300ms="date" size="sm" />
            </div>
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
                <flux:table.column>Book</flux:table.column>
                <flux:table.column>Borrow Date</flux:table.column>
                <flux:table.column>Due Date</flux:table.column>
                <flux:table.column>Return Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Fine</flux:table.column>
            </flux:table.columns>

            @forelse ($borrowings as $borrowing)
                <flux:table.rows>
                    <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $borrowing->book->title }}</flux:table.cell>
                    <flux:table.cell>
                        {{ Carbon::parse($borrowing->borrowed_at)->format('d-Y-M') }}</flux:table.cell>
                    <flux:table.cell>
                        {{ Carbon::parse($borrowing->due_date)->format('d-Y-M') }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $borrowing->returned_at ? Carbon::parse($borrowing->returned_at)->format('d-Y-M') : '-' }}
                    </flux:table.cell>
                    <flux:table.cell class="py-0">
                        @if ($borrowing->status === 'borrowed')
                            <flux:badge color="green" size="sm">
                                Book is borrowed
                            </flux:badge>
                        @elseif ($borrowing->status === 'returned')
                            <flux:badge color="blue" size="sm">Book has been returned</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        Rp {{ number_format($borrowing->fine, 0, ',', '.') }}</flux:table.cell>
                </flux:table.rows>
            @empty
                <flux:table.rows>
                    <flux:table.cell colspan="7" class="text-center">
                        No borrowings found.
                    </flux:table.cell>
                </flux:table.rows>
            @endforelse
        </flux:table>
    </div>

    {{-- Pagination --}}
    <div>
        <flux:pagination :paginator="$borrowings" />
    </div>
</div>
