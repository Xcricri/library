<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

use App\Models\Loan;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $statusFiltered = 'borrowed';

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

    // Search loans
    #[Computed]
    public function loans()
    {
        return Loan::query()
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
            'loans' => $this->loans(),
        ])
            ->layout('layouts::dashboard')
            ->title('Index Loans');
    }
};
?>

<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table Pinjaman

            <flux:text>
                Daftar Pinjaman di perpustakaan
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="w-full md:max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari buku..."
                size="sm" />
        </div>

        <div class="w-full md:max-w-sm flex items-center gap-4">
            <flux:select wire:model.live="statusFiltered" placeholder="Choose status..." size="sm">
                <flux:select.option value="all">Semua</flux:select.option>
                <flux:select.option value="borrowed">Dipinjam</flux:select.option>
                <flux:select.option value="returned">Dikembalikan</flux:select.option>
                <flux:select.option value="overdue">Terlambat</flux:select.option>
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
                <flux:table.column>Nomor</flux:table.column>
                <flux:table.column>Buku</flux:table.column>
                <flux:table.column>Tanggal Pinjaman</flux:table.column>
                <flux:table.column>Tenggat Waktu</flux:table.column>
                <flux:table.column>Tanggal Dikembalikan</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Denda</flux:table.column>
            </flux:table.columns>

            @forelse ($loans as $loan)
                <flux:table.rows>
                    <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                    <flux:table.cell>{{ $loan->book->title }}</flux:table.cell>
                    <flux:table.cell>{{ Carbon::parse($loan->borrowed_at)->format('d-Y-M') }}</flux:table.cell>
                    <flux:table.cell>{{ Carbon::parse($loan->due_date)->format('d-Y-M') }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $loan->returned_at ? Carbon::parse($loan->returned_at)->format('d-Y-M') : '-' }}
                    </flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge color="green" size="sm">{{ $loan->status }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>Rp {{ number_format($loan->fine, 0, ',', '.') }}</flux:table.cell>
                </flux:table.rows>
            @empty
                <flux:table.rows>
                    <flux:table.cell colspan="7" class="text-center">
                        Tidak ada pinjaman yang ditemukan.
                    </flux:table.cell>
                </flux:table.rows>
            @endforelse
        </flux:table>
    </div>
</div>
