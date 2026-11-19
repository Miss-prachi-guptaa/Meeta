<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function preview(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->validatedCheckout($request, false);
            $context = $this->checkoutContext($request, $data);
            $summary = $this->summary($context['items'], $data['shipping_method'] ?? 'standard', $context['mode']);

            return response()->json(['success' => true, 'message' => 'Checkout summary fetched.', 'data' => $summary]);
        });
    }

    public function placeOrder(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->validatedCheckout($request, true);
            $address = $request->user()->addresses()->whereKey($data['address_id'])->firstOrFail();
            $context = $this->checkoutContext($request, $data);
            $summary = $this->summary($context['items'], $data['shipping_method'], $context['mode']);

            $order = DB::transaction(function () use ($request, $data, $address, $context, $summary) {
                $items = $context['items'];
                foreach ($items as $item) {
                    $variant = $item->productVariant;
                    if (! $this->isSellable($variant)) {
                        throw ValidationException::withMessages(['checkout' => ['An item is no longer available. Please choose another option.']]);
                    }
                    $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->lockForUpdate()->first();
                    $available = max(0, ($inventory?->quantity ?? 0) - ($inventory?->reserved_quantity ?? 0));
                    if ($item->quantity > $available) {
                        throw ValidationException::withMessages(['checkout' => ["{$variant->product->name} has only {$available} item(s) available."]]);
                    }
                }

                $order = Order::create([
                    'order_number' => 'MTA-'.now()->format('ymd').'-'.strtoupper(Str::random(8)), 'user_id' => $request->user()->id,
                    'shipping_full_name' => $address->full_name, 'shipping_phone' => $address->phone, 'shipping_address_line_1' => $address->address_line_1,
                    'shipping_address_line_2' => $address->address_line_2, 'shipping_landmark' => $address->landmark, 'shipping_city' => $address->city,
                    'shipping_state' => $address->state, 'shipping_country' => $address->country, 'shipping_postal_code' => $address->postal_code,
                    'subtotal' => $summary['subtotal'], 'discount_amount' => 0, 'tax_amount' => 0, 'shipping_amount' => $summary['shipping_amount'],
                    'total_amount' => $summary['total'], 'payment_status' => ($data['payment_method'] === 'razorpay' ? 'paid' : 'pending'), 'order_status' => 'confirmed', 'notes' => $data['notes'] ?? null,
                ]);

                foreach ($items->groupBy(fn ($item) => $item->productVariant->product->vendor_id) as $vendorId => $vendorItems) {
                    $vendorSubtotal = round($vendorItems->sum(fn ($item) => (float) $item->unit_price * $item->quantity), 2);
                    $subOrder = $order->subOrders()->create([
                        'sub_order_number' => $order->order_number.'-'.$vendorId, 'vendor_id' => $vendorId, 'subtotal' => $vendorSubtotal,
                        'discount_amount' => 0, 'tax_amount' => 0, 'shipping_amount' => 0, 'total_amount' => $vendorSubtotal,
                        'order_status' => 'confirmed', 'confirmed_at' => now(),
                    ]);
                    foreach ($vendorItems as $item) {
                        $variant = $item->productVariant;
                        $product = $variant->product;
                        $subOrder->orderItems()->create([
                            'product_id' => $product->id, 'product_variant_id' => $variant->id, 'product_name' => $product->name,
                            'variant_name' => $variant->name, 'sku' => $variant->sku, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price,
                            'discount_amount' => 0, 'tax_amount' => 0, 'total_amount' => round((float) $item->unit_price * $item->quantity, 2),
                        ]);
                        Inventory::where('product_variant_id', $variant->id)->decrement('quantity', $item->quantity);
                    }
                }

                $isRzp = ($data['payment_method'] === 'razorpay');
                $payment = Payment::create([
                    'payment_number' => 'PAY-'.strtoupper(Str::random(12)),
                    'order_id' => $order->id,
                    'user_id' => $request->user()->id,
                    'amount' => $summary['total'],
                    'currency' => 'INR',
                    'payment_method' => $data['payment_method'],
                    'status' => $isRzp ? 'completed' : 'pending',
                    'paid_at' => $isRzp ? now() : null,
                ]);

                if ($isRzp && !empty($data['razorpay_payment_id'])) {
                    \App\Models\PaymentTransaction::create([
                        'payment_id' => $payment->id,
                        'transaction_id' => $data['razorpay_payment_id'],
                        'gateway' => 'razorpay',
                        'transaction_type' => 'payment',
                        'amount' => $summary['total'],
                        'status' => 'success',
                        'gateway_response' => [
                            'razorpay_payment_id' => $data['razorpay_payment_id'],
                            'razorpay_order_id' => $data['razorpay_order_id'] ?? null,
                            'razorpay_signature' => $data['razorpay_signature'] ?? null,
                        ],
                        'processed_at' => now(),
                    ]);
                }
                if ($context['mode'] === 'cart') {
                    $context['cart']->items()->delete();
                }

                Notification::create([
                    'user_id' => $request->user()->id, 'title' => 'Order confirmed', 'message' => "Order {$order->order_number} has been placed successfully.",
                    'type' => 'order_confirmed', 'channel' => 'database', 'reference_type' => 'order', 'reference_id' => $order->id,
                ]);
                $order->subOrders()->with('vendor:id,user_id')->get()->each(function ($subOrder) use ($order) {
                    if ($subOrder->vendor?->user_id) {
                        Notification::create([
                            'user_id' => $subOrder->vendor->user_id, 'title' => 'New order received',
                            'message' => "New order {$subOrder->sub_order_number} from {$order->order_number} requires your action.",
                            'type' => 'vendor_order_received', 'channel' => 'database', 'reference_type' => 'sub_order', 'reference_id' => $subOrder->id,
                        ]);
                    }
                });

                return $order;
            });

            return response()->json(['success' => true, 'message' => 'Order placed successfully.', 'data' => [
                'id' => $order->id, 'order_number' => $order->order_number, 'total' => $order->total_amount, 'status' => $order->order_status,
            ]], 201);
        });
    }

    private function validatedCheckout(Request $request, bool $placingOrder): array
    {
        return $request->validate([
            'mode' => ['nullable', Rule::in(['cart', 'buy_now'])],
            'product_variant_id' => ['nullable', 'required_if:mode,buy_now', 'integer'],
            'quantity' => ['nullable', 'required_if:mode,buy_now', 'integer', 'min:1', 'max:99'],
            'shipping_method' => [$placingOrder ? 'required' : 'nullable', Rule::in(['standard', 'express'])],
            'address_id' => [$placingOrder ? 'required' : 'nullable', 'integer'],
            'payment_method' => [$placingOrder ? 'required' : 'nullable', Rule::in(['cod', 'razorpay'])],
            'razorpay_payment_id' => ['nullable', 'string', 'required_if:payment_method,razorpay'],
            'razorpay_order_id' => ['nullable', 'string'],
            'razorpay_signature' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function checkoutContext(Request $request, array $data): array
    {
        $mode = $data['mode'] ?? 'cart';
        if ($mode === 'buy_now') {
            $variant = ProductVariant::with(['inventory', 'product.images', 'product.category', 'product.vendor'])
                ->whereKey($data['product_variant_id'])->first();
            if (! $this->isSellable($variant)) {
                throw ValidationException::withMessages(['product_variant_id' => ['The selected product option is unavailable. Please choose another option.']]);
            }
            $quantity = (int) ($data['quantity'] ?? 1);
            $available = max(0, ($variant->inventory?->quantity ?? 0) - ($variant->inventory?->reserved_quantity ?? 0));
            if ($quantity > $available) {
                throw ValidationException::withMessages(['quantity' => ["Only {$available} item(s) available."]]);
            }
            $item = (object) ['id' => null, 'product_variant_id' => $variant->id, 'productVariant' => $variant,
                'quantity' => $quantity, 'unit_price' => $this->price($variant)];

            return ['mode' => 'buy_now', 'items' => collect([$item]), 'cart' => null];
        }

        $cart = $this->loadedCart($request);

        return ['mode' => 'cart', 'items' => $cart->items, 'cart' => $cart];
    }

    private function summary(Collection $items, string $shippingMethod, string $mode): array
    {
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
        }
        $subtotal = round($items->sum(fn ($item) => (float) $item->unit_price * $item->quantity), 2);
        $shipping = $shippingMethod === 'express' ? 150.0 : ($subtotal >= 999 ? 0.0 : 49.0);

        return ['mode' => $mode, 'items' => $items->map(fn ($item) => [
            'id' => $item->id, 'product' => $item->productVariant->product->name, 'variant' => $item->productVariant->name,
            'variant_id' => $item->product_variant_id, 'image' => $item->productVariant->product->images->sortByDesc('is_primary')->first()?->public_url,
            'quantity' => $item->quantity, 'unit_price' => (float) $item->unit_price,
            'line_total' => round((float) $item->unit_price * $item->quantity, 2),
        ])->values(), 'subtotal' => $subtotal, 'shipping_method' => $shippingMethod, 'shipping_amount' => $shipping,
            'discount_amount' => 0, 'tax_amount' => 0, 'total' => $subtotal + $shipping];
    }

    private function loadedCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id], ['status' => 'active'])->load([
            'items.productVariant.inventory', 'items.productVariant.product.images',
            'items.productVariant.product.category', 'items.productVariant.product.vendor',
        ]);
    }

    private function isSellable(?ProductVariant $variant): bool
    {
        return $variant && $variant->is_active && $variant->product && $variant->product->status === 'active'
            && $variant->product->approved_at && $variant->product->vendor?->is_approved
            && $variant->product->category?->is_active && $variant->product->images->isNotEmpty();
    }

    private function price(ProductVariant $variant): float
    {
        $price = (float) $variant->price;
        $sale = $variant->sale_price === null ? null : (float) $variant->sale_price;

        return $sale && $sale < $price ? $sale : $price;
    }

    public function createRazorpayOrder(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->validatedCheckout($request, false);
            $context = $this->checkoutContext($request, $data);
            $summary = $this->summary($context['items'], $data['shipping_method'] ?? 'standard', $context['mode']);

            $amountInPaise = (int) round($summary['total'] * 100);
            $receipt = 'RCPT-' . strtoupper(Str::random(10));

            $key = config('services.razorpay.key');
            $secret = config('services.razorpay.secret');

            if (!$key || !$secret) {
                throw ValidationException::withMessages(['razorpay' => ['Razorpay credentials not configured.']]);
            }

            $response = Http::withBasicAuth($key, $secret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => $receipt,
                    'payment_capture' => 1,
                ]);

            if (!$response->successful()) {
                throw ValidationException::withMessages(['razorpay' => ['Failed to initiate Razorpay order: ' . ($response->json('error.description') ?? $response->body())]]);
            }

            $rzpOrder = $response->json();

            return response()->json([
                'success' => true,
                'message' => 'Razorpay order created.',
                'data' => [
                    'razorpay_order_id' => $rzpOrder['id'],
                    'amount' => $rzpOrder['amount'],
                    'currency' => $rzpOrder['currency'],
                    'key' => $key,
                    'order_summary' => $summary,
                ],
            ]);
        });
    }
}
