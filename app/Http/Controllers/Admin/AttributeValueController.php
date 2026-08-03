<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeValueRequest;
use App\Http\Requests\Admin\UpdateAttributeValueRequest;
use App\Http\Requests\Admin\UpdateAttributeValueStatusRequest;
use App\Http\Resources\Admin\AttributeValueResource;
use App\Models\AttributeValue;
use App\Services\Admin\AttributeValueService;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function __construct(
        protected AttributeValueService $attributeValueService
    ) {}

    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {

            $attributeValues = $this->attributeValueService
                ->getAttributeValues($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Attribute values fetched successfully.',
                'data' => AttributeValueResource::collection($attributeValues),
            ]);
        });
    }

    public function show(AttributeValue $attributeValue)
    {
        return $this->execute(function () use ($attributeValue) {

            $attributeValue = $this->attributeValueService
                ->getAttributeValue($attributeValue->id);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value fetched successfully.',
                'data' => new AttributeValueResource($attributeValue),
            ]);
        });
    }

    public function store(StoreAttributeValueRequest $request)
    {
        return $this->execute(function () use ($request) {

            $attributeValue = $this->attributeValueService
                ->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Attribute value created successfully.',
                'data' => new AttributeValueResource($attributeValue),
            ], 201);
        });
    }

    public function update(
        UpdateAttributeValueRequest $request,
        AttributeValue $attributeValue
    ) {
        return $this->execute(function () use ($request, $attributeValue) {

            $attributeValue = $this->attributeValueService
                ->update($attributeValue, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Attribute value updated successfully.',
                'data' => new AttributeValueResource($attributeValue),
            ]);
        });
    }

    public function updateStatus(
        UpdateAttributeValueStatusRequest $request,
        AttributeValue $attributeValue
    ) {
        return $this->execute(function () use ($request, $attributeValue) {

            $attributeValue = $this->attributeValueService
                ->updateStatus($attributeValue, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value status updated successfully.',
                'data' => new AttributeValueResource($attributeValue),
            ]);
        });
    }

    public function destroy(AttributeValue $attributeValue)
    {
        return $this->execute(function () use ($attributeValue) {

            $this->attributeValueService->delete($attributeValue);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value deleted successfully.',
            ]);
        });
    }
}