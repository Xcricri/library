<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all();
    }

    /**
     * Summary of headings
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Email',
            'Role',
            'Created At',
        ];
    }

    /**
     * Summary of map
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->created_at,
        ];
    }
}
