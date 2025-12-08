<?php

namespace App\Interfaces;

use App\Models\Expense;


interface ExpenseRepositoryInterface
{
     /**
     * Create a new record.
     */
    public function create(array $details): Expense;

}
