<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductApprovalController as AdminProductApprovalController;
use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\NewsletterController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Vendor\Auth\VendorAuthController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\InventoryController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ProductApprovalController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Vendor\ProductVariantController;
use App\Http\Controllers\Vendor\VariantAttributeValueController;
use App\Http\Controllers\Vendor\VendorAddressController;
use App\Http\Controllers\Vendor\VendorBankAccountController;
use App\Http\Controllers\Vendor\VendorCatalogController;
use App\Http\Controllers\Vendor\VendorDocumentController;
use App\Http\Controllers\Vendor\VendorOnboardingController;
use App\Http\Controllers\Vendor\VendorProfileController;
use Illuminate\Support\Facades\Route;

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

        Route::get('/dashboard', [
            DashboardController::class,
            'index',
        ]);

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

        // Product Approval Management
        Route::get('/products/pending', [AdminProductApprovalController::class, 'index']);
        Route::get('/products/{product}', [AdminProductApprovalController::class, 'show']);
        Route::post('/products/{product}/approve', [AdminProductApprovalController::class, 'approve']);
        Route::post('/products/{product}/reject', [AdminProductApprovalController::class, 'reject']);
    });

Route::middleware('auth:sanctum')
    ->prefix('vendor')
    ->group(function () {
        // Vendor Dashboard
        Route::get('/dashboard', [VendorDashboardController::class, 'index']);

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

        Route::get('/categories', [VendorCatalogController::class, 'categories']);
        Route::get('/brands', [VendorCatalogController::class, 'brands']);
        Route::get('/attributes', [
            VendorCatalogController::class,
            'attributes',
        ]);

        Route::get('/attribute-values', [
            VendorCatalogController::class,
            'attributeValues',
        ]);
        /*
|--------------------------------------------------------------------------
| Product Images
|--------------------------------------------------------------------------
*/

        Route::get('/products/{product}/images', [ProductImageController::class, 'index']);
        Route::post('/products/{product}/images', [ProductImageController::class, 'store']);
        Route::put('/products/{product}/images/{image}', [ProductImageController::class, 'update']);
        Route::delete('/products/{product}/images/{image}', [ProductImageController::class, 'destroy']);

        // Product Variants

        Route::get('/products/{product}/variants', [ProductVariantController::class, 'index']);
        Route::get('/products/{product}/variants/{variant}', [ProductVariantController::class, 'show']);
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'store']);
        Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update']);
        Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy']);

        // Variant Attribute Values

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

        /*
|--------------------------------------------------------------------------
| Inventory
|--------------------------------------------------------------------------
*/

        Route::get(
            '/products/{product}/variants/{variant}/inventory',
            [InventoryController::class, 'show']
        );

        Route::post(
            '/products/{product}/variants/{variant}/inventory',
            [InventoryController::class, 'store']
        );

        Route::put(
            '/products/{product}/variants/{variant}/inventory',
            [InventoryController::class, 'update']
        );

        /*
|--------------------------------------------------------------------------
| Product Approval
|--------------------------------------------------------------------------
*/

        Route::post(
            '/products/{product}/submit',
            [ProductApprovalController::class, 'submit']
        );

        /*
        |--------------------------------------------------------------------------
        | Vendor Orders
        |--------------------------------------------------------------------------
        | Essential fulfilment APIs: receive, accept/reject, pack, request pickup,
        | and simulate courier tracking until a real logistics API is connected.
        */
        Route::get('/orders', [VendorOrderController::class, 'index']);
        Route::get('/orders/{subOrder}', [VendorOrderController::class, 'show']);
        Route::patch('/orders/{subOrder}/status', [VendorOrderController::class, 'updateStatus']);
        Route::post('/orders/{subOrder}/shipment', [VendorOrderController::class, 'createShipment']);
        Route::patch('/shipments/{shipment}/status', [VendorOrderController::class, 'updateShipmentStatus']);
    });

// Customer Public Routes
Route::get('/home', [HomeController::class, 'index']);
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->middleware('throttle:5,1');
Route::prefix('customer')->group(function () {
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/products', [CatalogController::class, 'index']);
    Route::get('/products/{slug}', [CatalogController::class, 'show']);
    Route::get('/search/suggestions', [CatalogController::class, 'suggestions']);
});

Route::middleware('auth:sanctum')->prefix('customer')->group(function () {
    Route::get('/account/dashboard', [AccountController::class, 'dashboard']);
    Route::get('/account/profile', [AccountController::class, 'profile']);
    Route::match(['patch', 'post'], '/account/profile', [AccountController::class, 'updateProfile']);
    Route::patch('/account/password', [AccountController::class, 'updatePassword']);
    Route::get('/account/preferences', [AccountController::class, 'preferences']);
    Route::patch('/account/preferences', [AccountController::class, 'updatePreferences']);
    Route::get('/account/supported-artisans', [AccountController::class, 'supportedArtisans']);
    Route::post('/account/logout-all', [AccountController::class, 'logoutAll']);
    Route::delete('/account', [AccountController::class, 'destroy']);
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::patch('/cart/items/{item}', [CartController::class, 'update']);
    Route::post('/cart/items/{item}/move-to-wishlist', [CartController::class, 'moveToWishlist']);
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/items', [WishlistController::class, 'store']);
    Route::post('/wishlist/items/{item}/move-to-cart', [WishlistController::class, 'moveToCart']);
    Route::delete('/wishlist/items/{item}', [WishlistController::class, 'destroy']);
    Route::delete('/wishlist', [WishlistController::class, 'clear']);

    Route::apiResource('/addresses', AddressController::class);
    Route::patch('/addresses/{address}/default', [AddressController::class, 'makeDefault']);

    Route::get('/checkout', [CheckoutController::class, 'preview']);
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking']);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::patch('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
});
