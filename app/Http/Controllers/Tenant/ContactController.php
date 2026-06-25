<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('shop.contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        Mail::to(config('mail.from.address'))
            ->send(new ContactMessage(
                senderName:  $validated['name'],
                senderEmail: $validated['email'],
                subject:     $validated['subject'],
                body:        $validated['message'],
            ));

        return redirect()->route('shop.contact')
            ->with('success', 'Your astropathic message has been transmitted successfully.');
    }
}
