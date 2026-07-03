<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\User;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public User $user;

    #[Validate('min:5|required|string|max:255')]
    public $name;

    #[Validate('min:5|required|email|max:255')]
    public $email;

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    #[Validate('nullable|in:admin,user')]
    public $role = '';

    // Mount data
    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->role = $this->user->role;
    }

    public function update()
    {
        $this->validate();

        $imageName = null;

        // Save avatar
        if ($this->avatar) {
            // delete old image
            Storage::disk('public')->delete('avatars/' . $this->user->avatar);

            // store image
            $this->avatar->storeAs('avatars', $this->avatar->hashName(), 'public');

            // get image name
            $imageName = $this->avatar->hashName();
        } else {
            $imageName = $this->user->avatar;
        }

        // Update user
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $imageName,
            'role' => $this->role,
        ]);

        session()->flash('message', 'User updated successfully.');

        redirect()->route('users.index');
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Edit user');
    }
};
?>

<div class="max-w-7xl mx-auto">
    <flux:card>

        <form wire:submit="update" class="space-y-8">

            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl">
                    Edit User
                </flux:heading>

                <flux:text class="mt-2">
                    Perbarui informasi di bawah ini untuk mengubah detail pengguna.
                </flux:text>
            </div>

            <!-- Avatar -->
            <div class="space-y-4">

                <flux:label>Profile Photo</flux:label>

                <div class="flex items-center gap-5">

                    @if ($avatar)
                        <flux:avatar src="{!! $avatar->temporaryUrl() !!}" size="md" />
                    @elseif ($user->avatar)
                        <flux:avatar src="{{ Storage::url('avatars/' . $user->avatar) }}" size="md" />
                    @else
                        <flux:avatar size="md" />
                    @endif

                    <div class="flex-1">
                        <flux:input type="file" wire:model="avatar" />

                        <flux:text size="sm" class="mt-2">
                            JPG, PNG or WEBP. Maximum 2MB.
                        </flux:text>

                        @error('avatar')
                            <flux:text class="text-red-500 mt-1">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                </div>

            </div>

            <!-- Information -->
            <div class="grid gap-6">

                <div class="space-y-2">
                    <flux:label>Name</flux:label>

                    <flux:input wire:model="name" placeholder="John Doe" />

                    @error('name')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Email</flux:label>

                    <flux:input type="email" wire:model="email" placeholder="john@example.com" />

                    @error('email')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Role</flux:label>

                    <flux:select wire:model="role" placeholder="Pilih role...">
                        <flux:select.option value="admin">admin</flux:select.option>
                        <flux:select.option value="user">user</flux:select.option>
                    </flux:select>

                    @error('role')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

            </div>

            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">

                <flux:button variant="primary" type="submit">
                    Update User
                </flux:button>

            </div>

        </form>

    </flux:card>
</div>
