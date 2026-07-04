<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    public function search()
    {
        $this->resetPage();
    }

    // Search category
    #[Computed]
    public function categories()
    {
        return Category::query()
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(5);
    }

    // Delete category
    public function delete($id)
    {
        Category::destroy($id);

        session()->flash('message', 'Category deleted successfully.');
    }

    public function render()
    {
        return $this->view([
            'categories' => $this->categories,
        ])
            ->layout('layouts::dashboard')
            ->title('Index Category');
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table Category

            <flux:text>
                Daftar Category di perpustakaan
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="w-full md:max-w-sm">
            <flux:input icon="magnifying-glass" placeholder="Cari Category..." wire:model.live.debounce.300ms="search"
                size="sm" />
        </div>

        <flux:button href="{{ route('categories.create') }}" wire:navigate variant="primary" size="sm">
            Tambah Category
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
                <flux:table.column>Nama Kategori</flux:table.column>
                <flux:table.column>Deskripsi Kategori</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($categories as $category)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $categories->firstItem() + $loop->index }}
                        </flux:table.cell>

                        <flux:table.cell class="font-medium">
                            {{ $category->name }}
                        </flux:table.cell>

                        <flux:table.cell class="font-medium">
                            {{ $category->description }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="yellow" size="sm"
                                href="{{ route('categories.update', $category->id) }}" wire:navigate
                                class="cursor-pointer">
                                Edit
                            </flux:badge>

                            <flux:modal.trigger name="delete-category-{{ $category->id }}">
                                <flux:badge color="red" size="sm" class="cursor-pointer">
                                    Delete
                                </flux:badge>
                            </flux:modal.trigger>
                        </flux:table.cell>
                    </flux:table.row>

                    {{-- Delete Category Modal --}}
                    <flux:modal name="delete-category-{{ $category->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Delete Category</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to delete this category? This action
                                    cannot be undone.
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary" wire:click="delete({{ $category->id }})">
                                    Delete
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center py-6 text-gray-500">
                            No categories found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </div>


    {{-- Pagination --}}
    <div>
        <flux:pagination :paginator="$categories" />
    </div>

</div>
