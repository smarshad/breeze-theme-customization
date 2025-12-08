<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreRequest;
use App\Models\Category;
use App\Models\ExpenseType;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DomainException;
use Exception;

class ExpenseController extends BaseController
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.expense.index');
    }

    public function create()
    {
        $categories = Category::all();
        $expenseTypes = ExpenseType::all();
        $paymentMethods = PaymentMethod::all();
        return view('admin.expense.create', compact('categories', 'expenseTypes', 'paymentMethods'));
    }

     /**
     * Store a newly created record.
     */
    public function store(StoreRequest $request)
    {
        try {
            // Log raw incoming data
            $this->logInfo('Raw data for new payment method', $request->all());

            // Validate and prepare DTO
            $dto = $this->createDTOFromRequest($request);

            // Service layer creation
            $data  = $this->service->create($dto);

            return $this->successResponse(new PaymentMethodResource($data), 'Payment Method Succesfully Created', 201, ['callback' => route('expensetype.list')]);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }
}
