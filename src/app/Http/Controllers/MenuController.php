<?php

namespace App\Http\Controllers;

use App\DTOs\MenuDTO;
use App\Http\Requests\MenuStoreRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\User;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;

class MenuController extends BaseController
{
    public function __construct(
        protected MenuService $menuService
    ) {}

    public function index(): View
    {
        return view('admin.menu.index');
    }

    public function getAll(Request $request): JsonResponse
    {

        $perPage            = $request->get('per_page', NULL);
        $data               = $this->menuService->getPaginated($perPage);
        $draw               = $request->get('draw', 1);
        $response           = MenuResource::collection($data)->response()->getData(true);
        $response['draw']   = (int) $draw;
        $response['recordsTotal'] = $response['meta']['total'];
        $response['recordsFiltered'] = $response['meta']['total'];
        return response()->json($response);
    }

    public function create(): View
    {
        $menus = $this->menuService->getMenus();
        $permissions = $this->menuService->getAllPermissions();
        $parentMenus = $this->menuService->flattenMenu($menus);

        // dd($parentMenus);
        return view('admin.menu.create', compact('parentMenus', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuStoreRequest $request)
    {

        try {
            // Log raw incoming data
            $this->logInfo('Raw data for new Menu', $request->all());

            // Validate and prepare DTO
            $dto = $this->createDTOFromRequest($request);

            // Service layer creation
            $data  = $this->menuService->createMenu($dto);

            return $this->successResponse(new MenuResource($data), 'Menu Succesfully Created', 201, ['redirect' => route('menu.index')]);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (Exception $e) {
            return $this->handleUnexpectedException($e);
        }
    }
    private function createDTOFromRequest(Request $request): MenuDTO
    {
        $validatedData = $request->validated();
        // Assuming the user is authenticated and has an ID
        $validatedData['created_by'] = $request->user()->id ?? null;

        $this->logInfo('Passing Request to DTO', $validatedData);

        $dto = MenuDTO::fromArray($validatedData);

        $this->logInfo('DTO created:', $dto->toArray());

        return $dto;
    }
}
