<?php

namespace App\Http\Controllers;

use App\DTOs\ExpenseDTO;
use App\Http\Requests\Expense\StoreRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Category;
use App\Models\ExpenseType;
use App\Models\PaymentMethod;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;

class ExpenseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected ExpenseService $service) {}

    public function getAll(Request $request): JsonResponse
    {

        $perPage            = $request->get('per_page', NULL);
        $data               = $this->service->getPaginated($perPage);
        $draw               = $request->get('draw', 1);
        $response           = ExpenseResource::collection($data)->response()->getData(true);
        $response['draw']   = (int) $draw;
        $response['recordsTotal'] = $response['meta']['total'];
        $response['recordsFiltered'] = $response['meta']['total'];
        return response()->json($response);

       
    }

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
            $this->logInfo('Raw data for new Expense', $request->all());

            // Validate and prepare DTO
            $dto = $this->createDTOFromRequest($request);

            // Service layer creation
            $data  = $this->service->create($dto);

            return $this->successResponse(new ExpenseResource($data), 'Expense Succesfully Created', 201, ['redirect' => route('expense.index')]);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }

    private function createDTOFromRequest($request): ExpenseDTO
    {

        $validatedData  = $request->validated();

        $validatedData['created_by']  = $request->user()->id ?? NULL;

        // If file uploaded, store it and replace file_path with storage path string
        if ($request->hasFile('uploaded_file')) {
            $validatedData['file_path'] = $request->file('uploaded_file')
                ->store('expenses/files', 'public'); // returns string path
        } else {
            // ensure the key exists and is null (so DTO gets consistent shape)
            $validatedData['file_path'] = $validatedData['file_path'] ?? NULL;
        }

        $this->logInfo('Passing Validated Data to DTO For New Expense', $validatedData);

        $dto = ExpenseDTO::fromArray($validatedData);

        $this->logInfo('New Expense DTO Data', $dto->toArray());

        return $dto;
    }
}
