@extends('layouts.app')

@section('content')
    <div class="content-wrapper mt-5">
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Purchase Order</h3>
                </div>
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
                    <form action="{{ route('purchaseOrder.update', $inventory->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="create_date" class="form-label">Create Date</label>
                                <input type="date" class="form-control" id="create_date" name="create_date"
                                    value="{{ old('create_date', $inventory->create_date) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                    value="{{ old('invoice_number', $inventory->invoice_number) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="custom-select" id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($stakeHolders as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ $supplier->id == $inventory->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="purchase_order_invoice" class="form-label">Purchase Order Invoice (PDF) (Optional)</label>
                                <input type="file" class="form-control" id="purchase_order_invoice"
                                    name="purchase_order_invoice" accept="application/pdf">
                                @if ($inventory->purchase_order_invoice)
                                    <small class="form-text text-muted">
                                        Show Current File:
                                        <a href="{{ route('purchaseOrder.showFile', $inventory->id) }}" target="_blank">Show
                                            File</a>
                                    </small>
                                @endif

                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h4 class="mb-3">Purchase Order Items <button type="button"
                                        class="btn btn-primary add-row float-right"><i class="fas fa-plus"></i> Add
                                        New</button></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>Parts Name</th>
                                                <th>Quantity</th>
                                                {{-- <th>SKU Code</th> --}}
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inventory->items as $item)
                                                <tr>
                                                    <input type="hidden" value="{{ $item->id }}" name="itemIds[]" />
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
                                                    <td><input type="number" class="form-control" name="quantity[]"
                                                            min="0" value="{{ $item->quantity }}" required></td>
                                                    {{-- <td><input type="text" class="form-control" name="sku_code[]"
                                                            value="{{ $item->sku_code }}" required></td> --}}
                                                    <td><input type="number" class="form-control" name="amount[]"
                                                            min="0" value="{{ $item->amount }}" required></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                                class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-success float-right">Update Purchase Order</button>
                            </div>
                        </div>
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
                            @foreach ($spareParts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control" name="quantity[]" min="0" value="0" required></td>
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
