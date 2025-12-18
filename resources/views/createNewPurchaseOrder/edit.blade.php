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
                        <h1 class="m-0">Edit New Purchase Order</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('newpurchaseorder.index') }}">New Purchase Order</a></li>
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

                <div class="card-body">
                    <form action="{{ route('newpurchaseorder.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="client_name" class="form-label">Client Name</label>
                                <select class="custom-select select2 spare-part" id="client_name" name="client_name" required>
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ $invoice->customer_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="invoice" class="form-label">Invoice #</label>
                                <input type="text" class="form-control" id="invoice" name="invoice"
                                    value="{{ $invoice->invoice }}"  required>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="custom-select" id="status" name="status" required>
                                    <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="completed" {{ $invoice->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="date" class="form-label">PO Revision & Date</label>
                                <input type="date" class="form-control" id="po_revision_and_date" name="po_revision_and_date" value="{{ $invoice->po_revision_and_date }}">
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">Reason of Revision</label>
                                <input type="text" class="form-control" id="reason_of_revision" name="reason_of_revision" value="{{ $invoice->reason_of_revision }}" >
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">Quotation Ref. No.</label>
                                <input type="text" class="form-control" id="quotation_ref_no" name="quotation_ref_no" value="{{ $invoice->quotation_ref_no }}" >
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">Remarks</label>
                                <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $invoice->remarks }}" >
                            </div>
                            <div class="col-md-2">
                                <label for="prno" class="form-label">P.R. No</label>
                                <input type="text" class="form-control" id="prno" name="prno" value="{{ $invoice->prno }}" >
                            </div>
                            <div class="col-md-2">
                                <label for="pr_date" class="form-label">P.R. Date</label>
                                <input type="date" class="form-control" id="pr_date" name="pr_date" value="{{ $invoice->pr_date }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" class="form-control" id="address">{{ $invoice->address }}</textarea>
                            </div>
                            <div class="col-md-6">
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
                                            @foreach ($invoice->items as $item)
                                                <tr>
                                                    <td>
                                                        <select class="custom-select select2" style="width:100%;" name="item[]">
                                                            <option value="">Select Spare Part</option>
                                                            @foreach ($spareParts as $part)
                                                                <option value="{{ $part->id }}" {{ $item->spare_part_id == $part->id ? 'selected' : '' }}>
                                                                    {{ $part->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><textarea class="form-control" name="material_specification[]">{{ $item->material_specification }}</textarea></td>
                                                    <td><input type="number" class="form-control" name="quantity[]" min="1" value="{{ $item->quantity }}" required></td>
                                                    <td><input type="text" class="form-control" name="unit[]" value="{{ $item->unit }}"></td>
                                                    <td><input type="number" step="0.01" class="form-control" name="rate_kgs[]" min="0" value="{{ $item->rate_kgs }}"></td>
                                                    <td><input type="number" step="0.01" class="form-control" name="per_pc_weight[]" min="0" value="{{ $item->per_pc_weight }}"></td>
                                                    <td><input type="number" step="0.01" class="form-control" name="total_weight[]" min="0" value="{{ $item->total_weight }}"></td>
                                                    <td><input type="number" step="0.01" class="form-control amount" name="amount[]" value="{{ $item->amount }}"></td>
                                                    <td><input type="date" class="form-control" name="delivery_date[]" value="{{ $item->delivery_date }}"></td>
                                                    <td><input type="text" class="form-control" name="remark[]" value="{{ $item->remark }}"></td>
                                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            @endforeach
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
    $(document).ready(function () {
        // Event listener for spare part selection
        $('.spare-part').change(function () {
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

                            // Get Quantity and Rate/Kgs from the form
                            const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
                            const rate = parseFloat(row.find('input[name="rate_kgs[]"]').val()) || 0;
                            const perPcWeight = parseFloat(sparePart.weight) || 0;

                            // Calculate and Fill Total Weight (qty * perPcWeight)
                            const totalWeight = qty * perPcWeight;
                            row.find('input[name="total_weight[]"]').val(totalWeight.toFixed(2));

                            // Calculate and Fill Amount (qty * rate)
                            const amount = qty * rate;
                            row.find('input[name="amount[]"]').val(amount.toFixed(2));
                        }
                    },
                    error: function () {
                        alert('Please Select Again');
                    }
                });
            }
        });

        // Function to calculate and update Total Weight and Amount
        function updateRowCalculations(row) {
            const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
            const perPcWeight = parseFloat(row.find('input[name="per_pc_weight[]"]').val()) || 0;
            const rate = parseFloat(row.find('input[name="rate_kgs[]"]').val()) || 0;

            // Calculate and update Total Weight (qty * Per Pc Weight)
            const totalWeight = qty * perPcWeight;
            row.find('input[name="total_weight[]"]').val(totalWeight.toFixed(2));

            // Calculate and update Amount (qty * Rate/Kgs)
            const amount = qty * rate;
            row.find('input[name="amount[]"]').val(amount.toFixed(2));
        }

        // Event listener for Quantity and Rate change to update Total Weight and Amount
        $(document).on('input', 'input[name="quantity[]"], input[name="rate_kgs[]"]', function () {
            var row = $(this).closest('tr'); // Get the current row where the input changed
            updateRowCalculations(row); // Recalculate Total Weight and Amount
        });
    });
