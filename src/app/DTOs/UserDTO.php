<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $created_by,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly ?string $mobile_no,
        public readonly array $roles, // Keep as array for multiple roles
        public readonly array $permissions = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            mobile_no: $data['mobile_no'] ?? null,
            created_by: $data['created_by'] ?? Auth::id(),
            roles: $data['roles'] ?? [], // Expect array of role IDs
            permissions: $data['permissions'] ?? []
        );
    }
    
    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            password: null, // never expose password!
            mobile_no: $user->mobile_no,
            created_by: $user->created_by,
            roles: $user->roles()->pluck('id')->toArray(), // get role IDs
            permissions: $user->permissions()->pluck('name')->toArray() // or IDs if needed
        );
    }
    
    public function toArray(): array
    {
        // Prepare array for user creation (excluding roles here)
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'created_by' => $this->created_by,
            'mobile_no' => $this->mobile_no,
        ];

        // Only include and hash password if provided
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        return $data;
    }

    // Separate method to get roles for syncing
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}