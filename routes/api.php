<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Vendor\Auth\VendorAuthController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorAddressController;
use App\Http\Controllers\Vendor\VendorBankAccountController;
use App\Http\Controllers\Vendor\VendorOnboardingController;
use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductVariantController;
use App\Http\Controllers\Vendor\VariantAttributeValueController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);
Route::middleware('auth:sanctum')->post('/profile/update', [AuthController::class, 'updateProfile']);

// Vendor Public Routes
Route::prefix('vendor')->group(function () {
    Route::post('/register', [VendorAuthController::class, 'register']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', function () {
            return response()->json([
                'success' => true,
                'message' => 'Welcome Admin'
            ]);
        });

        // Vendor Management
        Route::get('/vendors/pending', [VendorApprovalController::class, 'pending']);
        Route::get('/vendors', [VendorController::class, 'index']);
        Route::get('/vendors/{id}', [VendorApprovalController::class, 'show']);
        Route::post('/vendors/{id}/approve', [VendorApprovalController::class, 'approve']);
        Route::post('/vendors/{id}/reject', [VendorApprovalController::class, 'reject']);
        Route::post('/vendors/{id}/suspend', [VendorApprovalController::class, 'suspend']);

        // Category Management
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/tree', [CategoryController::class, 'tree']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::patch('/categories/{category}/status', [CategoryController::class, 'updateStatus']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);


        // Brand Management
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{brand}', [BrandController::class, 'show']);
Route::post('/brands', [BrandController::class, 'store']);
Route::put('/brands/{brand}', [BrandController::class, 'update']);
Route::patch('/brands/{brand}/status', [BrandController::class, 'updateStatus']);
Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);


// Attribute Management
Route::get('/attributes', [AttributeController::class, 'index']);
Route::get('/attributes/{attribute}', [AttributeController::class, 'show']);
Route::post('/attributes', [AttributeController::class, 'store']);
Route::put('/attributes/{attribute}', [AttributeController::class, 'update']);
Route::patch('/attributes/{attribute}/status', [AttributeController::class, 'updateStatus']);
Route::delete('/attributes/{attribute}', [AttributeController::class, 'destroy']);

// Attribute Value Management
Route::get('/attribute-values', [AttributeValueController::class, 'index']);
Route::get('/attribute-values/{attributeValue}', [AttributeValueController::class, 'show']);
Route::post('/attribute-values', [AttributeValueController::class, 'store']);
Route::put('/attribute-values/{attributeValue}', [AttributeValueController::class, 'update']);
Route::patch('/attribute-values/{attributeValue}/status', [AttributeValueController::class, 'updateStatus']);
Route::delete('/attribute-values/{attributeValue}', [AttributeValueController::class, 'destroy']);
    });


Route::middleware('auth:sanctum')
    ->prefix('vendor')
    ->group(function () {

    
     // Vendor Profile
     
        Route::get('/profile', [VendorProfileController::class, 'show']);
        Route::put('/profile', [VendorProfileController::class, 'update']);

       // Vendor Address
        Route::get('/address', [VendorAddressController::class, 'show']);
        Route::post('/address', [VendorAddressController::class, 'store']);
        Route::put('/address', [VendorAddressController::class, 'update']);

      // Vendor Bank Account
        Route::get('/bank-account', [VendorBankAccountController::class, 'show']);
        Route::post('/bank-account', [VendorBankAccountController::class, 'store']);
        Route::put('/bank-account', [VendorBankAccountController::class, 'update']);

             // Vendor Documents
        Route::get('/documents', [VendorDocumentController::class, 'index']);
        Route::post('/documents', [VendorDocumentController::class, 'store']);
        Route::put('/documents/{id}', [VendorDocumentController::class, 'update']);
        Route::delete('/documents/{id}', [VendorDocumentController::class, 'destroy']);

  // Vendor Onboarding
        Route::post('/submit', [VendorOnboardingController::class, 'submit']);

       // Vendor Product Management
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);


        /*
|--------------------------------------------------------------------------
| Product Images
|--------------------------------------------------------------------------
*/

Route::get('/products/{product}/images', [ProductImageController::class, 'index']);
Route::post('/products/{product}/images', [ProductImageController::class, 'store']);
Route::put('/products/{product}/images/{image}', [ProductImageController::class, 'update']);
Route::delete('/products/{product}/images/{image}', [ProductImageController::class, 'destroy']);

//Product Variants

Route::get('/products/{product}/variants', [ProductVariantController::class, 'index']);
Route::get('/products/{product}/variants/{variant}', [ProductVariantController::class, 'show']);
Route::post('/products/{product}/variants', [ProductVariantController::class, 'store']);
Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update']);
Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy']);

//Variant Attribute Values

Route::get(
    '/products/{product}/variants/{variant}/attributes',
    [VariantAttributeValueController::class, 'index']
);

Route::post(
    '/products/{product}/variants/{variant}/attributes',
    [VariantAttributeValueController::class, 'store']
);

Route::put(
    '/products/{product}/variants/{variant}/attributes/{attribute}',
    [VariantAttributeValueController::class, 'update']
);

Route::delete(
    '/products/{product}/variants/{variant}/attributes/{attribute}',
    [VariantAttributeValueController::class, 'destroy']
);
    });