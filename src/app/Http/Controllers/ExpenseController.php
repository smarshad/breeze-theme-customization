<?php

namespace App\Http\Controllers;

use App\DTOs\ExpenseDTO;
use App\Http\Requests\Expense\StoreRequest;
use App\Http\Requests\Expense\UpdateRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\PaymentMethod;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Display the specified resource.
     */
    public function edit(string $id)
    {
        $expense        = Expense::findOrFail($id);
        $categories     = Category::all();
        $expenseTypes   = ExpenseType::all();
        $paymentMethods = PaymentMethod::all();
        return view('admin.expense.create', compact('categories', 'expenseTypes', 'paymentMethods', 'expense'));
    }

    /**
     * update the specified resource in storage
     */

    public function update(UpdateRequest $request)
    {
        try {
            $this->logInfo('Raw data for update expense', $request->all());

            // valiadate data and make DTO
            $dto = $this->createDTOFromRequest($request);

            $udapteData = $this->service->update($request->id, $dto);

            return $this->successResponse(
                $udapteData,
                'Expense successfully updated.',
                200,
                ['redirect' => route('expense.index')]
            );
        } catch (ValidationException $e) {
            $this->handleValidationException($e);
        } catch (DomainException $e) {
            $this->handleDomainException($e);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->handleQueryException($e);
        } catch (Exception $e) {
            $this->handleUnexpectedException($e);
        }
    }

    private function createDTOFromRequest($request): ExpenseDTO
    {
        $validatedData  = $request->validated();

        // Assuming 'created_by' should not be updated, but we keep the original logic for now.
        // Note: For an update, 'created_by' should typically not be changed, 
        // but rather 'updated_by' if you track that.
        $validatedData['created_by']  = $request->user()->id ?? NULL;

        $existingFilePath = null;
        if ($request->filled('id')) {
            // Assuming 'Expense' model is available in this scope
            $existingExpense = Expense::find($request->id);
            if ($existingExpense) {
                $existingFilePath = $existingExpense->file_path;
            }
        }

        // If file uploaded, store it and replace file_path with storage path string
        if ($request->hasFile('uploaded_file')) {
            $file = $request->file('uploaded_file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension; // New filename using timestamp

            // 1. Store the new file
            $newFilePath = $file->storeAs(
                'expenses/files', // Storage path
                $fileName,       // New filename
                'public'         // Disk
            );

            $validatedData['file_path'] = $newFilePath;

            // 2. Delete the old file if it exists
            if ($existingFilePath) {
                // Use the Storage facade to delete the old file from the 'public' disk
                Storage::disk('public')->delete($existingFilePath);
                $this->logInfo('Old expense file deleted during update', ['path' => $existingFilePath]);
            }
        } else {
            // If no new file is uploaded, retain the existing file path or set to NULL
            $validatedData['file_path'] = $validatedData['file_path'] ?? $existingFilePath ?? NULL;
        }

        $this->logInfo('Passing Validated Data to DTO For New Expense', $validatedData);

        $dto = ExpenseDTO::fromArray($validatedData);

        $this->logInfo('New Expense DTO Data', $dto->toArray());

        return $dto;
    }

    public function destroy(string $id)
    {
        try {
            // Log raw incoming data
            $this->logInfo('Raw request data for delete expense');

            $existingExpense = Expense::find($id);
            if ($existingExpense) {
                if ($existingExpense->file_path) {
                    Storage::disk('public')->delete($existingExpense->file_path);
                    $this->logInfo('Old expense file deleted during delete', ['path' => $existingExpense->file_path]);
                }
            }

            $delete = $this->service->delete($id);

            if ($delete) {
                return $this->successResponse(NULL, 'expense Deleted Successfully');
            } else {
                return $this->errorResponse('Error While Deleting expense', 409);
            }
        } catch (Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }
}
