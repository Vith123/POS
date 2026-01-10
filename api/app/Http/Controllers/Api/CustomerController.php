<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $filters = [
            'search' => $request->search,
            'is_vip' => $request->is_vip,
        ];

        $customers = $this->customerService->getAll($filters, $request->per_page ?? 15);

        return CustomerResource::collection($customers);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorize('view', Customer::class);

        $customer = $this->customerService->getById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        return (new CustomerResource($customer))->response();
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $this->authorize('create', Customer::class);

        $customer = $this->customerService->create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    public function update(CustomerRequest $request, int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $this->authorize('update', $customer);

        $updated = $this->customerService->update($id, $request->validated());

        return (new CustomerResource($updated))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $this->authorize('delete', $customer);

        try {
            $this->customerService->delete($id);
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function addPoints(Request $request, int $id): JsonResponse
    {
        $this->authorize('manageLoyaltyPoints', Customer::class);

        $request->validate(['points' => 'required|integer|min:1']);

        $customer = $this->customerService->manageLoyaltyPoints($id, 'add', $request->points);

        return (new CustomerResource($customer))->response();
    }

    public function subtractPoints(Request $request, int $id): JsonResponse
    {
        $this->authorize('manageLoyaltyPoints', Customer::class);

        $request->validate(['points' => 'required|integer|min:1']);

        $customer = $this->customerService->manageLoyaltyPoints($id, 'subtract', $request->points);

        return (new CustomerResource($customer))->response();
    }
}
