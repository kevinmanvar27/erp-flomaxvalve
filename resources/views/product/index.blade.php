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
                            <a href="{{ route('products.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add Product
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Products</li>
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
                                <h3 class="card-title">Products</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="products-table" class="display">
                                    <thead>
                                        <th>SL No.</th>
                                        <th>Product Name</th>
                                        <th>Valve Type</th>
                                        {{-- <th>Primary Material of Construction</th> --}}
                                        <th>SKU Code</th>
                                        <th>Pressure Rating</th>
                                        <th>Actuation</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tfoot>
                                        <th>SL No.</th>
                                        <th>Product Name</th>
                                        <th>Valve Type</th>
                                        {{-- <th>Primary Material of Construction</th> --}}
                                        <th>SKU Code</th>
                                        <th>Pressure Rating</th>
                                        <th>Actuation</th>
                                        <th>Actions</th>
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
            $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'valve_type', name: 'valve_type' },
                    // { data: 'primary_material_of_construction', name: 'primary_material_of_construction' },
                    { data: 'sku_code', name: 'sku_code' },
                    { data: 'pressure_rating', name: 'pressure_rating' },
                    { data: 'actuation', name: 'actuation' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
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
            if (!confirm('Are you sure you want to delete this product?')) {
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
                    alert(data.success || 'Product deleted successfully.');

                    $('#products-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });

    </script>

@endsection
