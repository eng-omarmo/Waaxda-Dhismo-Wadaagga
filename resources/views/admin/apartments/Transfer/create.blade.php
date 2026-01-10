@extends('layouts.mazer')

@section('title', 'Create Apartment Transfer')
@section('page-heading', 'Create New Apartment Transfer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apartment Transfer Information</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.apartment-transfers.store') }}" method="POST">
                    @csrf
                    @include('admin.apartments.Transfer.form')
                    <button type="submit" class="btn btn-primary">Create Apartment Transfer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
