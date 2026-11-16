<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return $this->execute(fn () => response()->json(['success' => true, 'message' => 'Addresses fetched successfully.', 'data' => $request->user()->addresses()->latest()->get()]));
    }

    public function show(Request $request, int $address)
    {
        return $this->execute(fn () => response()->json(['success' => true, 'message' => 'Address fetched successfully.', 'data' => $this->owned($request, $address)]));
    }

    public function store(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->validated($request);
            $address = DB::transaction(function () use ($request, $data) {
                if (! empty($data['is_default']) || ! $request->user()->addresses()->exists()) {
                    $request->user()->addresses()->update(['is_default' => false]);
                }

                return $request->user()->addresses()->create($data);
            });

            return response()->json(['success' => true, 'message' => 'Address created successfully.', 'data' => $address], 201);
        });
    }

    public function update(Request $request, int $address)
    {
        return $this->execute(function () use ($request, $address) {
            $model = $this->owned($request, $address);
            $data = $this->validated($request);
            DB::transaction(function () use ($request, $model, $data) {
                if (! empty($data['is_default'])) {
                    $request->user()->addresses()->whereKeyNot($model->id)->update(['is_default' => false]);
                }$model->update($data);
            });

            return response()->json(['success' => true, 'message' => 'Address updated successfully.', 'data' => $model->fresh()]);
        });
    }

    public function destroy(Request $request, int $address)
    {
        return $this->execute(function () use ($request, $address) {
            $model = $this->owned($request, $address);
            $wasDefault = $model->is_default;
            $model->delete();
            if ($wasDefault) {
                $request->user()->addresses()->first()?->update(['is_default' => true]);
            }

            return response()->json(['success' => true, 'message' => 'Address deleted successfully.']);
        });
    }

    public function makeDefault(Request $request, int $address)
    {
        return $this->execute(function () use ($request, $address) {
            $model = $this->owned($request, $address);
            DB::transaction(function () use ($request, $model) {
                $request->user()->addresses()->update(['is_default' => false]);
                $model->update(['is_default' => true]);
            });

            return response()->json(['success' => true, 'message' => 'Default address updated.', 'data' => $model->fresh()]);
        });
    }

    private function owned(Request $request, int $id): CustomerAddress
    {
        return $request->user()->addresses()->whereKey($id)->firstOrFail();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate(['label' => ['required', 'string', 'max:50'], 'full_name' => ['required', 'string', 'max:255'], 'phone' => ['required', 'string', 'max:20'], 'address_line_1' => ['required', 'string', 'max:255'], 'address_line_2' => ['nullable', 'string', 'max:255'], 'landmark' => ['nullable', 'string', 'max:255'], 'city' => ['required', 'string', 'max:100'], 'state' => ['required', 'string', 'max:100'], 'country' => ['nullable', 'string', 'max:100'], 'postal_code' => ['required', 'string', 'max:20'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'], 'is_default' => ['nullable', 'boolean']]);

        if (array_key_exists('country', $data) && $data['country'] === null) {
            unset($data['country']);
        }

        return $data;
    }
}
