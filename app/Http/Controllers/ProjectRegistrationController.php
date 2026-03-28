<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectRegistrationController extends Controller
{
    public function show()
    {
        return view('services.project-registration-enhanced');
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

        $service = Service::where('slug', 'project-registration')->first();
        if ($service) {
            ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'user_full_name' => $project->registrant_name,
                'user_email' => $project->registrant_email,
                'user_phone' => $project->registrant_phone,
                'user_national_id' => null,
                'request_details' => [
                    'project_name' => $project->project_name,
                    'location_text' => $project->location_text,
                    'project_id' => $project->id,
                ],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('services.project-registration.thankyou', ['id' => $project->id]);
    }

    public function thankyou(string $id)
    {
        return view('services.project-registration-thankyou', ['id' => $id]);
    }
}
