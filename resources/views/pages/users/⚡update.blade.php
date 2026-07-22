<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;

new class extends Component {
    use WithFileUploads;

    public UserForm $form;
    public User $user;
    public $roles;

    // Mount data
    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        $this->form->name = $this->user->name;
        $this->form->email = $this->user->email;
        $this->form->role_id = $this->user->roles()->first()?->id;
        $this->form->password = '';
        $this->roles = Role::all();
    }

    public function update()
    {
        $this->validate();

        $imageName = null;

        // Save avatar
        if ($this->form->avatar) {
            // delete old image
            Storage::disk('public')->delete('avatars/' . $this->user->avatar);

            // store image
            $this->form->avatar->storeAs('avatars', $this->form->avatar->hashName(), 'public');

            // get image name
            $imageName = $this->form->avatar->hashName();
        } else {
            $imageName = $this->user->avatar;
        }

        $password = $this->form->password ? Hash::make($this->form->password) : $this->user->password;

        // Update user
        $this->user->update([
            'name' => $this->form->name,
            'email' => $this->form->email,
            'avatar' => $imageName,
            'password' => $password,
        ]);

        $this->user->roles()->sync($this->form->role_id);

        session()->flash('message', 'User updated successfully.');

        redirect()->route('users.index');
    }

    public function render()
    {
        return $this->view([
            'roles' => $this->roles,
        ])
            ->layout('layouts::dashboard')
            ->title('Edit user');
    }
};
?>

<div class="mx-auto max-w-7xl">
    <flux:card>
        <form wire:submit="update" class="space-y-8">
            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl"> Edit User </flux:heading>

                <flux:text class="mt-2">
                    Update the information below to change the user's details.
                </flux:text>
            </div>

            <!-- Avatar -->
            <div class="space-y-4">
                <flux:label>Profile Photo</flux:label>

                <div class="flex items-center gap-5">
                    @if ($this->form->avatar)
                        <flux:avatar
                            src="{!! $this->form->avatar->temporaryUrl() !!}"
                            size="md"
                        />
                    @elseif ($this->user->avatar)
                        <flux:avatar
                            src="{{ Storage::url('avatars/' . $this->user->avatar) }}"
                            size="md"
                        />
                    @else
                        <flux:avatar size="md" />
                    @endif

                    <div class="flex-1">
                        <flux:input
                            type="file"
                            accept="image/*"
                            wire:model="form.avatar"
                        />

                        <flux:text size="sm" class="mt-2">
                            JPG, PNG or WEBP. Maximum 2MB.
                        </flux:text>

                        @error ('form.avatar')
                            <flux:text class="mt-1 text-red-500">
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

                    <flux:input wire:model="form.name" placeholder="John Doe" />

                    @error ('form.name')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Email</flux:label>

                    <flux:input
                        type="email"
                        wire:model="form.email"
                        placeholder="john@example.com"
                    />

                    @error ('form.email')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>New Password</flux:label>

                    <flux:input
                        type="password"
                        wire:model="form.password"
                        placeholder="••••••••"
                    />

                    @error ('form.password')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label for="confirm_password"
                        >Confirm Password</flux:label
                    >

                    <flux:input
                        type="password"
                        id="confirm_password"
                        wire:model="form.password_confirmation"
                        placeholder="••••••••"
                    />

                    @error ('form.password_confirmation')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Role</flux:label>

                    <flux:select wire:model="form.role_id">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </flux:select>

                    @error ('form.role_id')
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
