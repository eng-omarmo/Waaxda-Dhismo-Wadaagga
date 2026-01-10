<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentTransfer;

class ApartmentTransferController extends Controller
{

    public function index()
    {
        $ApartmentTransfer = ApartmentTransfer::paginate(10);
        return view('admin.apartments.Transfer.index', [
            'transfers' => $ApartmentTransfer,
        ]);
    }

    public function create()
    {
        return view('admin.apartments.Transfer.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'tenant_id' => 'required|exists:tenants,id',
            'transfer_date' => 'required|date',
        ]);

        ApartmentTransfer::create($request->all());

        return redirect()->route('admin.apartment-transfers.index')
            ->with('success', 'Apartment transfer created successfully.');
    }
}
