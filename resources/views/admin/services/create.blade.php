@extends('layouts.mazer')

@section('title', 'Create Service')
@section('page-heading', 'Create New Service')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Service Registration</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.store') }}" method="POST">
                    @csrf
                    @include('admin.services.form-fields')
                    <button type="submit" class="btn btn-primary">Create Service</button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection