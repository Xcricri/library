<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title('List Buku');
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="lg">
            Tabel Buku

            <flux:text>
                Daftar buku di perpustakaan
            </flux:text>
        </flux:heading>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="w-full md:max-w-sm flex items-center gap-4">
            <flux:select wire:model.live="statusFilter" placeholder="Choose status..." size="sm">
                <flux:select.option value="active">Aktif</flux:select.option>
                <flux:select.option value="trashed">Non-Aktif</flux:select.option>
                <flux:select.option value="all">Semua</flux:select.option>
            </flux:select>

            <flux:input icon="magnifying-glass" placeholder="Cari pinjaman..." wire:model.live.debounce.300ms="search"
                size="sm" />
        </div>

    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nomor</flux:table.column>
            <flux:table.column>Buku</flux:table.column>
            <flux:table.column>Genre</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            <flux:table.row>
                <flux:table.cell>1</flux:table.cell>
                <flux:table.cell>Lindsey Aminoff</flux:table.cell>
                <flux:table.cell>Fiksi</flux:table.cell>
                <flux:table.cell class="py-0">
                    <flux:badge color="blue" size="sm">Baca Buku</flux:badge>
                    <flux:badge color="red" size="sm">Kembalikan buku</flux:badge>
                </flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>
</div>
