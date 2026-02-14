<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::query()->with('roles');

        // استبعاد أي مستخدم يملك دور "admin"
        $query->whereDoesntHave('roles', fn($q) =>
            $q->where('slug', 'admin')
        );

        if (!empty($this->filters['name'])) {
            $query->where('name', 'LIKE', "%{$this->filters['name']}%");
        }

        if (!empty($this->filters['email'])) {
            $query->where('email', 'LIKE', "%{$this->filters['email']}%");
        }

        if (!empty($this->filters['role'])) {
            $query->whereHas('roles', fn($q) =>
                $q->where('slug', $this->filters['role'])
            );
        }

        if (isset($this->filters['is_active'])) {
            $query->where('is_active', $this->filters['is_active']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Roles',
            'Active',
            'Created At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->roles->pluck('name')->join(', '),
            $user->is_active ? 'Yes' : 'No',
            $user->created_at->format('Y-m-d')
        ];
    }
}
