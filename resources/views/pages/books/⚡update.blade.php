<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\FormBook;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;

new class extends Component {
    use WithFileUploads;

    public FormBook $form;

    public Book $book;

    public $genres;
    public $categories;

    // Mount data
    public function mount($id)
    {
        $this->book = Book::with(['genres', 'categories'])->findOrFail($id);
        $this->form->title = $this->book->title;
        $this->form->author = $this->book->author;
        $this->form->publisher_name = $this->book->publisher_name;
        $this->form->isbn = $this->book->isbn;
        $this->form->stock = $this->book->stock;
        $this->form->description = $this->book->description;
        $this->form->published_at = $this->book->published_at;
        $this->form->genre_ids = $this->book->genres()->pluck('genres.id')->unique()->toArray();
        $this->form->category_ids = $this->book->categories()->pluck('categories.id')->unique()->toArray();

        $this->genres = Genre::all();
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->form->validate();

        $ebookFile = $this->book->ebook_file;
        $imageName = null;

        // Save Ebook File
        if ($this->form->ebook_file) {
            // delete old ebook file
            if ($this->book->ebook_file) {
                Storage::disk('public')->delete('ebooks/' . $this->book->ebook_file);
            }

            $ebookFile = Storage::disk('public')->putFile('ebooks', $this->form->ebook_file);

            $ebookFile = basename($ebookFile);
        }

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
            'ebook_file' => $ebookFile,
            'isbn' => $this->form->isbn,
            'description' => $this->form->description,
            'published_at' => $this->form->published_at,
            'stock' => $this->form->stock,
        ]);

        // Relations
        $this->book->categories()->sync($this->form->category_ids ?? []);
        $this->book->genres()->sync($this->form->genre_ids ?? []);

        session()->flash('success', 'Buku berhasil diperbarui.');

        redirect()->route('books.index');
    }

    public function render()
    {
        return $this->view([
            'genres' => $this->genres,
            'categories' => $this->categories,
        ])
            ->layout('layouts::dashboard')
            ->title('Update Buku');
    }
};
?>


<div class="max-w-7xl mx-auto">
    <flux:card>

        <form wire:submit="save" class="space-y-8">

            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl">
                    Update Buku
                </flux:heading>

                <flux:text class="mt-2">
                    Update informasi buku.
                </flux:text>
            </div>

            <!-- Cover & Ebook File -->
            <div class="space-y-4">

                <flux:label>Cover Buku</flux:label>

                <div class="flex-1">
                    @if ($this->form->cover)
                        <div class="aspect-2/3 w-48 overflow-hidden rounded-lg">
                            <img src="{!! $this->form->cover->temporaryUrl() !!}" alt="Cover Buku" class="w-full h-full object-cover" />
                        </div>
                    @elseif ($this->book->cover)
                        <div class="aspect-2/3 w-48 overflow-hidden rounded-lg">
                            <img src="{{ Storage::url('covers/' . $this->book->cover) }}" alt="Cover Buku"
                                class="w-full h-full object-cover" />
                        </div>
                    @endif

                    <flux:input type="file" accept="image/*" wire:model="form.cover" />

                    <flux:text size="sm" class="mt-2">
                        JPG, PNG or WEBP. Maximum 2MB.
                    </flux:text>

                    @error('form.cover')
                        <flux:text class="text-red-500 mt-1">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                {{-- Ebook File --}}
                <flux:label>Ebook file</flux:label>

                <div class="flex items-center gap-5">

                    <div class="flex-1">
                        <flux:input type="file" accept="application/pdf" wire:model="form.ebook_file" />

                        <flux:text size="sm" class="mt-2">
                            PDF. Maximum 12MB.
                        </flux:text>

                        @error('form.ebook_file')
                            <flux:text class="text-red-500 mt-1">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                </div>
            </div>


            <div class="space-y-8">

                {{-- Informasi Buku --}}
                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Judul --}}
                    <div class="space-y-2 md:col-span-2">
                        <flux:label>Judul Buku</flux:label>

                        <flux:input wire:model="form.title" placeholder="Masukkan judul buku" />

                        @error('form.title')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Author --}}
                    <div class="space-y-2">
                        <flux:label>Penulis Buku</flux:label>

                        <flux:input wire:model="form.author" placeholder="Nama penulis" />

                        @error('form.author')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Publisher --}}
                    <div class="space-y-2">
                        <flux:label>Penerbit</flux:label>

                        <flux:input wire:model="form.publisher_name" placeholder="Nama penerbit" />

                        @error('form.publisher_name')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Tanggal Terbit --}}
                    <div class="space-y-2">
                        <flux:label>Tanggal Terbit</flux:label>

                        <flux:input type="date" wire:model="form.published_at" />

                        @error('form.published_at')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Isbn --}}
                    <div class="space-y-2">
                        <flux:label>Isbn</flux:label>

                        <flux:input type="text" wire:model="form.isbn" placeholder="Nomor ISBN" />

                        @error('form.isbn')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div class="space-y-2">
                        <flux:label>Stok Buku</flux:label>

                        <flux:input type="number" wire:model="form.stock" placeholder="Jumlah stok" />

                        @error('form.stock')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="space-y-2 md:col-span-2">
                        <flux:label>Deskripsi Buku</flux:label>

                        <flux:textarea rows="5" wire:model="form.description"
                            placeholder="Masukkan deskripsi buku..." />

                        @error('form.description')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                    </div>

                </div>

                {{-- Genre & Kategori --}}
                <div class="grid gap-6 lg:grid-cols-2">

                    {{-- Genre --}}
                    <div class="rounded-lg p-5 space-y-4">
                        <flux:label class="font-semibold">
                            Genre Buku
                        </flux:label>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($genres as $genre)
                                <label class="flex items-center gap-2 cursor-pointer">
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

                    {{-- Kategori --}}
                    <div class="rounded-lg p-5 space-y-4">
                        <flux:label class="font-semibold">
                            Kategori Buku
                        </flux:label>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2 cursor-pointer">
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
                    Update Buku
                </flux:button>

            </div>

        </form>

    </flux:card>
</div>
