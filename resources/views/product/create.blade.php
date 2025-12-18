@extends('layouts.app')

@section('content')
    <style>
        span.select2-selection.select2-selection--single {
            padding-bottom: 29px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 68% !important;
        }
    </style>
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
                            <li class="breadcrumb-item active">Create</li>
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
                        <h4 class="card-title text-center">Create Product</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="partName">Part Name</label>
                                <input type="text" class="form-control" id="partName" name="name"
                                    placeholder="Enter part name">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="valveType">Valve Type</label>
                                    <input type="text" class="form-control" id="valveType" name="valve_type"
                                        placeholder="Enter valve type">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="productCode">Product Code</label>
                                    <input type="number" class="form-control" id="productCode" name="product_code"
                                        placeholder="Enter product code">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="actuation">Actuation</label>
                                    <input type="text" class="form-control" id="actuation" name="actuation"
                                        placeholder="Enter actuation">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="pressureRating">Pressure Rating</label>
                                    <input type="text" class="form-control" id="pressureRating" name="pressure_rating"
                                        placeholder="Enter pressure rating">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="valveSize">Valve Size</label>
                                    <input type="text" class="form-control" id="valveSize" name="valve_size"
                                        placeholder="Enter valve size">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="size">Size</label>
                                    <select class="form-control" id="size" name="valve_size_rate">
                                        <option value="INCH">INCH</option>
                                        <option value="MM">MM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="media">Media</label>
                                    <input type="text" class="form-control" id="media" name="media"
                                        placeholder="Enter media">
                                </div>
                                {{-- <div class="form-group col-md-4">
                                    <label for="flow">Flow</label>
                                    <input type="text" class="form-control" id="flow" name="flow" placeholder="Enter flow">
                                </div> --}}
                                <div class="form-group col-md-4">
                                    <label for="skuCode">SKU Code</label>
                                    <input type="text" class="form-control" id="skuCode" name="sku_code"
                                        placeholder="Enter SKU Code">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="mrp">MRP</label>
                                    <input type="text" class="form-control" id="mrp" name="mrp"
                                        placeholder="Enter MRP">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="mediaTemperature">Media Temperature</label>
                                    <input type="text" class="form-control" id="mediaTemperature"
                                        name="media_temperature" placeholder="Enter media temperature">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tempRate">Temp Rate</label>
                                    <select class="form-control" id="tempRate" name="media_temperature_rate">
                                        <option value="FAHRENHEIT">FAHRENHEIT</option>
                                        <option value="CELSIUS">CELSIUS</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="mrp">HSN Code</label>
                                    <input type="text" class="form-control" id="hsncode" name="hsn_code"
                                        placeholder="Enter HSN Code">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="mediaTemperature">Body Material</label>
                                    <input type="text" class="form-control" id="body_material" name="body_material"
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
                                                    <th>Parts Name <span class="text-danger">*</span></th>
                                                    <th>Quantity <span class="text-danger">*</span></th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;"
                                                            name="parts_name[]" required>
                                                            <option value="">Select Spare Part</option>
                                                            @foreach ($parts as $part)
                                                                <option value="{{ $part->id }}">{{ $part->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="quantity[]"
                                                            value="0" placeholder="Enter quantity" required>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                                class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">SUBMIT</button>
                            <button type="reset" class="btn btn-secondary">CANCEL</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            // Function to initialize Select2
            function initializeSelect2() {
                $('.select2').select2({
                    placeholder: 'Select Spare Part',
                    allowClear: true
                });
            }

            // Initialize Select2 on existing select elements
            initializeSelect2();

            // Add row function
            $(".add-row").click(function() {
                var newRow = `
            <tr>
                <td>
                    <select class="form-control select2" style="width:100%;" name="parts_name[]" required>
                        <option value="">Select Spare Part</option>
                        @foreach ($parts as $part)
                            <option value="{{ $part->id }}">{{ $part->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" 
                           class="form-control" 
                           name="quantity[]" 
                           value="0"
                           placeholder="Enter quantity"
                           placeholder="Enter with 4 decimal places"
                           required></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
                $("#inventoryTable tbody").append(newRow);
                initializeSelect2();
            });

            // Remove row function
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            let isValid = true;
            
            // Check if at least one row exists
            if ($("#inventoryTable tbody tr").length === 0) {
                alert('Please add at least one product item');
                isValid = false;
            }

            // Validate each row
            $("#inventoryTable tbody tr").each(function() {
                const partSelect = $(this).find('select[name="parts_name[]"]');
                const quantityInput = $(this).find('input[name="quantity[]"]');
                
                // Validate part selection
                if (!partSelect.val()) {
                    partSelect.addClass('is-invalid');
                    isValid = false;
                }

                // Validate quantity
                const quantity = quantityInput.val();
                if (!quantity || quantity <= 0) {
                    quantityInput.addClass('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });

        // Clear validation on selection change
        $(document).on('change', 'select[name="parts_name[]"]', function() {
            $(this).removeClass('is-invalid');
        });

        // Clear validation on quantity input
        $(document).on('input', 'input[name="quantity[]"]', function() {
            $(this).removeClass('is-invalid');
        });

        // Remove row function
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        // Prevent duplicate part selection
        $(document).on('change', 'select[name="parts_name[]"]', function() {
            const selectedValue = $(this).val();
            if (selectedValue) {
                const currentRow = $(this).closest('tr');
                let isDuplicate = false;

                // Check other rows for the same part
                $('select[name="parts_name[]"]').not($(this)).each(function() {
                    if ($(this).val() === selectedValue) {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    alert('This spare part has already been selected');
                    $(this).val('').trigger('change');
                }
            }
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
