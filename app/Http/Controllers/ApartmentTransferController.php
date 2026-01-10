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
}