</script>

<script>
    $(document).ready(function() {
        function calculateAmounts() {
            $('#inventoryTable tbody tr').each(function() {
                const quantity = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
                const rateKgs = parseFloat($(this).find('input[name="rate_kgs[]"]').val()) || 0;
                const perPcWeight = parseFloat($(this).find('input[name="per_pc_weight[]"]').val()) || 0;
                const totalWeight = quantity * perPcWeight;
                // $(this).find('input[name="total_weight[]"]').val(totalWeight.toFixed(2));

                const amount = totalWeight * rateKgs;
                // $(this).find('input.amount').val(amount.toFixed(2));
            });
        }

        // Calculate amounts on change
        $(document).on('change', 'input[name="quantity[]"], input[name="rate_kgs[]"], input[name="per_pc_weight[]"]', function() {
            calculateAmounts();
        });

        // Add new row
        $('.add-row').click(function() {
            const newRow = `
                <tr>
                    <td>
                        <select class="custom-select select2 spare-part " style="width:100%;" name="item[]">
                            <option value="">Select Spare Part</option>
                            @foreach ($spareParts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><textarea class="form-control" name="material_specification[]"></textarea></td>
                    <td><input type="number" class="form-control" name="quantity[]" min="1" value="1" required></td>
                    <td><input type="text" class="form-control" name="unit[]" value=""></td>
                    <td><input type="number" step="0.01" class="form-control" name="rate_kgs[]" min="0" value="0"></td>
                    <td><input type="number" step="0.01" class="form-control" name="per_pc_weight[]" min="0" value="0"></td>
                    <td><input type="number" step="0.01" class="form-control" name="total_weight[]" min="0" value="0" ></td>
                    <td><input type="number" step="0.01" class="form-control amount" name="amount[]"></td>
                    <td><input type="date" class="form-control" name="delivery_date[]" value=""></td>
                    <td><input type="text" class="form-control" name="remark[]"></td>
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
    });
</script>

<script>
    $(document).ready(function () {
        // Event listener for spare part selection
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

                            // Get Quantity and Rate/Kgs from the form
                            const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
                            const rate = parseFloat(row.find('input[name="rate_kgs[]"]').val()) || 0;
                            const perPcWeight = parseFloat(sparePart.weight) || 0;

                            // Calculate and Fill Total Weight (qty * perPcWeight)
                            const totalWeight = qty * perPcWeight;
                            row.find('input[name="total_weight[]"]').val(totalWeight.toFixed(3));

                            // Calculate and Fill Amount (qty * rate)
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
        
        
         // Function to calculate and update Total Weight and Amount
        function updateRowCalculations(row) {
            // Get the input values
            const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
            const perPcWeight = parseFloat(row.find('input[name="per_pc_weight[]"]').val()) || 0;
            const rate = parseFloat(row.find('input[name="rate_kgs[]"]').val()) || 0;

            // Calculate and update Total Weight (qty * Per Pc Weight)
            const totalWeight = qty * perPcWeight;
            row.find('input[name="total_weight[]"]').val(totalWeight.toFixed(2));

            // Calculate and update Amount (Quantity * Rate/PC)
            const amount = qty * rate;
            row.find('input[name="amount[]"]').val(amount.toFixed(2));
        }

        // Event listener for Quantity, Per Pc Weight, and Rate/Kgs changes
        $(document).on('change', 'input', function () {
            var row = $(this).closest('tr'); // Get the current row where the input changed
            updateRowCalculations(row); // Recalculate Total Weight and Amount
        });


    });
</script>




@endsection
