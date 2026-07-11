<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\BookForm;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Book;

new class extends Component {
    use WithFileUploads;

    public BookForm $form;

    public $genres;
    public $categories;

    // Mount data
    public function mount()
    {
        $this->genres = Genre::all();
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->form->validate();

        $imageName = null;

        // Save Cover
        if ($this->form->cover) {
            $imageName = $this->form->cover->hashName();
            $this->form->cover->storeAs('covers', $imageName, 'public');
        }

        $create_slug = Str::slug($this->form->title);

        // Create book
        $book = Book::create([
            'slug' => $create_slug,
            'title' => $this->form->title,
            'author' => $this->form->author,
            'publisher_name' => $this->form->publisher_name,
            'cover' => $imageName,
            'isbn' => $this->form->isbn,
            'stock' => $this->form->stock,
            'description' => $this->form->description,
            'published_at' => $this->form->published_at,
        ]);

        // Relations
        $book->categories()->sync($this->form->category_ids ?? []);
        $book->genres()->sync($this->form->genre_ids ?? []);

        session()->flash('success', 'Book added successfully.');

        redirect()->route('staff.books.index');
    }

    public function render()
    {
        return $this->view([
            'genres' => $this->genres,
            'categories' => $this->categories,
        ])
            ->layout('layouts::dashboard')
            ->title('Add Book');
    }
};
?>

<div class="mx-auto max-w-7xl">
    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl"> Add Book </flux:heading>

                <flux:text class="mt-2">
                    Fill in the information below to create a new book.
                </flux:text>
            </div>

            <!-- Book Cover -->
            <div class="space-y-4">
                <flux:label>Book Cover</flux:label>
                <div>
                    @if ($this->form->cover)
                        <div class="aspect-2/3 w-48 overflow-hidden rounded-lg">
                            <img src="{!! $this->form->cover->temporaryUrl() !!}" alt="Book Cover" class="h-full w-full object-cover" />
                        </div>
                    @endif
                    <flux:input type="file" accept="image/*" wire:model="form.cover" />

                    <flux:text size="sm" class="mt-2">
                        JPG, PNG or WEBP. Maximum 2MB.
                    </flux:text>

                    @error('form.cover')
                        <flux:text class="mt-1 text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>
            </div>

            <div class="space-y-8">
                {{-- Book Information --}}
                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Book Title --}}
                    <div class="space-y-2 md:col-span-2">
                        <flux:label>Book Title</flux:label>

                        <flux:input wire:model="form.title" placeholder="Enter book title" />

                        @error('form.title')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Author --}}
                    <div class="space-y-2">
                        <flux:label>Author</flux:label>

                        <flux:input wire:model="form.author" placeholder="Enter author name" />

                        @error('form.author')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Publisher --}}
                    <div class="space-y-2">
                        <flux:label>Publisher</flux:label>

                        <flux:input wire:model="form.publisher_name" placeholder="Enter publisher name" />

                        @error('form.publisher_name')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Publication Date --}}
                    <div class="space-y-2">
                        <flux:label>Publication Date</flux:label>

                        <flux:input type="date" wire:model="form.published_at" />

                        @error('form.published_at')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Isbn --}}
                    <div class="space-y-2">
                        <flux:label>Isbn</flux:label>

                        <flux:input type="text" wire:model="form.isbn" placeholder="Enter ISBN" />

                        @error('form.isbn')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div class="space-y-2">
                        <flux:label>Book Stock</flux:label>

                        <flux:input type="number" wire:model="form.stock" placeholder="Enter book stock" />

                        @error('form.stock')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Book Description --}}
                    <div class="space-y-2 md:col-span-2">
                        <flux:label>Book Description</flux:label>

                        <flux:textarea rows="5" wire:model="form.description"
                            placeholder="Enter book description..." />

                        @error('form.description')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </div>

                {{-- Genre & Category --}}
                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Genre --}}
                    <div class="space-y-4 rounded-lg p-5">
                        <flux:label class="font-semibold">
                            Book Genre
                        </flux:label>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($genres as $genre)
                                <label class="flex cursor-pointer items-center gap-2">
                                    <flux:checkbox wire:model="form.genre_ids" value="{{ $genre->id }}" />

                                    <span class="text-sm">
                                        {{ $genre->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('form.genre_ids')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="space-y-4 rounded-lg p-5">
                        <flux:label class="font-semibold">
                            Book Category
                        </flux:label>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($categories as $category)
                                <label class="flex cursor-pointer items-center gap-2">
                                    <flux:checkbox wire:model="form.category_ids" value="{{ $category->id }}" />

                                    <span class="text-sm">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('form.category_ids')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">
                <flux:button variant="primary" type="submit">
                    Buat Buku
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
