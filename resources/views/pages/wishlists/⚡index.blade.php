<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Wishlist;

new class extends Component {
    use WithPagination;

    // Wishlists
    public function wishlists()
    {
        $query = Wishlist::query();
        $query->where('user_id', auth()->id());
        return $query->paginate(10);
    }

    // Remove wishlist
    public function remove($id)
    {
        Wishlist::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        session()->flash('message', 'Book removed from wishlist.');
    }

    public function render()
    {
        return $this->view([
            'wishlists' => $this->wishlists(),
        ])
            ->layout('layouts::dashboard')
            ->title('Wishlist Books');
    }
};
?>

<div>
    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table Wishlist

            <flux:text> List of Wishlist Books </flux:text>
        </flux:heading>
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
                @forelse ($wishlists as $wishlist)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $loop->iteration }}</flux:table.cell>
                        <flux:table.cell>
                            <img src="{{ Storage::url('covers/' . $wishlist->book->cover) }}"
                                alt="{{ $wishlist->book->title }}" class="aspect-2/3 w-9 rounded" />
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $wishlist->book->title }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $wishlist->book->genres->pluck('name')->join(', ') }}
                        </flux:table.cell>
                        <flux:table.cell class="py-0">

                            <flux:badge color="green" size="sm"
                                href="{{ route('public.books.view', $wishlist->book->slug) }}" wire:navigate>
                                View
                            </flux:badge>

                            <flux:badge color="red" size="sm" wire:click="remove({{ $wishlist->id }})">
                                Remove
                            </flux:badge>
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

    {{-- Pagination --}}
    <div>
        <flux:pagination :paginator="$wishlists" />
    </div>
</div>
