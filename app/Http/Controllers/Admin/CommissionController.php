<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CommissionRule;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommissionController extends Controller
{
    /**
     * List all category commission rules and global default setting.
     */
    public function index()
    {
        return $this->execute(function () {
            $rules = CommissionRule::with(['category:id,name,slug', 'vendor:id,store_name'])
                ->orderBy('id', 'desc')
                ->get();

            $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();

            // Default fallback commission rule
            $defaultRule = CommissionRule::whereNull('category_id')->whereNull('vendor_id')->first();

            return response()->json([
                'success' => true,
                'message' => 'Commission settings retrieved successfully.',
                'data' => [
                    'default_rate' => $defaultRule ? $defaultRule->commission_rate : 10.0,
                    'rules' => $rules,
                    'categories' => $categories,
                ],
            ]);
        });
    }

    /**
     * Create or update a commission rule (for category or global fallback).
     */
    public function store(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
                'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
                'title' => ['nullable', 'string', 'max:100'],
                'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
                'type' => ['nullable', Rule::in(['percentage', 'fixed'])],
                'is_active' => ['nullable', 'boolean'],
                'notes' => ['nullable', 'string', 'max:500'],
            ]);

            // Update existing if already defined for the exact category/vendor pair
            $rule = CommissionRule::updateOrCreate(
                [
                    'category_id' => $data['category_id'] ?? null,
                    'vendor_id' => $data['vendor_id'] ?? null,
                ],
                [
                    'title' => $data['title'] ?? 'Commission Rule',
                    'commission_rate' => $data['commission_rate'],
                    'type' => $data['type'] ?? 'percentage',
                    'is_active' => $data['is_active'] ?? true,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Commission rule saved successfully.',
                'data' => $rule->load(['category:id,name', 'vendor:id,store_name']),
            ]);
        });
    }

    /**
     * Delete a commission rule.
     */
    public function destroy(int $id)
    {
        return $this->execute(function () use ($id) {
            $rule = CommissionRule::findOrFail($id);
            $rule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commission rule removed successfully.',
            ]);
        });
    }
}
