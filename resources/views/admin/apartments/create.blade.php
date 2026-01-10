@extends('layouts.mazer')

@section('title', 'Create Apartment')
@section('page-heading', 'Create New Apartment')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apartment Information</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.apartments.store') }}" method="POST">
                    @csrf
                    @include('admin.apartments.form-fields')
                    <button type="submit" class="btn btn-primary">Create Apartment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
