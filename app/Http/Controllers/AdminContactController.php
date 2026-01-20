<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();
        if ($q = $request->string('q')->toString()) {
            $query->where(function ($s) use ($q) {
                $s->where('full_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('service_type', 'like', "%$q%");
            });
        }
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $messages = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.pages.contacts', compact('messages'));
    }
}
