<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Genre;

new class extends Component {
    #[Validate('min:3|required|string|max:255')]
    public $name;

    public function save()
    {
        $this->validate();

        Genre::create([
            'name' => $this->name,
        ]);

        session()->flash('message', 'Genre berhasil ditambahkan.');

        redirect()->route('genres.index');
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Tambah Genre');
    }
};
?>

<div class="max-w-7xl mx-auto">
    <flux:card>

        <form wire:submit="save" class="space-y-8">

            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl">
                    Tambah Genre
                </flux:heading>

                <flux:text class="mt-2">
                    Isi informasi di bawah ini untuk membuat genre baru.
                </flux:text>
            </div>

            <!-- Information -->
            <div class="grid gap-6">

                <div class="space-y-2">
                    <flux:label>Nama genre</flux:label>

                    <flux:input wire:model="name" placeholder="John Doe" />

                    @error('name')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">

                <flux:button variant="primary" type="submit">
                    Create Genre
                </flux:button>

            </div>

        </form>
    </flux:card>
</div>
