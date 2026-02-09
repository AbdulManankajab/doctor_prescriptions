@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Add New Facility</h1>
        </div>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Facility Details</h3>
        </div>
        
        <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Facility Name *</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Type *</label>
                            <select name="type" class="form-control" id="type" required>
                                <option value="">Select Type</option>
                                <option value="Clinic" {{ old('type') == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                                <option value="Hospital" {{ old('type') == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                                <option value="Polyclinic" {{ old('type') == 'Polyclinic' ? 'selected' : '' }}>Polyclinic</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="license_number">License Number</label>
                            <input type="text" name="license_number" class="form-control" id="license_number" value="{{ old('license_number') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <div class="custom-file">
                                <input type="file" name="logo" class="custom-file-input" id="logo" accept="image/*">
                                <label class="custom-file-label" for="logo">Choose file</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea name="address" class="form-control" id="address" rows="2" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="province">Province *</label>
                            <input type="text" name="province" class="form-control" id="province" value="{{ old('province') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="district">District *</label>
                            <input type="text" name="district" class="form-control" id="district" value="{{ old('district') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select name="status" class="form-control" id="status" required>
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Facility</button>
                <a href="{{ route('admin.facilities.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endsection
