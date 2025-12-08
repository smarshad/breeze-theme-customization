<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;
use App\Models\User;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user= User::first();

        $types = [
            'Business',
            'Personal',
            'Family',
            'Travel',
            'Medical',
            'Education',
            'Grocery',
            'Entertainment',
            'Other',
        ];

        foreach($types as $type){

            ExpenseType::create([
                'name' => $type,
                'created_by' => $user->id,
            ]);
        }
    }
}
