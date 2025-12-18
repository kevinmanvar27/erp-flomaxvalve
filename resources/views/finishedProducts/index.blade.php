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
                            <a href="{{ route('finishedProducts.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add Finished Product
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Finished Products</li>
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
                                <h3 class="card-title">Finished Products</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="finished-products-table" class="display">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Created By</th>
                                            <th>Upadted At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Created By</th>
                                            <th>Upadted At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            $('#finished-products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('finishedProducts.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'updated_at', name: 'updated_at' }, // 👈 Changed here
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
    
            if (!confirm('Are you sure you want to delete this finished product?')) {
                return;
            }
    
            var form = $(this).closest('form');
            var url = form.attr('action');
    
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    alert('Finished product deleted successfully.');
                    $('#finished-products-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error deleting finished product. Please try again.');
                }
            });
        });
    </script>

@endsection