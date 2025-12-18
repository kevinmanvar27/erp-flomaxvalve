@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Settings</li>
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

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Settings</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                        value="{{ old('company_name', $setting->company_name ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="company_gstin">Company GSTIN Number</label>
                                    <input type="text" class="form-control" id="company_gstin" name="company_gstin"
                                        value="{{ old('company_gstin', $setting->company_gstin ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="mobile_number">Mobile Number</label>
                                    <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                        value="{{ old('mobile_number', $setting->mobile_number ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="mobile_number">Company Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $setting->email ?? '') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address', $setting->address ?? '') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ old('city', $setting->city ?? '') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="state">State</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                        value="{{ old('state', $setting->state ?? '') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="pin_code">Pin Code</label>
                                    <input type="text" class="form-control" id="pin_code" name="pin_code"
                                        value="{{ old('pin_code', $setting->pin_code ?? '') }}">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                    @if ($setting && $setting->logo)
                                        <img src="{{ asset($setting->logo) }}" alt="Logo" class="mt-2"
                                            style="max-height: 100px; width:70%;">
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="authorized_signatory">Authorized Signatory (Image)</label>
                                    <input type="file" class="form-control" id="authorized_signatory"
                                        name="authorized_signatory">
                                    @if ($setting && $setting->authorized_signatory)
                                        <img src="{{ asset($setting->authorized_signatory) }}" alt="Authorized Signatory"
                                            class="mt-2" style="max-height: 100px; width:70%;">
                                    @endif
                                </div>
                            </div>
                            <div class="card-header" style="border: 1px solid rgba(0, 0, 0, .125);">
                                <h4 class="card-title text-center" style="margin-left:-10px;font-weight:800;">Purchase Order</h4>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="purchase_order_gstin">Purchase Order GSTIN Number</label>
                                    <input type="text" class="form-control" id="purchase_order_gstin"
                                        name="purchase_order_gstin"
                                        value="{{ old('purchase_order_gstin', $setting->purchase_order_gstin ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="purchase_order_mobile_number">Purchase Order Mobile Number</label>
                                    <input type="text" class="form-control" id="purchase_order_mobile_number"
                                        name="purchase_order_mobile_number"
                                        value="{{ old('purchase_order_mobile_number', $setting->purchase_order_mobile_number ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="purchase_order_email">Purchase Order Email</label>
                                    <input type="email" class="form-control" id="purchase_order_email"
                                        name="purchase_order_email"
                                        value="{{ old('purchase_order_email', $setting->purchase_order_email ?? '') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="purchase_order_address">Purchase Order Address</label>
                                    <input type="text" class="form-control" id="purchase_order_address"
                                        name="purchase_order_address"
                                        value="{{ old('purchase_order_address', $setting->purchase_order_address ?? '') }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="purchase_order_logo">Purchase Order Logo</label>
                                    <input type="file" class="form-control" id="purchase_order_logo" name="purchase_order_logo">
                                    @if ($setting && $setting->purchase_order_logo)
                                        <img src="{{ asset($setting->purchase_order_logo) }}" alt="Purchase Order Logo" class="mt-2"
                                            style="max-height: 100px;width:70%;">
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="prepared_by">Prepared By</label>
                                    <input type="file" class="form-control" id="prepared_by" name="prepared_by">
                                    @if ($setting && $setting->prepared_by)
                                        <img src="{{ asset($setting->prepared_by) }}" alt="Prepared By" class="mt-2"
                                            style="max-height: 100px; width:70%;">
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="approved_by">Approved By</label>
                                    <input type="file" class="form-control" id="approved_by" name="approved_by">
                                    @if ($setting && $setting->approved_by)
                                        <img src="{{ asset($setting->approved_by) }}" alt="Approved By" class="mt-2"
                                            style="max-height: 100px;width:70%;">
                                    @endif
                                </div>
                            </div>


                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
