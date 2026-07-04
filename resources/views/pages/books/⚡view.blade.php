<?php

use Livewire\Component;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

new class extends Component {
    public Book $book;

    public function mount($slug)
    {
        $this->book = Book::with(['categories', 'genres'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->book->published_at = Carbon::parse($this->book->published_at);
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('View Book');
    }
};
?>

<div class="max-w-7xl mx-auto ">
    <flux:card class="overflow-hidden p-0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 p-6 sm:p-8">

            {{-- Kolom Kiri: Cover Buku --}}
            <div class="flex flex-col items-center md:items-start space-y-4">
                <div
                    class="w-48 h-72 md:w-full md:h-auto aspect-3/4 shrink-0 overflow-hidden rounded-xl shadow-xl  transition duration-300 hover:scale-[1.02]">
                    <img src="{{ Storage::url('covers/' . $book->cover) }}" alt="{{ $book->title }}"
                        class="w-full h-full object-cover">
                </div>

                {{-- Tombol Aksi Tambahan (Opsional, sangat cocok untuk tema E-Book) --}}
                <div class="w-full pt-2 hidden md:block">
                    <flux:button variant="primary" class="w-full justify-center" icon="book-open">
                        Baca Sekarang
                    </flux:button>
                </div>
            </div>

            {{-- Kolom Kanan: Detail & Metadata (Mengambil 2 kolom pada desktop) --}}
            <div class="md:col-span-2 flex flex-col justify-between space-y-6">

                <div class="space-y-4">
                    {{-- Judul dan Penulis --}}
                    <div>
                        <flux:heading size="lg"
                            class="font-bold tracking-tight text-gray-900 dark:text-white leading-tight">
                            {{ $book->title }}
                        </flux:heading>
                        <flux:text class="text-sm font-medium text-primary mt-1">
                            Oleh <span class="hover:underline cursor-pointer">{{ $book->author }}</span>
                        </flux:text>
                    </div>

                    {{-- Tags Kategori & Genre --}}
                    <div class="flex flex-wrap gap-5 pt-1 items-center">
                        <flux:badge size="sm" variant="neutral" inset class="capitalize">
                            {{ $book->categories->pluck('name')->join(', ') }}
                        </flux:badge>
                        <flux:badge size="sm" variant="brand" inset class="capitalize">
                            {{ $book->genres->pluck('name')->join(', ') }}
                        </flux:badge>
                    </div>

                    <flux:separator variant="subtle" />

                    {{-- Informasi Penerbitan dalam Grid Kecil --}}
                    <div class="grid grid-cols-2 gap-4 sm:gap-6 pt-1">
                        <div>
                            <flux:text size="sm" class="text-gray-400 block mb-0.5">Penerbit</flux:text>
                            <flux:text class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $book->publisher_name }}
                            </flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-gray-400 block mb-0.5">Tahun Terbit</flux:text>
                            <flux:text class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $book->published_at->format('Y') }}
                            </flux:text>
                        </div>
                    </div>

                    <flux:separator variant="subtle" />

                    {{-- Sinopsis / Deskripsi --}}
                    <div class="space-y-2">
                        <flux:heading size="lg"
                            class=" font-bold tracking-tight text-gray-900 dark:text-white leading-tight ">
                            Deskripsi / Sinopsis
                        </flux:heading>
                        <flux:text class="leading-relaxed text-gray-600 dark:text-gray-300 text-sm whitespace-pre-line">
                            {{ $book->description }}
                        </flux:text>
                    </div>
                </div>

                {{-- Tombol Aksi Mobile (Hanya muncul di layar kecil) --}}
                <div class="pt-4 md:hidden">
                    <flux:button variant="primary" class="w-full justify-center" icon="book-open">
                        Baca Sekarang
                    </flux:button>
                </div>

            </div>
        </div>
    </flux:card>
</div>
