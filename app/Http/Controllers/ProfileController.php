<?php

namespace App\Http\Controllers;

use App\Models\ManualOperationLog;
use App\Models\OnlinePayment;
use App\Models\PendingRegistration;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\UserChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $role = $user->role;
        $payments = OnlinePayment::whereHas('registration', function ($q) use ($user) {
                $q->where('email', $user->email);
            })
            ->with(['registration', 'registration.service'])
            ->latest()
            ->get();

        $services = Service::orderBy('name')->get();
        $requests = ServiceRequest::where('user_email', $user->email)
            ->with(['service', 'payments'])
            ->latest()
            ->get();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'view_user_portal',
            'target_type' => 'User',
            'target_id' => (string) $user->id,
            'details' => ['role' => $role, 'services_count' => $services->count(), 'requests_count' => $requests->count()],
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'view_user_payments',
            'target_type' => 'User',
            'target_id' => (string) $user->id,
            'details' => ['payments_count' => $payments->count()],
        ]);

        return view('profile.show', compact('user', 'role', 'services', 'requests', 'payments'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', 'min:12', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z\\d]).{12,}$/'],
        ]);

        $original = $user->getOriginal();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_phone = $request->contact_phone;
        $user->contact_address = $request->contact_address;
        if ($request->filled('password')) {
            $user->password = $request->password;
        }
        $user->save();

        $changes = [];
        foreach ($user->getChanges() as $key => $value) {
            if ($key === 'updated_at') {
                continue;
            }
            $changes[$key] = ['from' => $original[$key] ?? null, 'to' => $value];
        }
        if (!empty($changes)) {
            UserChange::create([
                'user_id' => $user->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('profile.show')->with('status', 'updated');
    }
}
