<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApartmentTransfer;

class ApartmentTransferController extends Controller
{

    public function index()
    {
        $transfers = ApartmentTransfer::all();

        return view('admin.apartments.Transfer.index', [
            'transfers' => $transfers,
        ]);
    }
}
