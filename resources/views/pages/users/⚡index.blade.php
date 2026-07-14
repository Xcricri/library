<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\User;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = 'active';

    public function search()
    {
        $this->resetPage();
    }

    // Search user
    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->statusFilter === 'trashed', function ($query) {
                $query->onlyTrashed();
            })
            ->when($this->statusFilter === 'all', function ($query) {
                $query->withTrashed();
            })
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(5);
    }

    // soft delete user
    public function softDelete($id)
    {
        $user = User::findorFail($id);
        $user->delete();

        session()->flash('message', 'User deleted successfully.');
    }

    // force delete user
    public function forceDelete($id)
    {
        $user = User::withTrashed()->find($id);
        $user->forceDelete();

        // If avatar exists delete avatar
        if ($user->avatar) {
            // delete old image
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        session()->flash('message', 'User deleted successfully.');
    }

    // restore method
    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();

        session()->flash('message', 'User restored successfully.');
    }

    public function export()
    {
        return Excel::download(new UsersExport(), 'users.xlsx');
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
            Table Users

            <flux:text> List of library users </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="grid grid-cols-1 gap-3 sm:flex sm:max-w-3xl sm:flex-1 sm:items-center">
            <div class="w-full md:max-w-sm">
                <flux:input icon="magnifying-glass" placeholder="Cari user..." wire:model.live.debounce.300ms="search"
                    size="sm" clearable />
            </div>

            <div class="w-full sm:w-40">
                <flux:select wire:model.live="statusFilter" placeholder="Pilih status..." size="sm">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="active">
                        Active</flux:select.option>
                    <flux:select.option value="trashed">
                        Trash</flux:select.option>
                </flux:select>
            </div>
        </div>

        <div class="flex w-full justify-end md:w-auto items-center gap-2">
            <flux:button wire:click="export" variant="primary" size="sm">
                Export Users
            </flux:button>
            <flux:button href="{{ route('users.create') }}" wire:navigate variant="primary" size="sm"
                class="w-full md:w-auto">
                Add User
            </flux:button>
        </div>
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
                <flux:table.column>Number</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Role</flux:table.column>
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

                        <flux:table.cell> {{ $user->email }} </flux:table.cell>

                        <flux:table.cell> {{ $user->role }} </flux:table.cell>

                        <flux:table.cell class="py-1">
                            @if ($user->avatar)
                                <flux:avatar src="{{ Storage::url('avatars/' . $user->avatar) }}" class="h-9 w-9" />
                            @else
                                <flux:avatar class="h-9 w-9" />
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($user->trashed())
                                <flux:modal.trigger name="restore-user-{{ $user->id }}">
                                    <flux:badge color="blue" size="sm" class="cursor-pointer">
                                        Restore
                                    </flux:badge>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-forced-user-{{ $user->id }}">
                                    <flux:badge color="red" size="sm" class="cursor-pointer">
                                        Delete permanent
                                    </flux:badge>
                                </flux:modal.trigger>
                            @else
                                <flux:badge color="yellow" size="sm" href="{{ route('users.update', $user->id) }}"
                                    wire:navigate class="cursor-pointer">
                                    Edit
                                </flux:badge>

                                <flux:modal.trigger name="soft-delete-user-{{ $user->id }}">
                                    <flux:badge color="red" size="sm" class="cursor-pointer">
                                        Delete
                                    </flux:badge>
                                </flux:modal.trigger>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>

                    {{-- soft delete User Modal --}}
                    <flux:modal name="soft-delete-user-{{ $user->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Soft Delete User</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to soft delete this
                                    user? This action can be undone later.
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary"
                                    wire:click="softDelete({{ $user->id }})">
                                    Soft Delete
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>

                    {{-- force delete User Modal --}}
                    <flux:modal name="force-delete-user-{{ $user->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Force Delete User</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to force delete this
                                    user? This action cannot be undone.
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary"
                                    wire:click="forceDelete({{ $user->id }})">
                                    Force Delete
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>

                    {{-- Soft Delete User Modal --}}
                    <flux:modal name="restore-user-{{ $user->id }}" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Restore User</flux:heading>
                                <flux:text class="mt-2">Are you sure you want to restore this user?
                                    This action cannot be undone.
                                </flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary"
                                    wire:click="restore({{ $user->id }})">
                                    Restore
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="py-6 text-center text-gray-500">
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
