<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\ExpenseType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * ReportsController - Advanced Reporting and Export
 * 
 * Provides comprehensive reporting functionality:
 * - Detailed expense reports
 * - Category-wise reports
 * - Payment method reports
 * - Date range reports
 * - Custom filtering
 * - Export to CSV, PDF, Excel
 * - Summary statistics
 */
class ReportsController extends Controller
{
    /**
     * Get detailed expense report
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * Query Parameters:
     * - start_date: YYYY-MM-DD
     * - end_date: YYYY-MM-DD
     * - category_id: integer (optional)
     * - payment_method_id: integer (optional)
     * - expense_type_id: integer (optional)
     * - sort_by: amount, date, category (default: date)
     * - sort_order: asc, desc (default: desc)
     * - page: integer (default: 1)
     * - per_page: integer (default: 15)
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "expenses": [
     *       {
     *         "id": 1,
     *         "description": "Office supplies",
     *         "amount": 150.50,
     *         "expense_date": "2024-01-15",
     *         "category": "Office",
     *         "type": "Business",
     *         "payment_method": "Credit Card",
     *         "notes": "Monthly office supplies"
     *       }
     *     ],
     *     "summary": {
     *       "total_expenses": 5250.75,
     *       "total_count": 45,
     *       "average_expense": 116.68,
     *       "highest_expense": 500.00,
     *       "lowest_expense": 10.50
     *     },
     *     "pagination": {
     *       "current_page": 1,
     *       "per_page": 15,
     *       "total": 45,
     *       "last_page": 3
     *     }
     *   }
     * }
     */
    public function index()
    {
        $bodyCss = getAuthPageCss();
        $categories = Category::all();
        $expenseTypes = ExpenseType::all();
        $paymentMethods = PaymentMethod::all();
        return view('admin.reports.index', compact('bodyCss', 'categories', 'paymentMethods', 'expenseTypes'));
    }


