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

        session()->flash('message', 'Genre created successfully.');

        redirect()->route('genres.index');
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Add Genre');
    }
};
?>

<div class="mx-auto max-w-7xl">
    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl"> Add Genre </flux:heading>

                <flux:text class="mt-2">
                    Fill in the information below to create a new genre.
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
