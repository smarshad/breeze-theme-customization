<?php

namespace App\Http\Controllers;

use App\DTOs\PaymentMethodDO;
use App\Http\Resources\PaymentMethodResource;
use Illuminate\Http\Request;
use App\Services\PaymentMethodService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PaymentMethod\StoreRequest;
use App\Http\Requests\PaymentMethod\UpdateRequest;
use App\Models\PaymentMethod;
use Illuminate\Validation\ValidationException;
use DomainException;
use Exception;

class PaymentMethodController extends BaseController
{
    public function __construct(protected PaymentMethodService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.paymentmethod.index');
    }

    public function getAll(Request $request): JsonResponse
    {
        $perPage            = $request->get('per_page', NULL);
        $data               = $this->service->getPaginated($perPage);
        $draw               = $request->get('draw', 1);
        $response           = PaymentMethodResource::collection($data)->response()->getData(true);
        $response['draw']   = (int) $draw;
        $response['recordsTotal'] = $response['meta']['total'];
        $response['recordsFiltered'] = $response['meta']['total'];
        return response()->json($response);
    }

    public function create()
    {
        return view('admin.paymentmethod._form', ['paymentmethod' => null]);
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

    /**
     * Display the specified resource.
     */
    public function edit(string $id)
    {
        $PaymentMethod = PaymentMethod::findOrFail($id);
        $this->logInfo('edit', ['PaymentMethod' => $PaymentMethod->id]);
        return view('admin.paymentmethod._form', ['paymentmethod' => $PaymentMethod]);
    }

    /**
     * update the specified resource in storage
     */

    public function update(UpdateRequest $request) {
        try{
            $this->logInfo('Raw data for update Payment method', $request->all());

            // valiadate data and make DTO
            $dto = $this->createDTOFromRequest($request);

            $udapteData = $this->service->update($request->id, $dto);

            return $this->successResponse($udapteData,
                'Payment method successfully updated.',
                200,
                ['callback' => route('paymentmethod.list')]
            );

        } catch(ValidationException $e){
            $this->handleValidationException($e);
        } catch(DomainException $e){
            $this->handleDomainException($e);
        } catch(\Illuminate\Database\QueryException $e){
            $this->handleQueryException($e);
        } catch (Exception $e){
            $this->handleUnexpectedException($e);
        }
    }

     /**
     * Remove the specified resource from storage.
     */

     public function destroy(string $id){
        try{
            // Log raw incoming data
            $this->logInfo('Raw request data for delete payment method');

            $delete = $this->service->delete($id);

            if($delete){
                return $this->successResponse(NULL, 'Payment Method Deleted Successfully');
            }else{
                return $this->errorResponse('Some Record exist with this payment type',409);
            }
        }catch (Exception $e) {
            return $this->handleUnexpectedException($e);
        }
     }

    /**
     * Helper to create DTO from request.
     */
    private function createDTOFromRequest($request): PaymentMethodDO
    {

        $validatedData = $request->validated();

        $validatedData['created_by'] = $request->user()->id ?? NULL;

        $this->logInfo('Passing Validated Data to DTO For New Payment Method', $validatedData);

        $dto = PaymentMethodDO::fromArray($validatedData);

        $this->logInfo('DTO created For New Payment Method', $dto->toArray());

        return $dto;
    }
}
