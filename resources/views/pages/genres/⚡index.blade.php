<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Genre;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    // Search genre
    #[Computed]
    public function genres()
    {
        return Genre::query()
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(5);
    }

    // Delete genre
    public function delete($id)
    {
        Genre::destroy($id);

        session()->flash('message', 'Genre deleted successfully.');
    }

    public function render()
    {
        return $this->view([
            'genres' => $this->genres,
        ])
            ->layout('layouts::dashboard')
            ->title('Index Genre');
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table Genre

            <flux:text>
                Daftar Genre di perpustakaan
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="w-full md:max-w-sm">
            <flux:input icon="magnifying-glass" placeholder="Cari Genre..." wire:model.live.debounce.300ms="search"
                size="sm" />
        </div>

        <flux:button href="{{ route('genres.create') }}" wire:navigate variant="primary" size="sm">
            Tambah Genre
        </flux:button>

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
                <flux:table.column>Nama genre</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($genres as $genre)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $genres->firstItem() + $loop->index }}
                        </flux:table.cell>

                        <flux:table.cell class="font-medium">
                            {{ $genre->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="yellow" size="sm" href="{{ route('genres.update', $genre->id) }}"
                                wire:navigate class="cursor-pointer">
                                Edit
                            </flux:badge>

                            <flux:badge color="red" size="sm" class="cursor-pointer"
                                wire:click="delete({{ $genre->id }})">
                                Delete
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center py-6 text-gray-500">
                            No genres found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </div>

    {{-- Pagination --}}
    <div>
        <flux:pagination :paginator="$genres" />
    </div>

</div>
