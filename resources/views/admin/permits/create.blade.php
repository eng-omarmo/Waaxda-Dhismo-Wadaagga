@extends('layouts.mazer')

@section('title', 'Request Construction Permit')
@section('page-heading', 'Request New Construction Permit')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permit Application</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permits.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.permits.form-fields')
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
