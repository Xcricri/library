<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Category;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $name;

    #[Validate('nullable|string|max:255')]
    public $description;

    public function save()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Kategori berhasil ditambahkan.');

        redirect()->route('categories.index');
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Tambah Kategori');
    }
};
?>

<div class="max-w-7xl mx-auto">
    <flux:card>

        <form wire:submit="save" class="space-y-8">

            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl">
                    Tambah Kategori
                </flux:heading>

                <flux:text class="mt-2">
                    Isi informasi di bawah ini untuk membuat kategori baru.
                </flux:text>
            </div>

            <!-- Information -->
            <div class="grid gap-6">

                <div class="space-y-2">
                    <flux:label>Nama kategori</flux:label>

                    <flux:input wire:model="name" placeholder="John Doe" />

                    @error('name')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Deskripsi</flux:label>

                    <flux:textarea wire:model="description" placeholder="Description" />

                    @error('description')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">

                <flux:button variant="primary" type="submit">
                    Create Kategori
                </flux:button>

            </div>

        </form>
    </flux:card>
</div>
