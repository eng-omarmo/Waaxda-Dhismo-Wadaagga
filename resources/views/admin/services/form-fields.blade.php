<div class="form-group">
    <label for="name">Service Name</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $service->name ?? '') }}" required maxlength="100">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required maxlength="500">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Maximum 500 characters</small>
</div>

<div class="form-group">
    <label for="price">Price</label>
    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $service->price ?? '') }}" required min="0">
    @error('price')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>