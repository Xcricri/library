<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

use App\Models\Loan;

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
    public function loans()
    {
        $query = Loan::with(['book.genres'])->where('user_id', auth()->id());

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
            'loans' => $this->loans(),
        ])
            ->layout('layouts::dashboard')
            ->title('List Buku Pinjaman');
    }
};
?>

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Tabel Buku Pinjaman

            <flux:text>
                Daftar buku yang dipinjam
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="w-full md:max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari buku..."
                size="sm" />
        </div>

        <div class="w-full sm:w-48">
            <flux:select wire:model.live="statusFiltered" placeholder="Pilih Status" size="sm">
                <flux:select.option value="all">Semua</flux:select.option>
                <flux:select.option value="borrowed">Sedang Dipinjam</flux:select.option>
                <flux:select.option value="returned">Telah Dipulangkan</flux:select.option>
                <flux:select.option value="overdue">Lewat Dipulangkan</flux:select.option>
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
                <flux:table.column>Nomor</flux:table.column>
                <flux:table.column>Cover</flux:table.column>
                <flux:table.column>Buku</flux:table.column>
                <flux:table.column>Genre</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($loans as $loan)
                    <flux:table.row>
                        <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                        <flux:table.cell>
                            <img src="{{ Storage::url('covers/' . $loan->book->cover) }}" alt="{{ $loan->book->title }}"
                                class="w-9 aspect-2/3 rounded">
                        </flux:table.cell>
                        <flux:table.cell>{{ $loan->book->title }}</flux:table.cell>
                        <flux:table.cell>
                            @foreach ($loan->book->genres as $genre)
                                {{ $genre->name }}
                            @endforeach
                        </flux:table.cell>
                        <flux:table.cell class="py-0">
                            @if ($loan->status === 'borrowed')
                                <flux:badge color="blue" size="sm">Baca Buku</flux:badge>
                            @elseif($loan->status === 'overdue')
                                <flux:badge color="red" class="pointer" size="sm"
                                    href="{{ route('user.books.controller', $loan->id) }}" wire:navigate>Kembalikan
                                    Buku
                                </flux:badge>
                            @elseif($loan->status === 'returned')
                                <flux:badge color="gray" size="sm">Buku telah dikembalikan</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
