<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    // Search user
    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(5);
    }

    // Delete user
    public function delete($id)
    {
        $user = User::find($id);
        User::destroy($id);

        // If avatar exists delete avatar
        if ($user->avatar) {
            // delete old image
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        session()->flash('message', 'User deleted successfully.');
    }

    public function render()
    {
        return $this->view([
            'users' => $this->users,
        ])
            ->layout('layouts::dashboard')
            ->title('Index User');
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Table User

            <flux:text>
                Daftar Pengguna di perpustakaan
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="w-full md:max-w-sm">
            <flux:input icon="magnifying-glass" placeholder="Cari user..." wire:model.live.debounce.300ms="search"
                size="sm" />
        </div>

        <flux:button href="{{ route('users.create') }}" wire:navigate variant="primary" size="sm">
            Tambah User
        </flux:button>

    </div>

    {{-- Flash message --}}
    @if (session()->has('message'))
        <flux:callout variant="success">
            {{ session('message') }}
        </flux:callout>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        <flux:table>

            <flux:table.columns>
                <flux:table.column>Nomor</flux:table.column>
                <flux:table.column>Nama</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Peran</flux:table.column>
                <flux:table.column>Avatar</flux:table.column>
                <flux:table.column class="text-right">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($users as $user)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $users->firstItem() + $loop->index }}
                        </flux:table.cell>

                        <flux:table.cell class="font-medium">
                            {{ $user->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $user->email }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $user->role }}
                        </flux:table.cell>

                        <flux:table.cell class="py-1">
                            @if ($user->avatar)
                                <flux:avatar src="{{ Storage::url('avatars/' . $user->avatar) }}" class="w-9 h-9" />
                            @else
                                <flux:avatar class="w-9 h-9" />
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="yellow" size="sm" href="{{ route('users.edit', $user->id) }}"
                                wire:navigate class="cursor-pointer">
                                Edit
                            </flux:badge>

                            <flux:badge color="red" size="sm" class="cursor-pointer"
                                wire:click="delete({{ $user->id }})">
                                Delete
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-6 text-gray-500">
                            No users found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </div>

    {{-- Pagination --}}
    <div>
        <flux:pagination :paginator="$users" />
    </div>

</div>
