@extends('admin.layouts.admin')

@section('title', 'Edit Default Note')
@section('page-title', 'Edit Default Note')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Update Note Content</h3>
            </div>
            <form action="{{ route('admin.defaults.update', $default->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="detail_text">Note Text</label>
                        <textarea name="detail_text" class="form-control" rows="4" required>{{ $default->detail_text }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a href="{{ route('admin.defaults.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
