<?php

namespace App\Services;

use App\DTOs\ExpenseDTO;
use Illuminate\Support\Facades\Cache;

class ExpenseService
{

    protected const CACHE_KEY_ALL = 'expense.all';

     /**
     * Create New Record
     */

     public function __construct()
     {
        throw new \Exception('Not implemented');
     }

     public function create(ExpenseDTO $paymentMethodDO): PaymentMethod
     {
 
         return DB::transaction(function () use ($paymentMethodDO) {
             $paymentMethod = $this->paymentMethodRepository->create($paymentMethodDO->toArray());
 
             // Invalidate cache
             Cache::forget(self::CACHE_KEY_ALL);
             return $paymentMethod;
         });
     }
}
