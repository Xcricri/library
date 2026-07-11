<?php

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use App\Livewire\Forms\UserForm;

use App\Models\User;
use App\Models\Role;

new class extends Component {
    use WithFileUploads;

    public UserForm $form;

    public $roles;

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function save()
    {
        $this->validate();

        $imageName = null;

        // Save avatar
        if ($this->form->avatar) {
            $imageName = $this->form->avatar->hashName();
            $this->form->avatar->storeAs('avatars', $imageName, 'public');
        }

        // Create user
        $user = User::create([
            'name' => $this->form->name,
            'email' => $this->form->email,
            'password' => Hash::make($this->form->password),
            'avatar' => $imageName,
        ]);

        $user->roles()->sync($this->form->role_ids ?? []);

        session()->flash('message', 'User created successfully.');

        redirect()->route('users.index');
    }

    public function render()
    {
        return $this->view([
            'roles' => $this->roles,
        ])
            ->layout('layouts::dashboard')
            ->title('Create user');
    }
};
?>

<div class="mx-auto max-w-7xl">
    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <!-- Header -->
            <div class="border-b pb-5">
                <flux:heading size="xl"> Add User </flux:heading>

                <flux:text class="mt-2">
                    Fill in the information below to create a new user.
                </flux:text>
            </div>

            <!-- Avatar -->
            <div class="space-y-4">
                <flux:label>Profile Photo</flux:label>

                <div class="flex items-center gap-5">
                    @if ($this->form->avatar)
                        <flux:avatar src="{!! $this->form->avatar->temporaryUrl() !!}" size="md" />
                        <div wire:loading wire:target="form.avatar">
                            Uploading...
                        </div>
                    @else
                        <flux:avatar size="md" />
                    @endif

                    <div class="flex-1">
                        <flux:input type="file" accept="image/*" wire:model="form.avatar" />

                        <flux:text size="sm" class="mt-2">
                            JPG, PNG or WEBP. Maximum 2MB.
                        </flux:text>

                        @error('form.avatar')
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

                    @error('form.name')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Email</flux:label>

                    <flux:input type="email" wire:model="form.email" placeholder="john@example.com" />

                    @error('form.email')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <div class="space-y-2">
                    <flux:label>Roles</flux:label>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($roles as $role)
                            <label class="flex cursor-pointer items-center gap-2">
                                <flux:checkbox wire:model="form.role_ids" value="{{ $role->id }}" />

                                <span class="text-sm">
                                    {{ $role->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error('form.role_ids')
                        <flux:text class="text-red-500">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                @error('form.role_ids')
                    <flux:text class="text-red-500">
                        {{ $message }}
                    </flux:text>
                @enderror
            </div>

            <div class="space-y-2">
                <flux:label>Password</flux:label>

                <flux:input type="password" wire:model="form.password" placeholder="••••••••" />

                @error('form.password')
                    <flux:text class="text-red-500">
                        {{ $message }}
                    </flux:text>
                @enderror
            </div>

            <div class="space-y-2">
                <flux:label for="confirm_password">Confirm Password</flux:label>

                <flux:input type="password" id="confirm_password" wire:model="form.password_confirmation"
                    placeholder="••••••••" />

                @error('form.password_confirmation')
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
