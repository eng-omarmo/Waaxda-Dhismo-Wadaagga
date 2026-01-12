<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Certificate;
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
        $developers = Organization::orderBy('name')->get();
        $statuses = ['Draft', 'Submitted', 'Approved'];

        return view('admin.pages.projects', compact('projects', 'developers', 'statuses', 'sort', 'direction'));
    }


    public function create()
    {
        return view('admin.pages.new-project');
    }

    public function store(Request $request)
    {

        $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'location_text' => ['required', 'string', 'max:255'],
            'developer_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'status' => ['required', Rule::in(['Draft', 'Submitted', 'Approved'])],
        ]);

        $user = Auth::user();

        $project = Project::create([
            'project_name' => $request->project_name,
            'location_text' => $request->location_text,
            'developer_id' => $request->developer_id ?: null,
            'status' => $request->status,
            'registrant_name' => trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: 'Administrator',
            'registrant_phone' => $user->contact_phone ?? '',
            'registrant_email' => $user->email ?? '',
        ]);

        $service = Service::where('slug', 'project-registration')->first();
        if ($service) {
            Certificate::issueForProject($project, $service, Auth::id());
        }

        return redirect()->route('admin.projects')->with('status', 'Project registered successfully');
    }

    public function edit(Project $project){
        $developers = Organization::orderBy('name')->get();
        return view('admin.pages.edit-project', compact('project', 'developers'));
    }

    public function update(Request $request, Project $project){
        $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'location_text' => ['required', 'string', 'max:255'],
            'developer_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'status' => ['required', Rule::in(['Draft', 'Submitted', 'Approved'])],
        ]);

        $project->update([
            'project_name' => $request->project_name,
            'location_text' => $request->location_text,
            'developer_id' => $request->developer_id ?: null,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.projects')->with('status', 'Project updated successfully');
    }

    public function assignDeveloper(Request $request, Project $project)
    {
        $request->validate([
            'developer_id' => ['nullable', 'integer', 'exists:organizations,id'],
        ]);

        $project->developer_id = $request->developer_id ?: null;
        $project->save();

        return redirect()->route('admin.projects')->with('status', 'Developer assignment updated');
    }
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects')->with('status', 'Project deleted successfully');
    }
}
