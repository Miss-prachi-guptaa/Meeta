<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;

class VendorCatalogController extends Controller
{
  public function categories()
  {
    $categories = Category::where('is_active', true)
      ->orderBy('sort_order')
      ->orderBy('name')
      ->get([
        'id',
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
      ]);

    return response()->json([
      'success' => true,
      'message' => 'Categories fetched successfully.',
      'data' => $categories,
    ]);
  }

  public function brands()
  {
    $brands = Brand::where('status', 'active')
      ->orderBy('name')
      ->get([
        'id',
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'status',
      ]);

    return response()->json([
      'success' => true,
      'message' => 'Brands fetched successfully.',
      'data' => $brands,
    ]);
  }

  public function attributes()
  {
    $attributes = Attribute::where('status', 'active')
      ->orderBy('name')
      ->get([
        'id',
        'name',
        'slug',
        'status',
      ]);

    return response()->json([
      'success' => true,
      'message' => 'Attributes fetched successfully.',
      'data' => $attributes,
    ]);
  }

  public function attributeValues()
  {
    $values = AttributeValue::where('status', 'active')
      ->orderBy('sort_order')
      ->orderBy('value')
      ->get([
        'id',
        'attribute_id',
        'value',
        'slug',
        'sort_order',
        'status',
      ]);

    return response()->json([
      'success' => true,
      'message' => 'Attribute values fetched successfully.',
      'data' => $values,
    ]);
  }
  public function storeAttribute(\Illuminate\Http\Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:100',
    ]);

    $name = trim($validated['name']);
    $slug = \Illuminate\Support\Str::slug($name);

    $attribute = Attribute::firstOrCreate(
      ['slug' => $slug],
      ['name' => $name, 'status' => 'active']
    );

    return response()->json([
      'success' => true,
      'message' => 'Attribute saved successfully.',
      'data' => $attribute,
    ]);
  }

  public function storeAttributeValue(\Illuminate\Http\Request $request)
  {
    $validated = $request->validate([
      'attribute_id' => 'required|exists:attributes,id',
      'value' => 'required|string|max:100',
    ]);

    $value = trim($validated['value']);
    $slug = \Illuminate\Support\Str::slug($value);

    $attrValue = AttributeValue::firstOrCreate(
      ['attribute_id' => $validated['attribute_id'], 'slug' => $slug],
      ['value' => $value, 'status' => 'active', 'sort_order' => 0]
    );

    return response()->json([
      'success' => true,
      'message' => 'Attribute value saved successfully.',
      'data' => $attrValue,
    ]);
  }
}
