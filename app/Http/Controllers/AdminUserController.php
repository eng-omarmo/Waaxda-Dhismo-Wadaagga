<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
            });
        }
        if ($role = $request->string('role')->toString()) {
            $query->where('role', $role);
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('active', $status === 'active');
        }
        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:12', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z\\d]).{12,}$/'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'active' => ['required', Rule::in(['0', '1'])],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_phone' => $request->contact_phone,
            'contact_address' => $request->contact_address,
            'password' => $request->password,
            'role' => $request->role,
            'active' => (bool) $request->active,
        ]);

        return redirect()->route('admin.users.show', $user)->with('status', 'created');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', 'min:12', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z\\d]).{12,}$/'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'active' => ['required', Rule::in(['0', '1'])],
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
        $user->role = $request->role;
        $user->active = (bool) $request->active;
        $user->save();

        $changes = [];
        foreach ($user->getChanges() as $key => $value) {
            if ($key === 'updated_at') {
                continue;
            }
            $changes[$key] = ['from' => $original[$key] ?? null, 'to' => $value];
        }
        if (! empty($changes)) {
            UserChange::create([
                'user_id' => $user->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('admin.users.show', $user)->with('status', 'updated');
    }

    public function destroy(User $user)
    {
        $user->active = false;
        $user->save();
        UserChange::create([
            'user_id' => $user->id,
            'changed_by' => Auth::id(),
            'changes' => ['active' => ['from' => true, 'to' => false]],
        ]);

        return redirect()->route('admin.users.index')->with('status', 'deactivated');
    }
}
