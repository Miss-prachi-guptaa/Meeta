<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        return $this->execute(fn () => response()->json(['success' => true, 'message' => 'Reviews fetched successfully.', 'data' => $request->user()->reviews()->with('product:id,name,slug')->latest()->paginate(10)]));
    }

    public function store(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->validated($request, true);
            $item = $this->reviewableItem($request, $data['order_item_id']);
            if ($item->review()->exists()) {
                throw ValidationException::withMessages(['order_item_id' => ['This item has already been reviewed.']]);
            }
            $review = Review::create(['order_item_id' => $item->id, 'product_id' => $item->product_id, 'user_id' => $request->user()->id, 'rating' => $data['rating'],
                'title' => $data['title'] ?? null, 'review_text' => $data['review_text'] ?? null, 'is_verified_purchase' => true, 'is_approved' => false]);

            return response()->json(['success' => true, 'message' => 'Review submitted for approval.', 'data' => $review], 201);
        });
    }

    public function update(Request $request, int $review)
    {
        return $this->execute(function () use ($request, $review) {
            $model = $request->user()->reviews()->whereKey($review)->firstOrFail();
            $data = $this->validated($request, false);
            unset($data['order_item_id']);
            $data['is_approved'] = false;
            $model->update($data);

            return response()->json(['success' => true, 'message' => 'Review updated and submitted for approval.', 'data' => $model->fresh()]);
        });
    }

    public function destroy(Request $request, int $review)
    {
        return $this->execute(function () use ($request, $review) {
            $request->user()->reviews()->whereKey($review)->firstOrFail()->delete();

            return response()->json(['success' => true, 'message' => 'Review deleted successfully.']);
        });
    }

    private function reviewableItem(Request $request, int $id): OrderItem
    {
        $item = OrderItem::whereKey($id)->whereHas('subOrder.order', fn ($q) => $q->where('user_id', $request->user()->id)->where('order_status', 'delivered'))->first();
        if (! $item) {
            throw ValidationException::withMessages(['order_item_id' => ['Only delivered purchases can be reviewed.']]);
        }

        return $item;
    }

    private function validated(Request $request, bool $requireItem): array
    {
        return $request->validate(['order_item_id' => [$requireItem ? 'required' : 'sometimes', 'integer'], 'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:150'], 'review_text' => ['nullable', 'string', 'max:2000']]);
    }
}
