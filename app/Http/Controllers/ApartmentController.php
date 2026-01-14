<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Apartment::withCount('units')->latest();

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $apartments = $query->paginate(10);

        return view('admin.apartments.index', compact('apartments'));
    }

    public function create()
    {
        return view('admin.apartments.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:50',
            'contact_email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'units.*.unit_number' => 'required|string|max:255',
            'units.*.unit_type' => 'required|string|max:255',
            'units.*.square_footage' => 'required|integer|min:0',
            'units.*.monthly_rent' => 'required|numeric|min:0',
            'units.*.status' => 'required|in:available,occupied,under-maintenance',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $apartment = Apartment::create($request->only([
            'name', 'address_city',
            'contact_name', 'contact_phone', 'contact_email', 'notes',
        ]));

        if ($request->has('units')) {
            foreach ($request->units as $unitData) {
                $apartment->units()->create($unitData);
            }
        }

        return redirect()->route('admin.apartments.index')->with('success', 'Apartment created successfully.');
    }

    public function show(Apartment $apartment)
    {
        $apartment->load('units');

        return view('admin.apartments.show', compact('apartment'));
    }

    public function edit(Apartment $apartment)
    {
        $apartment->load('units');

        return view('admin.apartments.edit', compact('apartment'));
    }

    public function update(Request $request, Apartment $apartment)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address_street' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:50',
            'contact_email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'units.*.unit_number' => 'required|string|max:255',
            'units.*.unit_type' => 'required|string|max:255',
            'units.*.square_footage' => 'required|integer|min:0',
            'units.*.monthly_rent' => 'required|numeric|min:0',
            'units.*.status' => 'required|in:available,occupied,under-maintenance',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $apartment->update($request->only([
            'name', 'address_street',
            'contact_name', 'contact_phone', 'contact_email', 'notes',
        ]));

        // Sync units
        $existingUnitIds = $apartment->units->pluck('id')->toArray();
        $submittedUnitIds = [];

        if ($request->has('units')) {
            foreach ($request->units as $unitData) {
                if (isset($unitData['id'])) {
                    $unit = $apartment->units()->find($unitData['id']);
                    if ($unit) {
                        $unit->update($unitData);
                        $submittedUnitIds[] = $unit->id;
                    }
                } else {
                    $newUnit = $apartment->units()->create($unitData);
                    $submittedUnitIds[] = $newUnit->id;
                }
            }
        }

        $unitsToDelete = array_diff($existingUnitIds, $submittedUnitIds);
        $apartment->units()->whereIn('id', $unitsToDelete)->delete();

        return redirect()->route('admin.apartments.index')->with('success', 'Apartment updated successfully.');
    }

    public function destroy(Apartment $apartment)
    {
        $apartment->delete();

        return redirect()->route('admin.apartments.index')->with('success', 'Apartment deleted successfully.');
    }
}
