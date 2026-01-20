<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Service;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $services = Service::all();

        return view('landing', [
            'services' => $services,
        ]);
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
        ]);

        ContactMessage::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'service_type' => $request->service_type,
            'message' => $request->message,
        ]);

        return redirect()->route('landing.page.index')->with('status', 'Your message has been received');
    }
}
