@extends('layouts.mazer')

@section('title', 'Edit Service')
@section('page-heading', 'Edit Service')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Service</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.update', $service) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.services.form-fields')
                    <button type="submit" class="btn btn-primary">Update Service</button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection