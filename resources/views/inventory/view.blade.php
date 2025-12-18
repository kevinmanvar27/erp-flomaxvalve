@extends('layouts.app')

@section('content')
<div class="content-wrapper mt-5">
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3>Create Purchase Order</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('purchaseOrder.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="create_date" class="form-label">Create Date</label>
                            <input type="date" class="form-control" id="create_date" name="create_date" required>
                        </div>
                        <div class="col-md-4">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="custom-select" id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h4 class="mb-3">Purchase Items</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="inventoryTable">
                                    <thead>
                                        <tr>
                                            <th>Parts Name</th>
                                            <th>Quantity</th>
                                            <th>SKU Code</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="custom-select" name="parts_name[]">
                                                    <option value="">Select Spare Part</option>
                                                    @foreach ($parts as $part)
                                                        <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control" name="quantity[]" min="0" value="0" required></td>
                                            <td><input type="text" class="form-control" name="sku_code[]" required></td>
                                            <td><input type="number" class="form-control" name="amount[]" min="0" value="0" required></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-primary add-row"><i class="fas fa-plus"></i> Add Row</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Create Purchase Order</button>
                </form>
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
                            @foreach ($parts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control" name="quantity[]" min="0" value="0" required></td>
                    <td><input type="text" class="form-control" name="sku_code[]" required></td>
                    <td><input type="number" class="form-control" name="amount[]" min="0" value="0" required></td>
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
@endsection
