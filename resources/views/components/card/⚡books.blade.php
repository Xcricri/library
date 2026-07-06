<?php

use Livewire\Component;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;

use App\Livewire\Forms\LoanForm;

new class extends Component {
    public LoanForm $form;
    public $books;

    // Mount data
    public function mount()
    {
        $this->books = Book::all();

        if (auth()->check()) {
            $this->form->user_id = auth()->id();
        }

        $this->form->borrowed_at = now()->format('d-m-Y');
        $this->form->returned_at = null;
        $this->form->due_date = now()->addDays(7)->format('d-m-Y');
    }

    public function selectBook($bookId)
    {
        $this->form->book_id = $bookId;
        $this->js("Flux.modal('borrow-book').show()");
    }

    public function save()
    {
        $this->form->validate();

        $this->form->user_id = auth()->id();

        $book = Book::findOrFail($this->form->book_id);

        if ($book->stock <= 0) {
            session()->flash('error', 'Buku tidak tersedia untuk dipinjam.');
            return;
        }

        Loan::create([
            'user_id' => $this->form->user_id,
            'book_id' => $this->form->book_id,
            'borrowed_at' => $this->form->borrowed_at,
            'returned_at' => $this->form->returned_at,
            'due_date' => $this->form->due_date,
            'status' => 'borrowed',
        ]);

        $book->decrement('stock');

        session()->flash('message', 'Buku berhasil dipinjam.');

        return redirect()->route('books.index');
    }

    public function render()
    {
        return $this->view([
            'books' => $this->books,
        ]);
    }
};
?>
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">

        @forelse ($books as $book)
            <flux:card class="overflow-hidden rounded-xl shadow p-0 flex flex-col">

                <div class="aspect-4/5 overflow-hidden bg-zinc-100">
                    <img src="{{ Storage::url('covers/' . $book->cover) }}" alt="{{ $book->title }}"
                        class="w-full h-full object-cover transition duration-300">
                </div>

                <!-- Content -->
                <div class="p-3 space-y-2 flex flex-col flex-1">

                    <!-- Title & author lebih ringkas -->
                    <div class="space-y-0.5">
                        <flux:heading size="sm" class="leading-tight line-clamp-2">
                            {{ $book->title }}
                        </flux:heading>

                        <flux:text class="text-zinc-500 text-xs line-clamp-1">
                            {{ $book->author }}
                        </flux:text>
                    </div>

                    <div class="flex-1"></div>

                    <!-- Stock info lebih kecil -->
                    <div class="flex items-center justify-between">
                        @if ($book->stock > 0)
                            <flux:badge size="sm" color="green">Tersedia</flux:badge>
                        @else
                            <flux:badge size="sm" color="red">Habis</flux:badge>
                        @endif

                        <flux:text class="text-[10px] ">
                            Stok:
                            <span class="font-medium ">{{ $book->stock }}</span>
                        </flux:text>
                    </div>

                    <!-- Buttons lebih kecil -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <flux:button size="sm" variant="ghost" href="{{ route('public.books.view', $book->slug) }}"
                            class="w-full">
                            Lihat
                        </flux:button>

                        @auth
                            <flux:button size="sm" variant="primary" wire:click="selectBook({{ $book->id }})"
                                :disabled="$book->stock <= 0" class="w-full">
                                Pinjam
                            </flux:button>
                        @endauth
                    </div>

                </div>
            </flux:card>

        @empty
            <div class="col-span-full">
                <flux:card class="p-8 text-center rounded-xl shadow">
                    <flux:heading size="sm">Tidak ada buku</flux:heading>
                    <flux:text class="text-zinc-500 text-sm">
                        Tidak ada buku yang tersedia saat ini.
                    </flux:text>
                </flux:card>
            </div>
        @endforelse

    </div>

    <flux:modal name="borrow-book" class="md:w-105">
        <div class="overflow-hidden rounded-2xl">

            <!-- Header -->
            <div class="bg-linear-to-r from-indigo-500 to-violet-600 p-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-xl">
                        📚
                    </div>
                    <div>
                        <flux:heading size="lg">Pinjam Buku</flux:heading>
                        <flux:text class="text-white/80 text-sm">
                            Pastikan data peminjaman sudah benar
                        </flux:text>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-5 ">

                <!-- Book preview (opsional tapi bagus) -->
                <div class="flex gap-4 p-3 rounded-xl ">
                    <div class="w-12 h-16 rounded-md overflow-hidden shrink-0">
                        <img src="{{ isset($form->book_id) ? Storage::url('covers/' . optional($books->find($form->book_id))->cover) : '' }}"
                            class="w-full h-full object-cover">
                    </div>

                    <div class="min-w-0">
                        <flux:text class="font-medium truncate">
                            {{ optional($books->find($form->book_id))->title ?? 'Pilih buku' }}
                        </flux:text>

                        <flux:text class="text-xs text-zinc-500">
                            {{ optional($books->find($form->book_id))->author ?? '-' }}
                        </flux:text>
                    </div>
                </div>

                <!-- Error -->
                @if (session()->has('error'))
                    <div class="p-3 text-sm text-red-700 bg-red-50 rounded-xl border border-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Inputs -->
                <div class="space-y-4">
                    <flux:field>
                        <flux:input wire:model="form.borrowed_at" type="date" label="Tanggal pinjam" />
                        @error('form.borrowed_at')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </flux:field>

                    <flux:field>
                        <flux:input wire:model="form.due_date" type="date" label="Tanggal kembali" />
                        @error('form.due_date')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </flux:field>
                </div>

                <!-- Footer -->
                <div class="pt-4 border-t border-zinc-100 flex items-center justify-between gap-3">

                    <flux:text class="text-xs">
                        Buku akan tercatat di sistem peminjaman
                    </flux:text>

                    @if (auth()->check() && auth()->user()->role === 'user')
                        <flux:button wire:click="save" variant="primary" class="px-5">
                            Pinjam
                        </flux:button>
                    @elseif(auth()->check() && auth()->user()->role === 'admin')
                        <flux:text class="text-sm text-red-500 font-medium">
                            Admin tidak dapat meminjam
                        </flux:text>
                    @endif

                </div>

            </div>
        </div>
    </flux:modal>

</div>
