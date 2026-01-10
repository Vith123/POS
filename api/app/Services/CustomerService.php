<?php
namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function getAll(array $filters, int $perPage = 15)
    {
        $query = Customer::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById(int $id): ?Customer
    {
        return Customer::with(['sales' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): Customer
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(int $id): bool
    {
        return Customer::findOrFail($id)->delete();
    }

    public function manageLoyaltyPoints(int $id, string $action, int $points): Customer
    {
        $customer = Customer::findOrFail($id);

        if ($action === 'add') {
            $customer->increment('loyalty_points', $points);
        } else {
            $customer->decrement('loyalty_points', $points);
        }

        return $customer->fresh();
    }
}
