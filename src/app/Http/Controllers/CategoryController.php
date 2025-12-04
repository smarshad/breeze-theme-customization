<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Category\Store;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\DTOs\CategoryDTO;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function list(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $categories = $this->categoryService->getCategoriesPaginated($perPage);
        
        // Using collection::make for paginated resource response
        return CategoryResource::collection($categories);
    }

    public function index()
    {
        return view('admin.category.index');
    }

    public function create()
    {
        return view('admin.category._form', ['category' => null]);
    }

    public function store(Store $request)
    {
        try {
            // Log raw incoming data
            logAction('Raw request data for New Category', 'info', $request->all());

            // Validate
            $validatedData = $request->validated();
            $validatedData['created_by'] = $request->user()->id;
            logAction('Passing category data to DTO', 'info', $validatedData);

            // DTO creation
            $dto = CategoryDTO::fromArray($validatedData);
            logAction('DTO category Data created:', 'info', $dto->toArray());

            // Service layer creation
            $category = $this->categoryService->createCategory($dto);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'callback' => route('category.list'),
                'data' => new CategoryResource($category),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            // Validation-specific errors
            logAction('Validation Error', 'error', $e->errors());

            return response()->json([
                'success' => false,
                'message' => 'Validation Failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\DomainException $e) {

            // Domain / business logic errors thrown by service or DTO
            logAction('Domain Error', 'error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {

            // Catch-all for unexpected errors
            logAction('Unexpected Error', 'error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }
}
