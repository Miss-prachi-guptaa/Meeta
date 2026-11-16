<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        return $this->execute(function () use ($request) {
            $user = $request->user();
            $recentOrders = $user->orders()->latest()->limit(3)->with(['subOrders.orderItems.product.images'])->get();
            $vendors = Vendor::query()->whereHas('subOrders.order', fn ($query) => $query->where('user_id', $user->id))
                ->select('id', 'store_name', 'store_slug', 'logo', 'description')->distinct()->limit(6)->get()
                ->map(fn ($vendor) => [...$vendor->toArray(), 'logo_url' => $this->publicUrl($vendor->logo)]);

            return response()->json(['success' => true, 'message' => 'Account dashboard fetched successfully.', 'data' => [
                'profile' => $this->profileData($user),
                'counts' => [
                    'orders' => $user->orders()->count(),
                    'wishlist_items' => $user->wishlist?->items()->count() ?? 0,
                    'addresses' => $user->addresses()->count(),
                    'unread_notifications' => $user->userNotifications()->where('is_read', false)->count(),
                    'reviews' => $user->reviews()->count(),
                ],
                'recent_orders' => $recentOrders,
                'supported_artisans' => $vendors,
                'payment_methods' => ['available' => false, 'gateway_required' => true],
            ]]);
        });
    }

    public function profile(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Profile fetched successfully.', 'data' => $this->profileData($request->user())]);
    }

    public function updateProfile(Request $request)
    {
        return $this->execute(function () use ($request) {
            $user = $request->user();
            $data = $request->validate([
                'first_name' => ['sometimes', 'required', 'string', 'max:100'],
                'last_name' => ['nullable', 'string', 'max:100'],
                'display_name' => ['nullable', 'string', 'max:150'],
                'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
                'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
                'date_of_birth' => ['nullable', 'date', 'before:today'],
                'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);

            if ($request->hasFile('profile_image')) {
                if ($user->profile_image && ! str_starts_with($user->profile_image, 'http')) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $data['profile_image'] = $request->file('profile_image')->store('customers/profile', 'public');
            }
            $user->update($data);

            return response()->json(['success' => true, 'message' => 'Profile updated successfully.', 'data' => $this->profileData($user->fresh())]);
        });
    }

    public function updatePassword(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
                'logout_other_devices' => ['sometimes', 'boolean'],
            ]);
            if (! Hash::check($data['current_password'], $request->user()->password)) {
                throw ValidationException::withMessages(['current_password' => ['The current password is incorrect.']]);
            }
            $request->user()->update(['password' => $data['password']]);
            if ($data['logout_other_devices'] ?? false) {
                $currentTokenId = $request->user()->currentAccessToken()?->id;
                $request->user()->tokens()->when($currentTokenId, fn ($query) => $query->whereKeyNot($currentTokenId))->delete();
            }

            return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
        });
    }

    public function preferences(Request $request)
    {
        $preferences = $request->user()->preferences()->firstOrCreate();

        return response()->json(['success' => true, 'message' => 'Preferences fetched successfully.', 'data' => $preferences]);
    }

    public function updatePreferences(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'order_updates' => ['sometimes', 'boolean'],
                'promotional_emails' => ['sometimes', 'boolean'],
                'artisan_stories' => ['sometimes', 'boolean'],
                'language' => ['sometimes', Rule::in(['en', 'hi'])],
                'currency' => ['sometimes', Rule::in(['INR'])],
            ]);
            $preferences = $request->user()->preferences()->firstOrCreate();
            $preferences->update($data);

            return response()->json(['success' => true, 'message' => 'Preferences updated successfully.', 'data' => $preferences->fresh()]);
        });
    }

    public function supportedArtisans(Request $request)
    {
        $vendors = Vendor::query()->whereHas('subOrders.order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->select('id', 'store_name', 'store_slug', 'logo', 'description')->distinct()->paginate(12);
        $vendors->getCollection()->transform(fn ($vendor) => [...$vendor->toArray(), 'logo_url' => $this->publicUrl($vendor->logo)]);

        return response()->json(['success' => true, 'message' => 'Supported artisans fetched successfully.', 'data' => $vendors]);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['success' => true, 'message' => 'Signed out from all devices successfully.']);
    }

    public function destroy(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate(['password' => ['required'], 'confirmation' => ['required', Rule::in(['DELETE'])]]);
            if (! Hash::check($data['password'], $request->user()->password)) {
                throw ValidationException::withMessages(['password' => ['The password is incorrect.']]);
            }
            DB::transaction(function () use ($request) {
                $request->user()->tokens()->delete();
                $request->user()->update(['status' => 'inactive']);
                $request->user()->delete();
            });

            return response()->json(['success' => true, 'message' => 'Account deleted successfully.']);
        });
    }

    private function profileData($user): array
    {
        return [
            'id' => $user->id, 'first_name' => $user->first_name, 'last_name' => $user->last_name,
            'full_name' => $user->full_name, 'display_name' => $user->display_name, 'email' => $user->email,
            'phone' => $user->phone, 'gender' => $user->gender, 'date_of_birth' => $user->date_of_birth,
            'profile_image' => $user->profile_image, 'profile_image_url' => $this->publicUrl($user->profile_image),
            'email_verified_at' => $user->email_verified_at,
        ];
    }

    private function publicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path);
    }
}
