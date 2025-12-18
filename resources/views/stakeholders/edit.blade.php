@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Stakeholder</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customer</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center">Edit Stakeholder</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.update', $stakeHolder->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="name">First Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $stakeHolder->name) }}" required>
                            </div>
                            {{-- <div class="form-group col-md-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $stakeHolder->last_name) }}" required>
                            </div> --}}
                            <div class="form-group col-md-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $stakeHolder->email) }}" >
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_type">User Type</label>
                                <select class="form-control" id="user_type" name="user_type" required>
                                    <option value="supplier" {{ old('user_type', $stakeHolder->user_type) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                    <option value="customer" {{ old('user_type', $stakeHolder->user_type) == 'customer' ? 'selected' : '' }}>Customer</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $stakeHolder->city) }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            
                            <div class="form-group col-md-12">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $stakeHolder->address) }}" required>
                            </div>
                            
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="state">State</label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $stakeHolder->state) }}" required>
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="state_code">State Code</label>
                                <input type="text" class="form-control" id="state_code" name="state_code" value="{{ old('state_code', $stakeHolder->state_code) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="GSTIN">GSTIN</label>
                                <input type="text" class="form-control" id="GSTIN" name="GSTIN" value="{{ old('GSTIN', $stakeHolder->GSTIN) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="GSTIN">Business Name (Optional)</label>
                                <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $stakeHolder->business_name) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $stakeHolder->bank_name) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="bank_account_no">Bank Account No</label>
                                <input type="text" class="form-control" id="bank_account_no" name="bank_account_no" value="{{ old('bank_account_no', $stakeHolder->bank_account_no) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ifsc_code">IFSC Code</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code', $stakeHolder->ifsc_code) }}" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('customer.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
