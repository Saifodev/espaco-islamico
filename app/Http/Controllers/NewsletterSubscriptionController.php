<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use App\Mail\WelcomeNewsletterMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsletterSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null
            ]
        );

        // Enviar email de boas-vindas
        try {
            Mail::to($request->email)->send(new WelcomeNewsletterMail($request->email));
            // Log::info('Welcome email sent to newsletter subscriber', ['email' => $request->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to newsletter subscriber', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
        }

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

            // Log::info('Newsletter subscriber unsubscribed', ['email' => $email]);

            return redirect()->route('home');
        }

        return redirect()->route('home');
    }
}