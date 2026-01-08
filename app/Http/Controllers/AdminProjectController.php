<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()->with('developer');

        if ($q = $request->string('q')->toString()) {
            $query->where('project_name', 'like', "%$q%");
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $sort = $request->string('sort')->toString() ?: 'date';
        $direction = $request->string('direction')->toString() ?: 'desc';
        if ($sort === 'alpha') {
            $query->orderBy('project_name', $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', $direction === 'asc' ? 'asc' : 'desc');
        }

        $projects = $query->paginate(10)->withQueryString();
        $developers = User::orderBy('first_name')->orderBy('last_name')->get();
        $statuses = ['Draft', 'Submitted', 'Approved'];

        return view('admin.pages.projects', compact('projects', 'developers', 'statuses', 'sort', 'direction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'location_text' => ['required', 'string', 'max:255'],
            'developer_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(['Draft', 'Submitted', 'Approved'])],
        ]);

        $user = $request->user();

        Project::create([
            'project_name' => $request->project_name,
            'location_text' => $request->location_text,
            'developer_id' => $request->developer_id ?: null,
            'status' => $request->status,
            'registrant_name' => trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: 'Administrator',
            'registrant_phone' => $user->contact_phone ?? '',
            'registrant_email' => $user->email ?? '',
        ]);

        return redirect()->route('admin.projects')->with('status', 'Project registered successfully');
    }
}

