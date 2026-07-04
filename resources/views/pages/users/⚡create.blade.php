<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Validate('min:5|required|string|max:255')]
    public $name;

    #[Validate('min:5|required|email|unique:users')]
    public $email;

    #[Validate('min:8|required|string|confirmed')]
    public $password;

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    #[Validate('required|in:admin,user')]
    public $role = '';

    public function save()
    {
        $this->validate();

        $imageName = null;

        // Save avatar
        if ($this->avatar) {
            $imageName = $this->avatar->hashName();
            $this->avatar->storeAs('avatars', $imageName, 'public');
        }

        // Create user
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'avatar' => $imageName,
            'role' => $this->role,
        ]);

        session()->flash('message', 'User berhasil ditambahkan.');

        redirect()->route('users.index');
    }

    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('Create user');
    }
};
?>

<div class="max-w-7xl mx-auto">
    <flux:card>

        <form wire:submit="save" class="space-y-8">

            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl">
                    Tambah User
                </flux:heading>

                <flux:text class="mt-2">
                    Isi informasi di bawah ini untuk membuat pengguna baru.
                </flux:text>
            </div>

            <!-- Avatar -->
            <div class="space-y-4">

                <flux:label>Profile Photo</flux:label>

                <div class="flex items-center gap-5">

                    @if ($avatar)
                        <flux:avatar src="{!! $avatar->temporaryUrl() !!}" size="md" />
                        <div wire:loading wire:target="avatar">
                            Uploading...
                        </div>
                    @else
                        <flux:avatar size="md" />
                    @endif

                    <div class="flex-1">
                        <flux:input type="file" accept="image/*" wire:model="avatar" />

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
                    <flux:label>Nama user</flux:label>

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
                    <flux:label>Peran</flux:label>

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

                <div class="space-y-2">
                    <flux:label>Password</flux:label>

                    <flux:input type="password" wire:model="password" placeholder="••••••••" />

                    @error('password')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

            </div>

            <!-- Footer -->
            <div class="flex justify-end border-t pt-6">

                <flux:button variant="primary" type="submit">
                    Create User
                </flux:button>

            </div>

        </form>

    </flux:card>
</div>
