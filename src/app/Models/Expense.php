<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'expense_type_id',
        'payment_method_id',
        'expense_date',
        'description',
        'amount',
        'cashback',
        'notes',
        'bank_account',
        'file_path',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    /**
     * Scope to get expenses for a specific user
     */
    public function scopeForUser($query, User $user)
    {
        return $query;
        
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('created_by', $user->id);
    }

    /**
     * Scope to get expenses for a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get expenses by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get expenses by payment method
     */
    public function scopeByPaymentMethod($query, $paymentMethodId)
    {
        return $query->where('payment_method_id', $paymentMethodId);
    }
}
