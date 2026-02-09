@extends('admin.layouts.admin')

@section('title', 'Edit Medicine')
@section('page-title', 'Edit Medicine')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Update Medicine Details</h3>
            </div>
            <form action="{{ route('admin.medicines.update', $medicine->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Medicine Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $medicine->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" class="form-control" required>
                            <option value="tablet" {{ $medicine->type == 'tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="syrup" {{ $medicine->type == 'syrup' ? 'selected' : '' }}>Syrup</option>
                            <option value="capsule" {{ $medicine->type == 'capsule' ? 'selected' : '' }}>Capsule</option>
                            <option value="injection" {{ $medicine->type == 'injection' ? 'selected' : '' }}>Injection</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dosage_options">Dosage Options (comma separated)</label>
                        <input type="text" name="dosage_options" class="form-control" id="dosage_options" value="{{ $medicine->dosage_options }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a href="{{ route('admin.medicines.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
