<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\WishlistResource;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return $this->execute(fn () => $this->response($this->wishlist($request)));
    }

    public function store(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate(['product_variant_id' => ['required', 'integer']]);
            $variant = $this->sellableVariant($data['product_variant_id']);
            $wishlist = $this->wishlist($request);
            $wishlist->items()->firstOrCreate(['product_variant_id' => $variant->id]);

            return $this->response($wishlist, 'Product added to wishlist.', 201);
        });
    }

    public function destroy(Request $request, int $item)
    {
        return $this->execute(function () use ($request, $item) {
            $wishlist = $this->wishlist($request);
            $wishlist->items()->whereKey($item)->firstOrFail()->delete();

            return $this->response($wishlist, 'Product removed from wishlist.');
        });
    }

    public function clear(Request $request)
    {
        return $this->execute(function () use ($request) {
            $wishlist = $this->wishlist($request);
            $wishlist->items()->delete();

            return $this->response($wishlist, 'Wishlist cleared.');
        });
    }

    public function moveToCart(Request $request, int $item)
    {
        return $this->execute(function () use ($request, $item) {
            $wishlist = $this->wishlist($request);
            $wishlistItem = $wishlist->items()->whereKey($item)->firstOrFail();
            $variant = $this->sellableVariant($wishlistItem->product_variant_id);
            $available = max(0, ($variant->inventory?->quantity ?? 0) - ($variant->inventory?->reserved_quantity ?? 0));
            if ($available < 1) {
                throw ValidationException::withMessages(['product' => ['This product is currently out of stock.']]);
            }
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id], ['status' => 'active']);
            $cartItem = $cart->items()->where('product_variant_id', $variant->id)->first();
            if (($cartItem?->quantity ?? 0) >= $available) {
                throw ValidationException::withMessages(['quantity' => ["Only {$available} item(s) available."]]);
            }
            $price = (float) $variant->price;
            if ($variant->sale_price && (float) $variant->sale_price < $price) {
                $price = (float) $variant->sale_price;
            }
            $cart->items()->updateOrCreate(['product_variant_id' => $variant->id], ['quantity' => ($cartItem?->quantity ?? 0) + 1, 'unit_price' => $price]);
            $wishlistItem->delete();

            return $this->response($wishlist, 'Product moved to cart.');
        });
    }

    private function wishlist(Request $request): Wishlist
    {
        return Wishlist::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function sellableVariant(int $id): ProductVariant
    {
        return ProductVariant::with(['inventory', 'product'])->whereKey($id)->where('is_active', true)->whereHas('product', fn ($q) => $q->where('status', 'active')->whereNotNull('approved_at')->whereHas('vendor', fn ($v) => $v->approved()))->whereHas('product.category', fn ($q) => $q->active())->whereHas('product.images')->firstOrFail();
    }

    private function response(Wishlist $wishlist, string $message = 'Wishlist fetched successfully.', int $status = 200)
    {
        $wishlist->load(['items.productVariant.inventory', 'items.productVariant.product.category', 'items.productVariant.product.brand', 'items.productVariant.product.vendor', 'items.productVariant.product.images']);

        return response()->json(['success' => true, 'message' => $message, 'data' => (new WishlistResource($wishlist))->resolve()], $status);
    }
}
