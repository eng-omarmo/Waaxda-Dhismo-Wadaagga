@extends('layouts.mazer')

@section('title', 'Service Details')
@section('page-heading', 'Service Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $service->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Service Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $service->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Price:</strong></td>
                                <td>${{ number_format($service->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $service->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated:</strong></td>
                                <td>{{ $service->updated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Description</h5>
                        <p>{{ $service->description }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">Edit</a>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Back to List</a>
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection