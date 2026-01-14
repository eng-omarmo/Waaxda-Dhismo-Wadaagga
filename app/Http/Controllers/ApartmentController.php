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
            'owner_full_name' => 'nullable|string|max:255',
            'owner_national_id' => 'nullable|string|max:64',
            'owner_contact_phone' => 'nullable|string|max:50',
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

        if ($request->filled('owner_national_id')) {
            $owner = \App\Models\OwnerProfile::firstOrCreate(
                ['national_id' => $request->owner_national_id],
                [
                    'full_name' => $request->owner_full_name ?: $request->contact_name,
                    'contact_phone' => $request->owner_contact_phone ?: $request->contact_phone,
                ]
            );
            $apartment->owner_profile_id = $owner->id;
            $apartment->save();
            \App\Models\OwnershipHistory::create([
                'apartment_id' => $apartment->id,
                'owner_profile_id' => $owner->id,
                'started_at' => now()->toDateString(),
                'recorded_by_admin_id' => auth()->id(),
            ]);
        }

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
            'owner_full_name' => 'nullable|string|max:255',
            'owner_national_id' => 'nullable|string|max:64',
            'owner_contact_phone' => 'nullable|string|max:50',
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

        if ($request->filled('owner_national_id')) {
            $owner = \App\Models\OwnerProfile::firstOrCreate(
                ['national_id' => $request->owner_national_id],
                [
                    'full_name' => $request->owner_full_name ?: $request->contact_name,
                    'contact_phone' => $request->owner_contact_phone ?: $request->contact_phone,
                ]
            );
            if ($apartment->owner_profile_id !== $owner->id) {
                \App\Models\OwnershipHistory::where('apartment_id', $apartment->id)
                    ->whereNull('ended_at')
                    ->latest()
                    ->first()?->update(['ended_at' => now()->toDateString()]);
                $apartment->owner_profile_id = $owner->id;
                $apartment->save();
                \App\Models\OwnershipHistory::create([
                    'apartment_id' => $apartment->id,
                    'owner_profile_id' => $owner->id,
                    'started_at' => now()->toDateString(),
                    'recorded_by_admin_id' => auth()->id(),
                ]);
            }
        }

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
