@extends('layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Product</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
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
                        <h4 class="card-title text-center">Edit Product</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT') <!-- Needed for updating -->
                            <div class="form-group">
                                <label for="partName">Part Name</label>
                                <input type="text" class="form-control" id="partName" name="name"
                                    value="{{ $product->name }}" placeholder="Enter part name">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="valveType">Valve Type</label>
                                    <input type="text" class="form-control" id="valveType" name="valve_type"
                                        value="{{ $product->valve_type }}" placeholder="Enter valve type">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="productCode">Product Code</label>
                                    <input type="number" class="form-control" id="productCode" name="product_code"
                                        value="{{ $product->product_code }}" placeholder="Enter product code">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="actuation">Actuation</label>
                                    <input type="text" class="form-control" id="actuation" name="actuation"
                                        value="{{ $product->actuation }}" placeholder="Enter actuation">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="pressureRating">Pressure Rating</label>
                                    <input type="text" class="form-control" id="pressureRating" name="pressure_rating"
                                        value="{{ $product->pressure_rating }}" placeholder="Enter pressure rating">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="valveSize">Valve Size</label>
                                    <input type="text" class="form-control" id="valveSize" name="valve_size"
                                        value="{{ $product->valve_size }}" placeholder="Enter valve size">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="size">Size</label>
                                    <select class="form-control" id="size" name="valve_size_rate">
                                        <option value="INCH" {{ $product->valve_size_rate == 'INCH' ? 'selected' : '' }}>
                                            INCH</option>
                                        <option value="MM" {{ $product->valve_size_rate == 'MM' ? 'selected' : '' }}>MM
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="media">Media</label>
                                    <input type="text" class="form-control" id="media" name="media"
                                        value="{{ old('media', $product->media) }}" placeholder="Enter media">
                                </div>
                                {{-- <div class="form-group col-md-4">
                                    <label for="flow">Flow</label>
                                    <input type="text" class="form-control" id="flow" name="flow" value="{{ old('flow', $product->flow) }}" placeholder="Enter flow">
                                </div> --}}
                                <div class="form-group col-md-4">
                                    <label for="skuCode">SKU Code</label>
                                    <input type="text" class="form-control" id="skuCode" name="sku_code"
                                        value="{{ old('sku_code', $product->sku_code) }}" placeholder="Enter SKU Code">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="mrp">MRP</label>
                                    <input type="text" class="form-control" id="mrp" name="mrp"
                                        value="{{ old('mrp', $product->mrp) }}" placeholder="Enter MRP">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="mediaTemperature">Media Temperature</label>
                                    <input type="text" class="form-control" id="mediaTemperature"
                                        name="media_temperature"
                                        value="{{ old('media_temperature', $product->media_temperature) }}"
                                        placeholder="Enter media temperature">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tempRate">Temp Rate</label>
                                    <select class="form-control" id="tempRate" name="media_temperature_rate">
                                        <option value="FAHRENHEIT"
                                            {{ old('media_temperature_rate', $product->media_temperature_rate) == 'FAHRENHEIT' ? 'selected' : '' }}>
                                            FAHRENHEIT</option>
                                        <option value="CELSIUS"
                                            {{ old('media_temperature_rate', $product->media_temperature_rate) == 'CELSIUS' ? 'selected' : '' }}>
                                            CELSIUS</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="hsncode">HSN Code</label>
                                    <input type="text" class="form-control" id="hsncode" name="hsn_code"
                                        value="{{ old('hsn_code', $product->hsn_code) }}" placeholder="Enter HSN Code">
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="body_material">Body Material</label>
                                    <input type="text" class="form-control" id="body_material" name="body_material"
                                        value="{{ old('body_material', $product->body_material) }}"
                                        placeholder="Enter Body Material">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h4 class="mb-3">Product Items <button type="button"
                                            class="btn btn-primary add-row float-right"><i class="fas fa-plus"></i> Add
                                            New</button></h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="inventoryTable">
                                            <thead>
                                                <tr>
                                                    <th>Parts Name</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($product->items as $item)
                                                    <tr>
                                                        <input type="hidden" value="{{ $item->id }}"
                                                            name="itemIds[]" />
                                                        <td>
                                                            <select class="custom-select" name="parts_name[]">
                                                                <option value="">Select Spare Part</option>
                                                                @foreach ($spareParts as $part)
                                                                    <option value="{{ $part->id }}"
                                                                        {{ $part->id == $item->spare_part_id ? 'selected' : '' }}>
                                                                        {{ $part->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="quantity[]" placeholder="Enter quantity"
                                                                value="{{ $item->quantity }}" required>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-row"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <label for="primaryMaterial">Primary Material of Construction</label>
                                <input type="text" class="form-control" id="primaryMaterial" name="primary_material_of_construction" value="{{ old('primary_material_of_construction', $product->primary_material_of_construction) }}" placeholder="Enter primary material of construction">
                            </div> --}}
                            <!-- Additional fields similar to above, all populated with existing $product data -->
                            <button type="submit" class="btn btn-primary">UPDATE</button>
                            <button type="reset" class="btn btn-secondary">CANCEL</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function() {
            // Add row function
            $(".add-row").click(function() {
                var newRow = `
                <tr>
                    <td>
                        <select class="custom-select" name="parts_name[]">
                            <option value="">Select Spare Part</option>
                            @foreach ($spareParts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" 
                           class="form-control" 
                           name="quantity[]" 
                           value="0"
                           placeholder="Enter quantity"
                           required></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
                $("#inventoryTable tbody").append(newRow);
            });

            // Remove row function
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Function to validate and format quantity input
            function handleQuantityInput(input) {
                var value = $(input).val();
                
                // Remove any non-numeric characters except decimal point
                value = value.replace(/[^\d.]/g, '');
                
                // If it's a whole number, allow it
                if (!value.includes('.')) {
                    $(input).val(value);
                    return;
                }
                
                // If it has a decimal, enforce 4 digits
                var parts = value.split('.');
                if (parts[1]) {
                    parts[1] = parts[1].slice(0, 4);
                    value = parts[0] + '.' + parts[1];
                }
                
                $(input).val(value);
            }

            // Apply to existing inputs
            $(document).on('input', 'input[name="quantity[]"]', function() {
                handleQuantityInput(this);
            });


        });
    </script>
@endsection
