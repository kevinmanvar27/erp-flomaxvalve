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
                        <h1 class="m-0">Create New Purchase Order</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('newpurchaseorder.index') }}">New Purchase Order</a></li>
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

                <div class="card-body">
                    <form action="{{ route('newpurchaseorder.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="client_name" class="form-label">Client Name</label>
                                <select class="custom-select select2" id="client_name" name="client_name" required>
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="invoice" class="form-label">Invoice #</label>
                                <input type="text" class="form-control" id="invoice" name="invoice"
                                    value="{{ $invoiceNumber }}"  required>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="custom-select" id="status" name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>                            
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="date" class="form-label">PO Revision & Date</label>
                                <input type="date" class="form-control" id="po_revision_and_date" name="po_revision_and_date" required>
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">Reason of Revision</label>
                                <input type="text" class="form-control" id="reason_of_revision" name="reason_of_revision" value="" >
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">Quotation Ref. No.</label>
                                <input type="text" class="form-control" id="quotation_ref_no" name="quotation_ref_no" value="" >
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">Remarks</label>
                                <input type="text" class="form-control" id="remarks" name="remarks" value="" >
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">P.R. No</label>
                                <input type="text" class="form-control" id="prno" name="prno"
                                    value="" >
                            </div>
                            <div class="col-md-2">
                                <label for="pr_date" class="form-label">P.R. Date</label>
                                <input type="date" class="form-control" id="pr_date" name="pr_date" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" class="form-control" id="address"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" class="form-control" id="note"></textarea>
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
                                                <th>Spare Part</th>
                                                <th>Material / Specification</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Rate/PC</th>
                                                <th>Per Pc Weight</th>
                                                <th>Total Weight</th>
                                                <th>Amount</th>
                                                <th>Delivery Date</th>
                                                <th>Remark</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="custom-select select2 spare-part" style="width:100%;" name="item[]">
                                                        <option value="">Select Spare Part</option>
                                                        @foreach ($spareParts as $part)
                                                            <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><textarea class="form-control" name="material_specification[]"></textarea></td>
                                                <td><input type="number" class="form-control" name="quantity[]" min="1" value="1" required></td>
                                                <td><input type="text" class="form-control" name="unit[]" value=""></td>
                                                <td><input type="number" step="0.001" class="form-control" name="rate_kgs[]" min="0" ></td>
                                                <td><input type="number" step="0.001" class="form-control" name="per_pc_weight[]" min="0"  ></td>
                                                <td><input type="number" step="0.001" class="form-control" name="total_weight[]" min="0" ></td>
                                                <td><input type="number" step="0.001" class="form-control amount" name="amount[]"></td>
                                                <td><input type="date" class="form-control" name="delivery_date[]" value=""></td>
                                                <td><input type="text" class="form-control" name="remark[]"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                </div>
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
           
           initializeSelect2();
           
            // Function to calculate amounts
            function calculateAmounts() {
                let subtotal = 0;
                $('#inventoryTable tbody tr').each(function() {
                    const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
                    const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
                    const amount = qty * price;
                    subtotal += amount;
                    $(this).find('input.amount').val(amount.toFixed(2));
                });
                $('#subtotal').val(subtotal.toFixed(2));

                let discount = parseFloat($('#discount').val()) || 0;
                const discountType = $('#discount_type').val();
                if (discountType === 'percentage') {
                    discount = (subtotal * discount) / 100;
                }

                const cgst = (parseFloat($('#cgst').val()) || 0) / 100;
                const sgst = (parseFloat($('#sgst').val()) || 0) / 100;

                const total = (subtotal - discount) + (subtotal * cgst) + (subtotal * sgst);
                $('#total').val(total.toFixed(2));
            }

            // // Calculate amounts on change
            // $(document).on('change',
            //     'input[name="quantity[]"], input[name="price[]"], #discount, #discount_type, #cgst, #sgst',
            //     calculateAmounts);

            function initializeSelect2() {
                // Reinitialize select2 for all elements, including new rows
                $('.select2').select2({
                    placeholder: 'Select Spare Part',
                    allowClear: true,
                    width: '100%' // Ensure proper width handling
                });
            }

            // Initialize Select2 on existing select elements
            initializeSelect2();

            // Add new row
            $(".add-row").click(function() {
                let newRow = `
                <tr>
                    <td>
                        <select class="custom-select select2 spare-part" style="width:100%;" name="item[]">
                            <option value="">Select Spare Part</option>
                            @foreach ($spareParts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <textarea class="form-control" name="material_specification[]"></textarea>
                    </td>
                    <td><input type="number" class="form-control" name="quantity[]" min="1" value="1" required></td>
                    <td><input type="text" class="form-control" name="unit[]" value=""></td>
                    <td><input type="number" step="0.001" class="form-control" name="rate_kgs[]" min="0" ></td>
                    <td><input type="number" step="0.001" class="form-control" name="per_pc_weight[]" min="0" ></td>
                    <td><input type="number" step="0.001" class="form-control" name="total_weight[]" min="0" ></td>
                    <td><input type="number" step="0.001" class="form-control amount" name="amount[]"></td>
                    <td><input type="date" class="form-control" name="delivery_date[]" value=""></td>
                    <td><input type="text" class="form-control" name="remark[]"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                </tr>`;
                $('#inventoryTable tbody').append(newRow);
                initializeSelect2();  // Reinitialize Select2 for the new row
            });


            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateAmounts();
            });
        });

    </script>

    <script>
        $(document).ready(function () {
            $(document).on('change', '.spare-part', function () {
                var sparePartId = $(this).val();
                
                var row = $(this).closest('tr'); // Get the row where the change occurred
    
                if (sparePartId) {
                    $.ajax({
                        url: `/newpurchaseorder/get-part-details/${sparePartId}`,
                        type: 'get',
                        success: function (response) {
                            if (response.success) {
                                const sparePart = response.sparePart;
                                
                                // Fill the Unit
                                row.find('input[name="unit[]"]').val(sparePart.unit);
    
                                // Fill the Per Pc Weight
                                row.find('input[name="per_pc_weight[]"]').val(sparePart.weight);
    
                                // Get Quantity and Rate/PC from the form
                                const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
                                const rate = parseFloat(row.find('input[name="rate_kgs[]"]').val()) || 0;
                                const perPcWeight = parseFloat(sparePart.weight) || 0;
    
                                // Calculate and Fill Total Weight (qty * perPcWeight)
                                const totalWeight = qty * perPcWeight;
                                row.find('input[name="total_weight[]"]').val(totalWeight.toFixed(3));
    
                                // Calculate and Fill Amount (Quantity * Rate/PC)
                                const amount = qty * rate;
                                row.find('input[name="amount[]"]').val(amount.toFixed(3));
                            }
                        },
                        error: function () {
                            alert('Please Select Again');
                        }
                    });
                }
            });
           
           
           $(document).on('change', 'input', function () {
                var row = $(this).closest('tr'); // Get the current row where the input changed
                updateRowCalculations(row); // Recalculate Total Weight and Amount
            });
            
            function updateRowCalculations(row) {
                // Get the input values
                const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
                const perPcWeight = parseFloat(row.find('input[name="per_pc_weight[]"]').val()) || 0;
                const rate = parseFloat(row.find('input[name="rate_kgs[]"]').val()) || 0;
            
                // Calculate and update Total Weight (qty * Per Pc Weight)
                const totalWeight = qty * perPcWeight;
                row.find('input[name="total_weight[]"]').val(totalWeight.toFixed(3));
            
                // Calculate and update Amount (Quantity * Rate/PC)
                const amount = qty * rate;
                row.find('input[name="amount[]"]').val(amount.toFixed(3));
            }

           
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
