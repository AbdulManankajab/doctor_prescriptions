@extends('admin.layouts.admin')

@section('title', 'Add Medicine')
@section('page-title', 'Add Medicine')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Medicine Details</h3>
            </div>
            <form action="{{ route('admin.medicines.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Medicine Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" class="form-control" required>
                            <option value="tablet">Tablet</option>
                            <option value="syrup">Syrup</option>
                            <option value="capsule">Capsule</option>
                            <option value="injection">Injection</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dosage_options">Dosage Options (comma separated)</label>
                        <input type="text" name="dosage_options" class="form-control" id="dosage_options" placeholder="e.g. 500mg, 650mg">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.medicines.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
