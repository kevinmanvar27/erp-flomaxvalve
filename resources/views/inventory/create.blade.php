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
                            <li class="breadcrumb-item"><a href="{{ route('purchaseOrder.index') }}">Purchase Order</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <section class="content">

            <div class="card">
                <div class="card-header">
                    <h3>Create Purchase Order</h3>
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
                    <form action="{{ route('purchaseOrder.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="create_date" class="form-label">Create Date</label>
                                <input type="date" class="form-control" id="create_date" name="create_date" required>
                            </div>
                            <div class="col-md-4">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-control select2" style="width: 100%;" id="supplier_id"
                                    name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="purchase_order_invoice" class="form-label">Purchase Order Invoice (PDF
                                    only) (Optional)</label>
                                <input type="file" class="form-control" id="purchase_order_invoice"
                                    name="purchase_order_invoice" accept=".pdf">
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
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-control select2" style="width:100%;"
                                                        name="parts_name[]">
                                                        <option value="">Select Spare Part</option>
                                                        @foreach ($parts as $part)
                                                            <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control" name="quantity[]"
                                                        min="0" value="0" required></td>
                                                {{-- <td><input type="text" class="form-control" name="sku_code[]" required>
                                                </td> --}}
                                                <td><input type="number" class="form-control" name="amount[]"
                                                        min="0" value="0" required></td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                            class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-success float-right">Create Purchase</button>
                            </div>
                        </div>

                    </form>
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
                    <select class="form-control select2" style="width:100%;" name="parts_name[]">
                        <option value="">Select Spare Part</option>
                        @foreach ($parts as $part)
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

                // Reinitialize Select2 on newly added select elements
                initializeSelect2();
            });

            // Remove row function
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
