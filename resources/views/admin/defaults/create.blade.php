@extends('admin.layouts.admin')

@section('title', 'Add Default Note')
@section('page-title', 'Add Default Note')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Note Content</h3>
            </div>
            <form action="{{ route('admin.defaults.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="detail_text">Note Text</label>
                        <textarea name="detail_text" class="form-control" rows="4" required placeholder="Enter default prescription note..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.defaults.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
