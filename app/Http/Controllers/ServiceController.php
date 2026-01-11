<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'The service name is required.',
            'name.max' => 'The service name must not exceed 100 characters.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description must not exceed 500 characters.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be a positive value.',
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        $map = [
            'project-registration' => 'services.project-registration',
            'developer-registration' => 'services.developer-registration',
            'business-license' => 'services.business-license',
            'ownership-certificate' => 'services.ownership-certificate',
            'ownership-transfer' => 'services.ownership-transfer',
            'property-transfer-services' => 'services.ownership-transfer',
            'construction-permit-application' => 'services.project-registration',
        ];

        if (isset($map[$service->slug])) {
            return redirect()->route($map[$service->slug]);
        }

        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'The service name is required.',
            'name.max' => 'The service name must not exceed 100 characters.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description must not exceed 500 characters.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be a positive value.',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }



}
