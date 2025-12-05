<?php

namespace App\Http\Controllers;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Category\Store;
use App\Http\Requests\Category\Update;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function list(Request $request): JsonResponse
    {
        $perPage            = $request->get('per_page', 5);
        $categories         = $this->categoryService->getCategoriesPaginated($perPage);
        $draw               = $request->get('draw', 1);
        $response           = CategoryResource::collection($categories)->response()->getData(true);
        $response['draw']   = (int) $draw;
        $response['recordsTotal'] = $response['meta']['total'];
        $response['recordsFiltered'] = $response['meta']['total'];
        return response()->json($response);
        // Using collection::make for paginated resource response
        // return CategoryResource::collection($categories);
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

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category._form', compact('category'));
    }


    public function update(Update $request)
    {
        try {
            // Log raw incoming data
            logAction('Raw request data for update Category', 'info', $request->all());

            // Validate
            $validatedData = $request->validated();
            $validatedData['created_by'] = $request->user()->id;
            logAction('Passing update category data to DTO', 'info', $validatedData);

            // DTO creation
            $dto = CategoryDTO::fromArray($validatedData);
            logAction('DTO updated category Data created:', 'info', $dto->toArray());

            // Service layer creation
            $category = $this->categoryService->updateCategory($request->id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
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

            logAction('Domain Error', 'error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {

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

    public function destroy($id)
    {
        try {
            // Log raw incoming data
            logAction('Raw request data for delete Category', 'info', [$id]);
            $category = $this->categoryService->deleteCategory($id);

            if($category){
                return response()->json([
                    'success' => true,
                    'message' => 'category deleted sucessfully.',
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Some record exist with this category.',
                ], 500);
            }
        } catch (\Exception $e) {

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
