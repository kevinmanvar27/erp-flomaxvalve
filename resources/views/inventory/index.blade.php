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
                            <a href="{{ route('purchaseOrder.create') }}" class="btn btn-success">Add Purchase Order</a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Received Purchase Order</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Received Purchase Order</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="inventory-table" class="display">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Create Date</th>
                                            <th>Invoice Number</th>
                                            <th>Supplier Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Create Date</th>
                                            <th>Invoice Number</th>
                                            <th>Supplier Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <script>
        $(document).ready(function() {
            $('#inventory-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('purchaseOrder.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'create_date',
                        name: 'create_date'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                paging: true,
                lengthChange: true,
                pageLength: 10,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
            });
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            // Confirm deletion with the user
            if (!confirm('Are you sure you want to delete this inventory?')) {
                return;
            }

            var form = $(this).closest('form');
            var url = form.attr('action');

            // Send the AJAX DELETE request
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    alert(data.success || 'Inventory deleted successfully.');

                    $('#inventory-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting inventory:', error);
                }
            });
        });
    </script>
@endsection