    public function detailedReport(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            // Build query
            $query = Expense::where('created_by', $userId)
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->with('category', 'expenseType', 'paymentMethod');

            // Apply filters
            if ($request->has('category_id') && $request->query('category_id')) {
                $query->where('category_id', $request->query('category_id'));
            }

            if ($request->has('payment_method_id') && $request->query('payment_method_id')) {
                $query->where('payment_method_id', $request->query('payment_method_id'));
            }

            if ($request->has('expense_type_id') && $request->query('expense_type_id')) {
                $query->where('expense_type_id', $request->query('expense_type_id'));
            }

            // Sorting
            $sortBy = $request->query('sort_by', 'expense_date');
            $sortOrder = $request->query('sort_order', 'desc');

            $sortMap = [
                'amount' => 'amount',
                'date' => 'expense_date',
                'category' => 'category_id',
                'description' => 'description'
            ];

            $sortColumn = $sortMap[$sortBy] ?? 'expense_date';
            $query->orderBy($sortColumn, $sortOrder);

            // Get summary before pagination
            $summaryQuery = clone $query;
            $totalExpenses = $summaryQuery->sum('amount');
            $totalCount = $summaryQuery->count();
            $averageExpense = $totalCount > 0 ? $totalExpenses / $totalCount : 0;
            $highestExpense = $summaryQuery->max('amount') ?? 0;
            $lowestExpense = $summaryQuery->min('amount') ?? 0;

            // Pagination
            $perPage = $request->query('per_page', 15);
            $expenses = $query->paginate($perPage);

            // Format response
            $formattedExpenses = $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'description' => $expense->description,
                    'amount' => round($expense->amount, 2),
                    'expense_date' => $expense->expense_date->format('Y-m-d'),
                    'category' => $expense->category->name ?? 'N/A',
                    'type' => $expense->expenseType->name ?? 'N/A',
                    'payment_method' => $expense->paymentMethod->name ?? 'N/A',
                    'notes' => $expense->notes
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Detailed report retrieved successfully',
                'data' => [
                    'expenses' => $formattedExpenses,
                    'summary' => [
                        'total_expenses' => round($totalExpenses, 2),
                        'total_count' => $totalCount,
                        'average_expense' => round($averageExpense, 2),
                        'highest_expense' => round($highestExpense, 2),
                        'lowest_expense' => round($lowestExpense, 2)
                    ],
                    'pagination' => [
                        'current_page' => $expenses->currentPage(),
                        'per_page' => $expenses->perPage(),
                        'total' => $expenses->total(),
                        'last_page' => $expenses->lastPage()
                    ],
                    'filters' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'category_id' => $request->query('category_id'),
                        'payment_method_id' => $request->query('payment_method_id'),
                        'expense_type_id' => $request->query('expense_type_id')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving detailed report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category-wise detailed report
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function categoryReport(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            $expenses = Expense::where('expenses.created_by', $userId)
                ->whereNull('expenses.deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->select(
                    'categories.id',
                    'categories.name',
                    DB::raw('COUNT(expenses.id) as count'),
                    DB::raw('SUM(expenses.amount) as total'),
                    DB::raw('AVG(expenses.amount) as average'),
                    DB::raw('MAX(expenses.amount) as highest'),
                    DB::raw('MIN(expenses.amount) as lowest')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total')
                ->get();

            $totalAmount = $expenses->sum('total');

            $formattedData = $expenses->map(function ($item) use ($totalAmount) {
                return [
                    'category_id' => $item->id,
                    'category_name' => $item->name,
                    'count' => $item->count,
                    'total_amount' => round($item->total, 2),
                    'average_amount' => round($item->average, 2),
                    'highest_amount' => round($item->highest, 2),
                    'lowest_amount' => round($item->lowest, 2),
                    'percentage' => $totalAmount > 0 ? round(($item->total / $totalAmount) * 100, 2) : 0
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Category report retrieved successfully',
                'data' => [
                    'categories' => $formattedData,
                    'summary' => [
                        'total_amount' => round($totalAmount, 2),
                        'total_categories' => count($formattedData),
                        'average_per_category' => count($formattedData) > 0 ? round($totalAmount / count($formattedData), 2) : 0
                    ],
                    'date_range' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment method report
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function paymentMethodReport(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            $expenses = Expense::where('expenses.created_by', $userId)
                ->whereNull('expenses.deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->join('payment_methods', 'expenses.payment_method_id', '=', 'payment_methods.id')
                ->select(
                    'payment_methods.id',
                    'payment_methods.name',
                    DB::raw('COUNT(expenses.id) as count'),
                    DB::raw('SUM(expenses.amount) as total'),
                    DB::raw('AVG(expenses.amount) as average')
                )
                ->groupBy('payment_methods.id', 'payment_methods.name')
                ->orderByDesc('total')
                ->get();

            $totalAmount = $expenses->sum('total');

            $formattedData = $expenses->map(function ($item) use ($totalAmount) {
                return [
                    'method_id' => $item->id,
                    'method_name' => $item->name,
                    'method_type' => $item->type,
                    'count' => $item->count,
                    'total_amount' => round($item->total, 2),
                    'average_amount' => round($item->average, 2),
                    'percentage' => $totalAmount > 0 ? round(($item->total / $totalAmount) * 100, 2) : 0
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment method report retrieved successfully',
                'data' => [
                    'payment_methods' => $formattedData,
                    'summary' => [
                        'total_amount' => round($totalAmount, 2),
                        'total_methods' => count($formattedData)
                    ],
                    'date_range' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment method report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly summary report
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function monthlySummaryReport(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $year = $request->query('year', now()->year);

            $expenses = Expense::where('created_by', $userId)
                ->whereNull('deleted_at')
                ->whereYear('expense_date', $year)
                ->select(
                    DB::raw('MONTH(expense_date) as month'),
                    DB::raw('MONTHNAME(expense_date) as month_name'),
                    DB::raw('COUNT(expenses.id) as count'),
                    DB::raw('SUM(amount) as total'),
                    DB::raw('AVG(amount) as average'),
                    DB::raw('MAX(amount) as highest'),
                    DB::raw('MIN(amount) as lowest')
                )
                ->groupBy(DB::raw('MONTH(expense_date)'), DB::raw('MONTHNAME(expense_date)'))
                ->orderBy('month', 'asc')
                ->get();

            $totalAmount = $expenses->sum('total');

            $formattedData = $expenses->map(function ($item) use ($totalAmount) {
                return [
                    'month' => $item->month,
                    'month_name' => $item->month_name,
                    'count' => $item->count,
                    'total_amount' => round($item->total, 2),
                    'average_amount' => round($item->average, 2),
                    'highest_amount' => round($item->highest, 2),
                    'lowest_amount' => round($item->lowest, 2),
                    'percentage' => $totalAmount > 0 ? round(($item->total / $totalAmount) * 100, 2) : 0
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Monthly summary report retrieved successfully',
                'data' => [
                    'months' => $formattedData,
                    'summary' => [
                        'total_amount' => round($totalAmount, 2),
                        'average_per_month' => count($formattedData) > 0 ? round($totalAmount / count($formattedData), 2) : 0,
                        'highest_month' => $formattedData->sortByDesc('total')->first()['month_name'] ?? 'N/A',
                        'lowest_month' => $formattedData->sortBy('total')->first()['month_name'] ?? 'N/A'
                    ],
                    'year' => $year
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving monthly summary report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export expenses to CSV
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request)
    {
        try {
            $userId = auth()->id();
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            $expenses = Expense::where('created_by', $userId)
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->with('category', 'expenseType', 'paymentMethod')
                ->orderBy('expense_date', 'desc')
                ->get();

            $filename = 'expenses_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ];

            $callback = function () use ($expenses) {
                $file = fopen('php://output', 'w');

                // Write headers
                fputcsv($file, [
                    'ID',
                    'Description',
                    'Amount',
                    'Date',
                    'Category',
                    'Type',
                    'Payment Method',
                    'Notes'
                ]);

                // Write data
                foreach ($expenses as $expense) {
                    fputcsv($file, [
                        $expense->id,
                        $expense->description,
                        $expense->amount,
                        $expense->expense_date->format('Y-m-d'),
                        $expense->category->name ?? 'N/A',
                        $expense->expenseType->name ?? 'N/A',
                        $expense->paymentMethod->name ?? 'N/A',
                        $expense->notes
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting to CSV',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export expenses to PDF
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $userId = auth()->id();
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            $expenses = Expense::where('created_by', $userId)
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->with('category', 'expenseType', 'paymentMethod')
                ->orderBy('expense_date', 'desc')
                ->get();

            $totalAmount = $expenses->sum('amount');

            $pdf = Pdf::loadView('reports.expenses-pdf', [
                'expenses' => $expenses,
                'totalAmount' => $totalAmount,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

            return $pdf->download('expenses_' . now()->format('Y-m-d_H-i-s') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting to PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export expenses to Excel
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            $userId = auth()->id();
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            $expenses = Expense::where('created_by', $userId)
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->with('category', 'expenseType', 'paymentMethod')
                ->orderBy('expense_date', 'desc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = ['ID', 'Description', 'Amount', 'Date', 'Category', 'Type', 'Payment Method', 'Notes'];
            foreach ($headers as $col => $header) {
                $sheet->setCellValue(chr(65 + $col) . '1', $header);
            }

            // Set data
            $row = 2;
            foreach ($expenses as $expense) {
                $sheet->setCellValue('A' . $row, $expense->id);
                $sheet->setCellValue('B' . $row, $expense->description);
                $sheet->setCellValue('C' . $row, $expense->amount);
                $sheet->setCellValue('D' . $row, $expense->expense_date->format('Y-m-d'));
                $sheet->setCellValue('E' . $row, $expense->category->name ?? 'N/A');
                $sheet->setCellValue('F' . $row, $expense->expenseType->name ?? 'N/A');
                $sheet->setCellValue('G' . $row, $expense->paymentMethod->name ?? 'N/A');
                $sheet->setCellValue('H' . $row, $expense->notes);
                $row++;
            }

            // Auto-fit columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'expenses_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            ob_start();
            $writer->save('php://output');
            $content = ob_get_clean();

            return response($content, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting to Excel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get custom report with advanced filtering
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function customReport(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            // $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date', now()->subMonth()->toDateString()));
            // $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date', now()->toDateString()))->endOfDay();

            $startDate =$request->query('start_date');
            $endDate =$request->query('end_date');
            
            $query = Expense::where('created_by', $userId)
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate]);

            // Apply all filters
            if ($request->has('category_id') && $request->query('category_id')) {
                $query->where('category_id', $request->query('category_id'));
            }

            if ($request->has('payment_method_id') && $request->query('payment_method_id')) {
                $query->where('payment_method_id', $request->query('payment_method_id'));
            }

            if ($request->has('expense_type_id') && $request->query('expense_type_id')) {
                $query->where('expense_type_id', $request->query('expense_type_id'));
            }

            if ($request->has('min_amount') && $request->query('min_amount')) {
                $query->where('amount', '>=', $request->query('min_amount'));
            }

            if ($request->has('max_amount') && $request->query('max_amount')) {
                $query->where('amount', '<=', $request->query('max_amount'));
            }

            if ($request->has('search') && $request->query('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', '%' . $search . '%')
                        ->orWhere('notes', 'like', '%' . $search . '%');
                });
            }

            // Get data
            $expenses = $query->with('category', 'expenseType', 'paymentMethod')
                ->orderBy('expense_date', 'desc')
                ->get();
            \Log::info('Detailed Report FULL SQL', [
                'query' => $this->getFullSql($query),
            ]);
            $totalAmount = $expenses->sum('amount');
            $totalCount = $expenses->count();

            // Group by category
            $byCategory = $expenses->groupBy('category.name')->map(function ($items) {
                return [
                    'category' => $items->first()->category->name,
                    'count' => $items->count(),
                    'total' => round($items->sum('amount'), 2)
                ];
            })->values();

            // Group by payment method
            $byPaymentMethod = $expenses->groupBy('paymentMethod.name')->map(function ($items) {
                return [
                    'method' => $items->first()->paymentMethod->name,
                    'count' => $items->count(),
                    'total' => round($items->sum('amount'), 2)
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Custom report generated successfully',
                'data' => [
                    'summary' => [
                        'total_amount' => round($totalAmount, 2),
                        'total_count' => $totalCount,
                        'average_amount' => $totalCount > 0 ? round($totalAmount / $totalCount, 2) : 0
                    ],
                    'by_category' => $byCategory,
                    'by_payment_method' => $byPaymentMethod,
                    'date_range' => $startDate. ' to ' . $endDate,
                    'filters_applied' => [
                        'category_id' => $request->query('category_id'),
                        'payment_method_id' => $request->query('payment_method_id'),
                        'expense_type_id' => $request->query('expense_type_id'),
                        'min_amount' => $request->query('min_amount'),
                        'max_amount' => $request->query('max_amount'),
                        'search' => $request->query('search')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating custom report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getFullSql($query)
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        foreach ($bindings as $binding) {
            if (is_string($binding)) {
                $binding = "'" . addslashes($binding) . "'";
            } elseif ($binding instanceof \DateTimeInterface) {
                $binding = "'" . $binding->format('Y-m-d H:i:s') . "'";
            } elseif (is_null($binding)) {
                $binding = 'NULL';
            }

            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }

        return $sql;
    }
}
