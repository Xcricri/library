<?php

use Livewire\Component;
use Livewire\Attributes\Validate;

use App\Models\Category;

new class extends Component {
    public Category $category;

    #[Validate('required|string|max:255')]
    public $name;

    #[Validate('nullable|string|max:255')]
    public $description;

    // Mount data
    public function mount($id)
    {
        $this->category = Category::findOrFail($id);
        $this->name = $this->category->name;
        $this->description = $this->category->description;
    }

    // Update category
    public function update()
    {
        $this->validate();

        $this->category->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        redirect()->route('genres.index');

        session()->flash('message', 'Genre updated successfully.');
    }

    // Render
    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Edit Genre');
    }
};
?>

<div class="max-w-7xl mx-auto">
    <flux:card>

        <form wire:submit="update" class="space-y-8">

            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl">
                    Edit Genre
                </flux:heading>

                <flux:text class="mt-2">
                    Isi informasi di bawah ini untuk mengedit genre.
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
                    Update Genre
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
