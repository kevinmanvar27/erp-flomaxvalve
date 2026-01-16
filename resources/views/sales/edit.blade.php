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
                        <h1 class="m-0">Edit Sales</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                            <li class="breadcrumb-item active">Edit</li>
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

                <!--<div class="card-header">-->
                <!--    <h3>Edit Sales</h3>-->
                <!--</div>-->
                <div class="card-body">
                    <form action="{{ route('sales.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="client_name" class="form-label">Client Name</label>
                                <select class="custom-select select2" id="client_name" name="client_name" required>
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ $invoice->customer_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="invoice" class="form-label">Invoice #</label>
                                <input type="text" class="form-control" id="invoice" name="invoice"
                                    value="{{ $invoice->invoice }}"  required>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="custom-select" id="status" name="status" required>
                                    <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="completed" {{ $invoice->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ $invoice->create_date }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ $invoice->due_date }}" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="orderno" class="form-label">Order No</label>
                                <input type="text" class="form-control" id="orderno" name="orderno" value="{{ $invoice->orderno }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="lrno" class="form-label">L.R. No</label>
                                <input type="text" class="form-control" id="lrno" name="lrno" value="{{ $invoice->lrno }}">
                            </div>
                            <div class="col-md-3">
                                <label for="transport" class="form-label">Transport</label>
                                <input type="text" class="form-control" id="transport" name="transport"  value="{{ $invoice->transport }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" class="form-control" id="address">{{ $invoice->address }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" class="form-control" id="note">{{ $invoice->note }}</textarea>
                            </div>
                        </div>

                        <!-- Inventory Table -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary add-row mb-2"><i class="fas fa-plus"></i> Add Row</button>
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
                                            @foreach ($invoice->items as $item)
                                                <tr>
                                                    <td>
                                                        <select class="custom-select select2" style="width:100%;" name="item[]">
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }} (Qty: {{ $product->available_quantity }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    
                                                    <td><input type="number" class="form-control" name="quantity[]" min="1" value="{{ $item->quantity }}" required></td>
                                                    {{-- <td><input type="text" class="form-control" name="product_unit[]" value="{{ $item->product_unit }}" required></td> --}}
                                                    <td><input type="number" class="form-control" name="price[]" min="0" value="{{ $item->price }}" step="0.001" required></td>
                                                    <td><input type="number" class="form-control amount" name="amount[]" value="{{ $item->amount }}" readonly></td>
                                                    {{-- <td><input type="text" class="form-control remark" name="remark[]" value="{{ $item->remark }}" ></td> --}}
                                                    <td>
                                                        <select class="custom-select select2" style="width:100%;" name="remark[]">
                                                            <option value="">Select Product remark</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->name }}" {{ $item->remark == $product->name ? 'selected' : '' }}>
                                                                    {{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            @endforeach
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
                                        <td><input type="text" class="form-control" id="subtotal" name="subtotal" value="{{ $invoice->sub_total }}" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>Courier (With GST)</td>
                                        <td><input type="number" class="form-control" id="courier_charge_before_gst" name="courier_charge_before_gst" value="{{ $invoice->courier_charge_before_gst ?? 0 }}" /></td>
                                    </tr>
                                    <tr>
                                        <td>P & F Charge</td>
                                        <td>
                                            <input type="number" step='0.1' class="form-control mt-2" id="pfcouriercharge" value="{{ $invoice->pfcouriercharge }}"
                                                name="pfcouriercharge" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Discount</td>
                                        <td>
                                            <select class="custom-select" id="discount_type" name="discount_type">
                                                <option value="flat" {{ $invoice->discount_type == 'flat' ? 'selected' : '' }}>Flat</option>
                                                <option value="percentage" {{ $invoice->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                            </select>
                                            <input type="number" class="form-control mt-2" id="discount" name="discount" value="{{ $invoice->discount }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CGST (%)</td>
                                        <td><input type="number" class="form-control" id="cgst" name="cgst" value="{{ $invoice->cgst }}"></td>
                                    </tr>
                                    <tr>
                                        <td>SGST (%)</td>
                                        <td><input type="number" class="form-control" id="sgst" name="sgst" value="{{ $invoice->sgst }}"></td>
                                    </tr>
                                    <tr>
                                        <td>IGST (%)</td>
                                        <td><input type="number" class="form-control" id="igst" name="igst" value="{{ $invoice->igst }}"></td>
                                    </tr>
                                    <tr>
                                        <td>Courier (Without GST)</td>
                                        <td><input type="number" class="form-control" id="courier_charge_after_gst" name="courier_charge_after_gst" value="{{ $invoice->courier_charge_after_gst ?? 0 }}" /></td>
                                    </tr>
                                    <tr>
                                        <td>Round Off</td>
                                        <td><input type="number" class="form-control" step="any" id="round_off" /></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td><input type="text" class="form-control" id="total" name="balance" value="{{ $invoice->balance }}" readonly></td>
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
    <script>
        $(document).ready(function() {
            // Enable disabled courier fields before form submit to ensure values are sent
            $('form').on('submit', function() {
                $('#courier_charge_before_gst, #courier_charge_after_gst').prop('disabled', false);
            });

            // Function to calculate amounts
            calculateAmounts();
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

                // Get P & F / Courier Charges
                const pfcouriercharge = parseFloat($('#pfcouriercharge').val()) || 0;
                const courierBeforeGst = parseFloat($('#courier_charge_before_gst').val()) || 0;
                const courierAfterGst = parseFloat($('#courier_charge_after_gst').val()) || 0;

                // Calculate discount
                let discount = parseFloat($('#discount').val()) || 0;
                const discountType = $('#discount_type').val();
                if (discountType === 'percentage') {
                    discount = (subtotal * discount) / 100;
                }

                // Calculate P&F charge amount
                const pfcourierchargeAmount = ((subtotal) * pfcouriercharge) / 100;
                
                // Grand total includes courier_before_gst (GST will be applied on this)
                const grandTotal = (subtotal + pfcourierchargeAmount + courierBeforeGst) - discount;

                // Get CGST, SGST, IGST rates and calculate tax
                const cgstRate = (parseFloat($('#cgst').val()) || 0) / 100;
                const sgstRate = (parseFloat($('#sgst').val()) || 0) / 100;
                const igstRate = (parseFloat($('#igst').val()) || 0) / 100;

                // Calculate total tax amounts on grand total (which includes courier_before_gst)
                const cgstAmount = grandTotal * cgstRate;
                const sgstAmount = grandTotal * sgstRate;
                const igstAmount = grandTotal * igstRate;

                // Calculate final total (Grand total + GST + Courier After GST)
                const finalTotal = grandTotal + cgstAmount + sgstAmount + igstAmount + courierAfterGst;

                // Set final total with round off
                var roundedValue = Math.round(finalTotal);
                var difference = roundedValue - finalTotal;
                $('#total').val(roundedValue);
                $('#round_off').val(difference.toFixed(2));
            }

            // Calculate amounts on change
           
            $(document).on('change', 
                'input[name="quantity[]"], input[name="price[]"], #discount, #discount_type, #cgst, #sgst, #igst, #courier_charge_before_gst, #courier_charge_after_gst, #pfcouriercharge', 
                calculateAmounts
            );

            // Courier field toggle - disable one when other has value
            function toggleCourierFields() {
                const beforeGst = parseFloat($('#courier_charge_before_gst').val()) || 0;
                const afterGst = parseFloat($('#courier_charge_after_gst').val()) || 0;
                
                if (beforeGst > 0) {
                    $('#courier_charge_after_gst').prop('disabled', true).css('background-color', '#e9ecef');
                } else if (afterGst > 0) {
                    $('#courier_charge_before_gst').prop('disabled', true).css('background-color', '#e9ecef');
                } else {
                    $('#courier_charge_before_gst').prop('disabled', false).css('background-color', '');
                    $('#courier_charge_after_gst').prop('disabled', false).css('background-color', '');
                }
            }

            // Run on input for immediate feedback
            $(document).on('input', '#courier_charge_before_gst, #courier_charge_after_gst', function() {
                toggleCourierFields();
                calculateAmounts();
            });

            // Initialize on page load
            toggleCourierFields();



            // Add new row
            $('.add-row').click(function() {
                const newRow = `
                    <tr>
                        <td>
                            <select class="custom-select select2" style="width:100%;" name="item[]">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Qty: {{ $product->available_quantity }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" class="form-control" name="quantity[]" min="1" required></td>
                        <td><input type="number" class="form-control" name="price[]" min="0" required></td>
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
                $('.select2').select2();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateAmounts();
            });

            // Initialize select2
            $('.select2').select2();

            // Auto-calculate due date (30 days from date)
            $('#date').change(function () {
                var selectedDate = $(this).val();
                if (selectedDate) {
                    var date = new Date(selectedDate);
                    date.setDate(date.getDate() + 45);
                    var dueDate = date.toISOString().split('T')[0];
                    $('#due_date').val(dueDate);
                }
            });
        });
    </script>
@endsection
