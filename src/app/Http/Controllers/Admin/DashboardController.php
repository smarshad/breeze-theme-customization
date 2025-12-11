<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard1(){
        $bodyCss = getAuthPageCss();
        return view('admin.dashboard1', compact('bodyCss'));
    }

    public function dashboard2(){
        $bodyCss = getAuthPageCss();
        return view('admin.dashboard2', compact('bodyCss'));
    }

    /**
     * Get dashboard summary
     */
    public function summary(): JsonResponse
    {
        try {
            // $user = auth()->user();

            $query = Expense::query();
            // if (!$user->isSuperAdmin()) {
            //     $query->where('created_by', $user->id);
            // }

            // Calculate totals
            $totalExpenses = (float) $query->clone()->sum('amount');
            $totalRecords = $query->clone()->count();
            $averageExpense = (float) ($query->clone()->avg('amount') ?? 0);

            // Get top categories
            $topCategories = $query->clone()
                ->with('category')
                ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('category_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            // Get top payment methods
            $topPaymentMethods = $query->clone()
                ->with('paymentMethod')
                ->select('payment_method_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            // Get monthly trend (last 6 months)
            $monthlyTrend = $query->clone()
                ->select(
                    DB::raw('DATE_FORMAT(expense_date, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get()
                ->reverse();

            // Get recent expenses
            $recentExpenses = $query->clone()
                ->with(['category', 'expenseType', 'paymentMethod'])
                ->orderBy('expense_date', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard summary retrieved successfully',
                'data' => [
                    'summary' => [
                        'total_expenses' => $totalExpenses,
                        'total_records' => $totalRecords,
                        'average_expense' => $averageExpense,
                    ],
                    'top_categories' => $topCategories,
                    'top_payment_methods' => $topPaymentMethods,
                    'monthly_trend' => $monthlyTrend,
                    'recent_expenses' => $recentExpenses,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dashboard index
     */
    public function index(): JsonResponse
    {
        return $this->summary();
    }
}
