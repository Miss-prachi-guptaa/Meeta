<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
            NewsletterSubscriber::updateOrCreate(['email' => strtolower($data['email'])], ['is_active' => true, 'subscribed_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Welcome to the MEETA circle.']);
        });
    }
}
