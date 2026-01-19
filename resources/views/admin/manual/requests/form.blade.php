@extends('layouts.mazer')
@section('title', 'Data Collection Form')
@section('page-heading', $schema['title'] ?? 'Data Collection Form')
@section('content')
<div class="card">
  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (! empty($schema['instructions']))
      <div class="alert alert-info">{{ $schema['instructions'] }}</div>
    @endif
    <form method="post" action="{{ route('admin.manual-requests.form.submit', $request) }}">
      @csrf
      @foreach(($schema['fields'] ?? []) as $field)
        @php
          $name = (string) ($field['name'] ?? '');
          $label = (string) ($field['label'] ?? ucfirst($name));
          $type = (string) ($field['type'] ?? 'text');
          $required = ! empty($field['required']);
          $val = old('form_values.'.$name, $values[$name] ?? '');
        @endphp
        @if($name !== '')
        <div class="mb-3">
          <label class="form-label">
            {{ $label }}@if($required) <span class="text-danger">*</span>@endif
          </label>
          @if($type === 'select')
            @php $opts = (array) ($field['options'] ?? []); @endphp
            <select name="form_values[{{ $name }}]" class="form-select" @if($required) required @endif>
              <option value="">-- select --</option>
              @foreach($opts as $opt)
                <option value="{{ $opt }}" @selected($val===$opt)>{{ $opt }}</option>
              @endforeach
            </select>
          @elseif($type === 'textarea')
            <textarea name="form_values[{{ $name }}]" rows="3" class="form-control" @if($required) required @endif>{{ $val }}</textarea>
          @else
            <input
              type="{{ in_array($type, ['text','email','number','date']) ? $type : 'text' }}"
              name="form_values[{{ $name }}]"
              value="{{ $val }}"
              class="form-control"
              @if($required) required @endif
            >
          @endif
          @error('form_values.'.$name)
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
          @if(! empty($field['instructions']))
            <div class="form-text">{{ $field['instructions'] }}</div>
          @endif
        </div>
        @endif
      @endforeach
      <div class="d-flex justify-content-between">
        <a href="{{ route('admin.manual-requests.show', $request) }}" class="btn btn-outline-secondary">Back to Request</a>
        <button class="btn btn-primary">Save Form</button>
      </div>
    </form>
  </div>
  <div class="card-footer">
    <div class="text-muted small">
      Request #{{ $request->id }} • {{ $request->service->name }} • Created {{ $request->created_at->format('Y-m-d H:i') }}
    </div>
  </div>
  @if(($schema['fields'] ?? []) === [])
    <div class="card-body">
      <div class="alert alert-warning mb-0">No fields defined for this service. You may proceed with notes only.</div>
    </div>
  @endif
</div>
@endsection
