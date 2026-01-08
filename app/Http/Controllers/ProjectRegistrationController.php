<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectRegistrationController extends Controller
{
    public function show()
    {
        return view('services.project-registration');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registrant_name' => ['required', 'string', 'max:255'],
            'registrant_phone' => ['required', 'string', 'max:50'],
            'registrant_email' => ['required', 'email', 'max:255'],
            'project_name' => ['required', 'string', 'max:255'],
            'location_text' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Draft,Submitted'],
        ]);

        $project = Project::create([
            'project_name' => $validated['project_name'],
            'location_text' => $validated['location_text'],
            'developer_id' => null,
            'status' => $validated['status'],
            'registrant_name' => $validated['registrant_name'],
            'registrant_phone' => $validated['registrant_phone'],
            'registrant_email' => $validated['registrant_email'],
        ]);

        return redirect()->route('services.project-registration.thankyou', ['id' => $project->id]);
    }

    public function thankyou(string $id)
    {
        return view('services.project-registration-thankyou', ['id' => $id]);
    }
}
