<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeRequest;
use App\Http\Requests\Admin\UpdateAttributeRequest;
use App\Http\Requests\Admin\UpdateAttributeStatusRequest;
use App\Http\Resources\Admin\AttributeResource;
use App\Models\Attribute;
use App\Services\Admin\AttributeService;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function __construct(
        protected AttributeService $attributeService
    ) {}

    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {

            $attributes = $this->attributeService->getAttributes($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Attributes fetched successfully.',
                'data' => AttributeResource::collection($attributes),
            ]);
        });
    }

    public function show(Attribute $attribute)
    {
        return $this->execute(function () use ($attribute) {

            $attribute = $this->attributeService->getAttribute($attribute->id);

            return response()->json([
                'success' => true,
                'message' => 'Attribute fetched successfully.',
                'data' => new AttributeResource($attribute),
            ]);
        });
    }

    public function store(StoreAttributeRequest $request)
    {
        return $this->execute(function () use ($request) {

            $attribute = $this->attributeService->create(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Attribute created successfully.',
                'data' => new AttributeResource($attribute),
            ], 201);
        });
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        return $this->execute(function () use ($request, $attribute) {

            $attribute = $this->attributeService->update(
                $attribute,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully.',
                'data' => new AttributeResource($attribute),
            ]);
        });
    }

    public function updateStatus(UpdateAttributeStatusRequest $request, Attribute $attribute)
    {
        return $this->execute(function () use ($request, $attribute) {

            $attribute = $this->attributeService->updateStatus(
                $attribute,
                $request->status
            );

            return response()->json([
                'success' => true,
                'message' => 'Attribute status updated successfully.',
                'data' => new AttributeResource($attribute),
            ]);
        });
    }

    public function destroy(Attribute $attribute)
    {
        return $this->execute(function () use ($attribute) {

            $this->attributeService->delete($attribute);

            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully.',
            ]);
        });
    }
}