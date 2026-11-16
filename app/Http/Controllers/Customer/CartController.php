<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CartResource;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function show(Request $request)
    {
        return $this->execute(fn () => $this->response($this->cart($request)));
    }

    public function store(Request $request)
    {
        return $this->execute(function () use ($request) {
            // Use case: a customer must choose an exact sellable option combination before adding it.
            $data = $request->validate(
                ['product_variant_id' => ['required', 'integer'], 'quantity' => ['nullable', 'integer', 'min:1', 'max:99']],
                ['product_variant_id.required' => 'Please select the required product options before adding this item to your cart.']
            );
            $variant = $this->sellableVariant($data['product_variant_id']);
            $quantity = $data['quantity'] ?? 1;
            $available = $this->available($variant);
            $cart = $this->cart($request);
            $item = $cart->items()->where('product_variant_id', $variant->id)->first();
            $newQuantity = ($item?->quantity ?? 0) + $quantity;
            if ($newQuantity > $available) {
                throw ValidationException::withMessages(['quantity' => ["Only {$available} item(s) available."]]);
            }
            $price = $this->price($variant);
            $cart->items()->updateOrCreate(['product_variant_id' => $variant->id], ['quantity' => $newQuantity, 'unit_price' => $price]);

            return $this->response($cart, 'Product added to cart.', 201);
        });
    }

    public function update(Request $request, int $item)
    {
        return $this->execute(function () use ($request, $item) {
            $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
            $cart = $this->cart($request);
            $cartItem = $cart->items()->whereKey($item)->with('productVariant.inventory')->firstOrFail();
            $available = $this->available($cartItem->productVariant);
            if ($data['quantity'] > $available) {
                throw ValidationException::withMessages(['quantity' => ["Only {$available} item(s) available."]]);
            }
            $cartItem->update(['quantity' => $data['quantity'], 'unit_price' => $this->price($cartItem->productVariant)]);

            return $this->response($cart, 'Cart updated.');
        });
    }

    public function destroy(Request $request, int $item)
    {
        return $this->execute(function () use ($request, $item) {
            $this->cart($request)->items()->whereKey($item)->firstOrFail()->delete();

            return $this->response($this->cart($request), 'Item removed from cart.');
        });
    }

    public function clear(Request $request)
    {
        return $this->execute(function () use ($request) {
            $cart = $this->cart($request);
            $cart->items()->delete();

            return $this->response($cart, 'Cart cleared.');
        });
    }

    public function moveToWishlist(Request $request, int $item)
    {
        return $this->execute(function () use ($request, $item) {
            $cart = $this->cart($request);
            $cartItem = $cart->items()->whereKey($item)->firstOrFail();
            $wishlist = Wishlist::firstOrCreate(['user_id' => $request->user()->id]);
            $wishlist->items()->firstOrCreate(['product_variant_id' => $cartItem->product_variant_id]);
            $cartItem->delete();

            return $this->response($cart, 'Product moved to wishlist.');
        });
    }

    private function cart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id], ['status' => 'active']);
    }

    private function sellableVariant(int $id): ProductVariant
    {
        $variant = ProductVariant::with(['inventory', 'product.vendor'])->whereKey($id)->first();

        if (! $variant || ! $variant->is_active || ! $variant->product || $variant->product->status !== 'active' || ! $variant->product->approved_at
            || ! $variant->product->vendor?->is_approved || ! $variant->product->category?->is_active || ! $variant->product->images()->exists()) {
            throw ValidationException::withMessages(['product_variant_id' => ['The selected product option is unavailable. Please choose another option.']]);
        }

        if ($this->available($variant) < 1) {
            throw ValidationException::withMessages(['product_variant_id' => ['The selected product option is out of stock. Please choose another option.']]);
        }

        return $variant;
    }

    private function available(ProductVariant $variant): int
    {
        return max(0, ($variant->inventory?->quantity ?? 0) - ($variant->inventory?->reserved_quantity ?? 0));
    }

    private function price(ProductVariant $variant): float
    {
        $price = (float) $variant->price;
        $sale = $variant->sale_price === null ? null : (float) $variant->sale_price;

        return $sale && $sale < $price ? $sale : $price;
    }

    private function response(Cart $cart, string $message = 'Cart fetched successfully.', int $status = 200)
    {
        $cart->load(['items.productVariant.inventory', 'items.productVariant.product.images', 'items.productVariant.product.vendor']);

        return response()->json(['success' => true, 'message' => $message, 'data' => (new CartResource($cart))->resolve()], $status);
    }
}
