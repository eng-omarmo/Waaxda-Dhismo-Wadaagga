@extends('layouts.mazer')

@section('title', 'Edit Construction Permit')
@section('page-heading', 'Edit Permit for ' . $permit->applicant_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permit Information</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permits.update', $permit) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.permits.form-fields', ['permit' => $permit])
                    <button type="submit" class="btn btn-primary">Update Permit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
