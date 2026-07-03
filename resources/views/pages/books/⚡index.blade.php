<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    // Search book
    #[Computed]
    public function books()
    {
        return Book::query()
            ->when($this->search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%')->orWhere('author', 'like', '%' . $search . '%');
            })
            ->paginate(5);
    }

    // Delete book
    public function delete($id)
    {
        $book = Book::find($id);
        Book::destroy($id);

        // If cover exists delete cover
        if ($book->cover) {
            // delete old image
            Storage::disk('public')->delete('covers/' . $book->cover);
        }

        session()->flash('message', 'Book deleted successfully.');
    }

    public function render()
    {
        return $this->view([
            'books' => $this->books,
        ])
            ->layout('layouts::dashboard')
            ->title('Index Book');
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table Buku

            <flux:text>
                Daftar Buku di perpustakaan
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="w-full md:max-w-sm">
            <flux:input icon="magnifying-glass" placeholder="Cari buku..." wire:model.live.debounce.300ms="search"
                size="sm" />
        </div>

        <flux:button href="{{ route('books.create') }}" wire:navigate variant="primary" size="sm">
            Tambah Buku
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
                <flux:table.column>Judul</flux:table.column>
                <flux:table.column>Penulis</flux:table.column>
                <flux:table.column>Tahun Terbit</flux:table.column>
                <flux:table.column>Cover</flux:table.column>
                <flux:table.column class="text-right">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($books as $book)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $books->firstItem() + $loop->index }}
                        </flux:table.cell>

                        <flux:table.cell class="font-medium">
                            {{ $book->title }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $book->author }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $book->released_at }}
                        </flux:table.cell>

                        <flux:table.cell class="py-1">
                            @if ($book->cover)
                                <flux:avatar src="{{ Storage::url('covers/' . $book->cover) }}" class="w-9 h-9" />
                            @else
                                <flux:avatar class="w-9 h-9" />
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="yellow" size="sm" href="{{ route('books.update', $book->id) }}"
                                wire:navigate class="cursor-pointer">
                                Edit
                            </flux:badge>

                            <flux:badge color="red" size="sm" class="cursor-pointer"
                                wire:click="delete({{ $book->id }})">
                                Delete
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-6 text-gray-500">
                            No books found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </div>

    {{-- Pagination --}}
    <div>
        <flux:pagination :paginator="$books" />
    </div>

</div>
