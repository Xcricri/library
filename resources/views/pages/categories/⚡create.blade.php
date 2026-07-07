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

        session()->flash('message', 'Category created successfully.');

        redirect()->route('categories.index');
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Add Category');
    }
};
?>

<div class="mx-auto max-w-7xl">
    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl"> Add Category </flux:heading>

                <flux:text class="mt-2">
                    Fill in the information below to create a new category.
                </flux:text>
            </div>

            <!-- Information -->
            <div class="grid gap-6">
                <div class="space-y-2">
                    <flux:label>Name</flux:label>

                    <flux:input wire:model="name" placeholder="John Doe" />

                    @error ('name')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Description</flux:label>

                    <flux:textarea
                        wire:model="description"
                        placeholder="Description"
                    />

                    @error ('description')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">
                <flux:button variant="primary" type="submit">
                    Create Category
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
