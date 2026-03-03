<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Log;

class NewsletterSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null
            ]
        );

        // Log::info('New newsletter subscription', ['email' => $request->email]);

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $email = $request->get('email');

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            $subscriber->update([
                'is_active' => false,
                'unsubscribed_at' => now()
            ]);

            // return view('public.newsletter-unsubscribed');
            return redirect()->route('home');
        }

        return redirect()->route('home');
    }
}
