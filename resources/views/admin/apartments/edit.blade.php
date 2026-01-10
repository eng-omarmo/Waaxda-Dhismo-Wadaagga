@extends('layouts.mazer')

@section('title', 'Edit Apartment')
@section('page-heading', 'Edit Apartment: ' . $apartment->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apartment Information</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.apartments.update', $apartment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.apartments.form-fields', ['apartment' => $apartment])
                    <button type="submit" class="btn btn-primary">Update Apartment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
