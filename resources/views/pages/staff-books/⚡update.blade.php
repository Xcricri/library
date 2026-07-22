<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\BookForm;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;

new class extends Component {
    use WithFileUploads;

    public BookForm $form;

    public Book $book;

    public $genres;
    public $categories;

    // Mount data
    public function mount($id)
    {
        $this->book = Book::with(['genres'])->findOrFail($id);
        $this->form->title = $this->book->title;
        $this->form->author = $this->book->author;
        $this->form->publisher_name = $this->book->publisher_name;
        $this->form->isbn = $this->book->isbn;
        $this->form->stock = $this->book->stock;
        $this->form->description = $this->book->description;
        $this->form->published_at = $this->book->published_at;
        $this->form->genre_ids = $this->book->genres()->pluck('genres.id')->unique()->toArray();
        $this->form->category_id = $this->book->category_id;
        $this->genres = Genre::all();
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->form->validate();

        $imageName = null;

        // Save cover
        if ($this->form->cover) {
            // delete old image
            Storage::disk('public')->delete('covers/' . $this->book->cover);

            // store image
            $this->form->cover->storeAs('covers', $this->form->cover->hashName(), 'public');

            // get image name
            $imageName = $this->form->cover->hashName();
        } else {
            $imageName = $this->book->cover;
        }

        // Generate slug
        $created_slug = Str::slug($this->form->title);

        // Update book
        $this->book->update([
            'slug' => $created_slug,
            'title' => $this->form->title,
            'author' => $this->form->author,
            'cover' => $imageName,
            'isbn' => $this->form->isbn,
            'description' => $this->form->description,
            'published_at' => $this->form->published_at,
            'stock' => $this->form->stock,
            'category_id' => $this->form->category_id,
        ]);

        // Relations
        $this->book->genres()->sync($this->form->genre_ids ?? []);

        session()->flash('success', 'Book updated successfully.');

        redirect()->route('staff.books.index');
    }

    public function render()
    {
        return $this->view([
            'genres' => $this->genres,
            'categories' => $this->categories,
        ])
            ->layout('layouts::dashboard')
            ->title('Update Book');
    }
};
?>

<div class="mx-auto max-w-7xl">
    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl"> Update Book </flux:heading>

                <flux:text class="mt-2"> Update book information. </flux:text>
            </div>

            <!-- Book Cover -->
            <div class="space-y-4">
                <flux:label>Book Cover</flux:label>

                <div class="flex-1">
                    @if ($this->form->cover)
                        <div class="aspect-2/3 w-48 overflow-hidden rounded-lg">
                            <img
                                src="{!! $this->form->cover->temporaryUrl() !!}"
                                alt="Cover"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    @elseif ($this->book->cover)
                        <div class="aspect-2/3 w-48 overflow-hidden rounded-lg">
                            <img
                                src="{{ Storage::url('covers/' . $this->book->cover) }}"
                                alt="Cover"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    @endif

                    <flux:input
                        type="file"
                        accept="image/*"
                        wire:model="form.cover"
                    />

                    <flux:text size="sm" class="mt-2">
                        JPG, PNG or WEBP. Maximum 2MB.
                    </flux:text>

                    @error ('form.cover')
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

                        <flux:input
                            wire:model="form.title"
                            placeholder="Enter book title"
                        />

                        @error ('form.title')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Author --}}
                    <div class="space-y-2">
                        <flux:label>Author</flux:label>

                        <flux:input
                            wire:model="form.author"
                            placeholder="Enter author name"
                        />

                        @error ('form.author')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="space-y-2">
                        <flux:label class="font-semibold">
                            Book Category
                        </flux:label>

                        <div>
                            <flux:select
                                wire:model="form.category_id"
                                placeholder="Choose category..."
                            >
                                @foreach ($categories as $category)
                                    <flux:select.option
                                        value="{{ $category->id }}"
                                    >
                                        {{ $category->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        @error ('form.category_id')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Publisher --}}
                    <div class="space-y-2">
                        <flux:label>Publisher</flux:label>

                        <flux:input
                            wire:model="form.publisher_name"
                            placeholder="Enter publisher name"
                        />

                        @error ('form.publisher_name')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Publication Date --}}
                    <div class="space-y-2">
                        <flux:label>Publication Date</flux:label>

                        <flux:input
                            type="date"
                            wire:model="form.published_at"
                        />

                        @error ('form.published_at')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Isbn --}}
                    <div class="space-y-2">
                        <flux:label>Isbn</flux:label>

                        <flux:input
                            type="text"
                            wire:model="form.isbn"
                            placeholder="Enter ISBN"
                        />

                        @error ('form.isbn')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div class="space-y-2">
                        <flux:label>Book Stock</flux:label>

                        <flux:input
                            type="number"
                            wire:model="form.stock"
                            placeholder="Enter book stock"
                        />

                        @error ('form.stock')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="space-y-2 md:col-span-2">
                        <flux:label>Book Description</flux:label>

                        <flux:textarea
                            rows="5"
                            wire:model="form.description"
                            placeholder="Enter book description..."
                        />

                        @error ('form.description')
                            <flux:text
                                class="text-red-500"
                                >{{ $message }}</flux:text
                            >
                        @enderror
                    </div>
                </div>

                {{-- Genre --}}
                <div class="space-y-4 rounded-lg p-5">
                    <flux:label class="font-semibold"> Book Genres </flux:label>

                    <div class="grid grid-cols-6 gap-3">
                        @foreach ($genres as $genre)
                            <label
                                class="flex cursor-pointer items-center gap-2"
                            >
                                <flux:checkbox
                                    wire:model="form.genre_ids"
                                    value="{{ $genre->id }}"
                                />

                                <span class="text-sm">
                                    {{ $genre->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error ('form.genre_ids')
                        <flux:text
                            class="text-red-500"
                            >{{ $message }}</flux:text
                        >
                    @enderror
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">
                <flux:button variant="primary" type="submit">
                    Update Book
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
