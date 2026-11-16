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
}
