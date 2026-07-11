<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

use App\Models\Book;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $date = '';

    #[Url]
    public $statusFilter = 'active';

    public function search()
    {
        $this->resetPage();
    }

    public function statusFilter()
    {
        $this->resetPage();
    }

    // Search book
    #[Computed]
    public function books()
    {
        return Book::query()
            ->when($this->statusFilter === 'trashed', function ($q) {
                $q->onlyTrashed();
            })
            ->when($this->statusFilter === 'all', function ($q) {
                $q->withTrashed();
            })
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%")->orWhere('author', 'like', "%{$this->search}%");
            })
            ->when($this->date, function ($q) {
                $q->where('published_at', $this->date);
            })
            ->paginate(5);
    }

    // softDelete book method
    public function softDelete($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        session()->flash('message', 'Book deleted successfully.');
    }

    // forceDelete method
    public function forceDelete($id)
    {
        $book = Book::withTrashed()->find($id);
        $book->forceDelete();

        // If cover exists delete cover
        if ($book->cover) {
            // delete old image
            Storage::disk('public')->delete('covers/' . $book->cover);
        }

        // If ebook file exists delete it
        if ($book->ebook_file) {
            // delete old ebook file
            Storage::disk('public')->delete('ebooks/' . $book->ebook_file);
        }

        session()->flash('message', 'Book permanently deleted.');
    }

    // restore method
    public function restore($id)
    {
        $book = Book::withTrashed()->find($id);
        $book->restore();

        session()->flash('message', 'Book restored successfully.');
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
            Table Books

            <flux:text> List of Books in the Library </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="grid grid-cols-1 gap-3 sm:flex sm:max-w-3xl sm:flex-1 sm:items-center">
            <div class="min-w-50 sm:flex-1">
                <flux:input icon="magnifying-glass" placeholder="Search books..." wire:model.live.debounce.300ms="search"
                    size="sm" clearable />
            </div>

            <div class="w-full sm:w-40">
                <flux:select wire:model.live="statusFilter" placeholder="Select status..." size="sm">
                    <flux:select.option value="all">
                        All Status</flux:select.option>
                    <flux:select.option value="active">
                        Active</flux:select.option>
                    <flux:select.option value="trashed">
                        Trash</flux:select.option>
                </flux:select>
            </div>

            <div class="w-full sm:w-44">
                <flux:input type="date" wire:model.live.debounce.300ms="date" size="sm" />
            </div>
        </div>

        <div class="flex w-full justify-end md:w-auto">
            <flux:button href="{{ route('staff.books.create') }}" wire:navigate variant="primary" size="sm"
                class="w-full md:w-auto">
                Add Book
            </flux:button>
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
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Author</flux:table.column>
                <flux:table.column>Publisher</flux:table.column>
                <flux:table.column>Publication Date</flux:table.column>
                <flux:table.column>Book Cover</flux:table.column>
                <flux:table.column>Book Stock</flux:table.column>
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

                        <flux:table.cell> {{ $book->author }} </flux:table.cell>

                        <flux:table.cell>
                            {{ $book->publisher_name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $book->published_at }}
                        </flux:table.cell>

                        <flux:table.cell class="py-1">
                            <img src="{{ Storage::url('covers/' . $book->cover) }}" class="aspect-2/3 w-9 rounded"
                                alt="{{ $book->title }}" />
                        </flux:table.cell>

                        <flux:table.cell> {{ $book->stock }} </flux:table.cell>

                        <flux:table.cell>
                            @if ($book->trashed())
                                <flux:modal.trigger name="restore-book-{{ $book->id }}">
                                    <flux:badge color="blue" size="sm" class="cursor-pointer">
                                        Restore
                                    </flux:badge>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-forced-book-{{ $book->id }}">
                                    <flux:badge color="red" size="sm" class="cursor-pointer">
                                        Delete permanent
                                    </flux:badge>
                                </flux:modal.trigger>
                            @else
                                <flux:badge color="green" size="sm"
                                    href="{{ route('public.books.view', $book->slug) }}" wire:navigate
                                    class="cursor-pointer">
                                    view
                                </flux:badge>

                                <flux:badge color="yellow" size="sm"
                                    href="{{ route('staff.books.update', $book->id) }}" wire:navigate
                                    class="cursor-pointer">
                                    Edit
                                </flux:badge>

                                <flux:modal.trigger name="soft-delete-book-{{ $book->id }}">
                                    <flux:badge color="red" size="sm" class="cursor-pointer">
                                        Delete
                                    </flux:badge>
                                </flux:modal.trigger>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>

                    {{-- Soft Delete Book Modal --}}
                    <flux:modal name="soft-delete-book-{{ $book->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Soft Delete Book</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to soft delete this
                                    book? This action can be reversed later.
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary"
                                    wire:click="softDelete({{ $book->id }})">
                                    Delete
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>

                    {{-- Restore Book Modal --}}
                    <flux:modal name="restore-book-{{ $book->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Restore Book</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to restore this book?
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary"
                                    wire:click="restore({{ $book->id }})">
                                    Restore
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>

                    {{-- Delete Forced Book Modal --}}
                    <flux:modal name="delete-forced-book-{{ $book->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Delete Forced Book</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to delete this book?
                                    This action cannot be undone.
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary"
                                    wire:click="forceDelete({{ $book->id }})">
                                    Delete permanent
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-6 text-center text-gray-500">
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
