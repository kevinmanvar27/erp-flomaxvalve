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

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Create Sales</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="card">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-header">
                    <h3>Create Sales</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="client_name" class="form-label">Client Name</label>
                                <select class="custom-select select2" id="client_name" name="client_name" required>
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="invoice" class="form-label">Invoice #</label>
                                <input type="text" class="form-control" id="invoice" name="invoice"
                                    value="{{ $invoiceNumber }}"  required>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="custom-select" id="status" name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="orderno" class="form-label">Order No</label>
                                <input type="text" class="form-control" id="orderno" name="orderno"
                                    value="" >
                            </div>
                            <div class="col-md-3">
                                <label for="lrno" class="form-label">L.R. No</label>
                                <input type="text" class="form-control" id="lrno" name="lrno"
                                    value="" >
                            </div>
                            <div class="col-md-3">
                                <label for="transport" class="form-label">Transport</label>
                                <input type="text" class="form-control" id="transport" name="transport" value="" >
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" class="form-control" id="address"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" class="form-control" id="note"></textarea>
                            </div>
                        </div>

                        <!-- Inventory Table -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary add-row mb-2"><i class="fas fa-plus"></i> Add
                                    Row</button>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                {{-- <th>Product Unit</th> --}}
                                                <th>Price</th>
                                                <th>Amount</th>
                                                <th>Remark</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="custom-select select2 product-select" style="width:100%;"
                                                        name="item[]">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}" data-quantity="{{ $product->available_quantity }}">{{ $product->name }} (Qty: {{ $product->available_quantity }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control quantity-input" name="quantity[]" min="1" value="1" required></td>
                                                {{-- <td><input type="text" class="form-control" name="product_unit[]" required></td> --}}
                                                <td><input type="number" class="form-control" name="price[]" min="0" value="0" step="0.001" required></td>
                                                <td><input type="number" class="form-control amount" name="amount[]" readonly></td>
                                                {{-- <td><input type="text" class="form-control remark" name="remark[]" ></td> --}}
                                                <td>
                                                    <select class="custom-select select2" style="width:100%;"
                                                    name="remark[]">
                                                        <option value="">Select Product Remark</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->name }}">{{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select> 
                                                </td>
                                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                            class="fas fa-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="row mb-3">
                            <div class="col-md-7"></div>
                            <div class="col-md-5">
                                <table class="table">
                                    <tr>
                                        <td>Subtotal</td>
                                        <td><input type="text" class="form-control" id="subtotal" name="subtotal"
                                                readonly></td>
                                    </tr>
                                    <tr>
                                        <td>P & F Charge</td>
                                        <td>
                                            <input type="number" step='0.1' class="form-control mt-2" id="pfcouriercharge"
                                                name="pfcouriercharge" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Discount</td>
                                        <td>
                                            <select class="custom-select" id="discount_type" name="discount_type">
                                                <option value="flat">Flat</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                            <input type="number" class="form-control mt-2" id="discount" step='any' name="discount" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CGST (%)</td>
                                        <td><input type="number" class="form-control" id="cgst" name="cgst"
                                                value="9" /></td>
                                    </tr>
                                    <tr>
                                        <td>SGST (%)</td>
                                        <td><input type="number" class="form-control" id="sgst" name="sgst"
                                                value="9" /></td>
                                    </tr>
                                    <tr>
                                        <td>IGST (%)</td>
                                        <td><input type="number" class="form-control" id="igst" name="igst" value="18" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="form-check-label" for="courier_charge_enabled">Courier Charge</label><br>
                                            <!-- <div class="form-check"> -->
                                                <label>
                                                    <input type="checkbox" class="form-check-input" id="courier_charge_enabled" name="courier_charge_enabled" value="1"> Add courier GST
                                                </label>
                                            <!-- </div> -->
                                        </td>
                                        <td><input type="number" class="form-control" id="courier_charge" name="courier_charge" value="0" /></td>
                                    </tr>
                                    <tr>
                                        <td>Round Off</td>
                                        <td><input type="text" class="form-control" id="round_off" name="round_off" /></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td><input type="text" class="form-control" id="total" name="balance" readonly></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            // Function to calculate amounts
            function calculateAmounts() { 
                let subtotal = 0;

                // Calculate subtotal from inventory table
                $('#inventoryTable tbody tr').each(function() {
                    const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
                    const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
                    const amount = qty * price;
                    subtotal += amount;
                    $(this).find('input.amount').val(amount.toFixed(2));
                });

                // Set subtotal value
                $('#subtotal').val(subtotal.toFixed(2));

                // Get P & F / Courier Charge
                const pfcouriercharge = parseFloat($('#pfcouriercharge').val()) || 0;
                const courier_charge = parseFloat($('#courier_charge').val()) || 0;

                // Calculate discount
                let discount = parseFloat($('#discount').val()) || 0;
                const discountType = $('#discount_type').val();
                if (discountType === 'percentage') {
                    discount = (subtotal * discount) / 100;
                }

                // Calculate grand total (Subtotal + P & F - Discount)
                const pfcourierchargeAmount = ((subtotal)*pfcouriercharge)/100;
                const grandTotal = (subtotal + pfcourierchargeAmount) - discount;

                // Get CGST and SGST rates and calculate tax
                const cgstRate = (parseFloat($('#cgst').val()) || 0) / 100;
                const sgstRate = (parseFloat($('#sgst').val()) || 0) / 100;
                const igstRate = (parseFloat($('#igst').val()) || 0) / 100;
                
                // Calculate total tax amounts on grand total
                const cgstAmount = grandTotal * cgstRate;
                const sgstAmount = grandTotal * sgstRate;
                const igstAmount = grandTotal * igstRate;

                // Calculate courier charge with or without GST based on checkbox
                const courierChargeEnabled = $('#courier_charge_enabled').is(':checked');
                let finalCourierCharge = 0;
                
                if (courierChargeEnabled && courier_charge > 0) {
                    // Courier charge with GST (CGST + SGST + IGST)
                    const totalGstRate = cgstRate + sgstRate + igstRate;
                    finalCourierCharge = courier_charge * (1 + totalGstRate);
                } else {
                    finalCourierCharge = courier_charge;
                }

                // Calculate final total (Grand total + SGST + CGST + Courier with GST)
                const finalTotal = grandTotal + cgstAmount + sgstAmount + igstAmount + finalCourierCharge;

                // Set final total
                var roundedValue = Math.round(finalTotal);
                var difference = roundedValue - finalTotal;
                $('#total').val(roundedValue);
                $('#round_off').val(difference.toFixed(2));
                
            }


            // Calculate amounts on change
            $(document).on('change', 
                'input[name="quantity[]"], input[name="price[]"], #discount, #discount_type, #cgst, #sgst, #igst, #courier_charge, #pfcouriercharge, #courier_charge_enabled', 
                calculateAmounts
            );




            function initializeSelect2() {
                $('.select2').select2({
                    placeholder: 'Select Product',
                    allowClear: true
                });
            }

            // Initialize Select2 on existing select elements
            initializeSelect2();

            // Add new row
            $(".add-row").click(function() {
                let newRow = `
            <tr>
                <td>
                    <select class="custom-select select2 product-select" style="width:100%;" name="item[]">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-quantity="{{ $product->available_quantity }}">{{ $product->name }} (Qty: {{ $product->available_quantity }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" class="form-control quantity-input" name="quantity[]" min="1" value="1" required></td>
                <td><input type="number" class="form-control" name="price[]" min="0" value="0" required></td>
                <td><input type="number" class="form-control amount" name="amount[]" readonly></td>
                <td>
                    <select class="custom-select select2" style="width:100%;" name="remark[]">
                        <option value="">Select Product Remark</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->name }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
            </tr>`;
                $('#inventoryTable tbody').append(newRow);
                // Reinitialize Select2 on newly added selects
                initializeSelect2();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateAmounts();
            });

            // Set max quantity when product is selected
            $(document).on('change', '.product-select', function() {
                var selectedOption = $(this).find('option:selected');
                var availableQty = selectedOption.data('quantity') || 0;
                var quantityInput = $(this).closest('tr').find('.quantity-input');
                
                // Set max attribute
                quantityInput.attr('max', availableQty);
                
                // Reset quantity to 1 if current value exceeds available
                if (parseInt(quantityInput.val()) > availableQty) {
                    quantityInput.val(availableQty > 0 ? 1 : 0);
                }
                
                // Recalculate amounts
                calculateAmounts();
            });

            // Validate quantity on input
            $(document).on('input', '.quantity-input', function() {
                var max = parseInt($(this).attr('max')) || 0;
                var val = parseInt($(this).val()) || 0;
                
                if (max > 0 && val > max) {
                    $(this).val(max);
                    alert('Maximum available quantity is ' + max);
                }
            });
        });
    </script>

    <script>
        // Auto-calculate due date (30 days from date)
        $(document).ready(function () {
            $('#date').change(function () {
                var selectedDate = $(this).val();
                if (selectedDate) {
                    var date = new Date(selectedDate);
                    date.setDate(date.getDate() + 30);
                    var dueDate = date.toISOString().split('T')[0];
                    $('#due_date').val(dueDate);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#client_name').change(function () {
                var clientId = $(this).val();
                
                if (clientId) {
                    // Make an AJAX request to fetch client details
                    $.ajax({
                        url: '/sales/get-client-details/' + clientId,
                        type: 'GET',
                        success: function (response) {
                            $('#address').val(response.address);
                        },
                        error: function () {
                            $('#address').val('');
                            alert('Error retrieving client details.');
                        }
                    });
                } else {
                    // Clear the address field if no client is selected
                    $('#address').val('');
                }
            });
        });

    </script>
    
    
@endsection
