@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('newpurchaseorder.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add New Purchase Order
                                </button>
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">New Purchase Order</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">New Purchase Order</h3>
                            </div>
                            <div class="card-body">
                                <table id="sales-table" class="display">
                                    <thead>
                                        <th>SL No.</th>
                                        <th>Invoice Number</th>
                                        <th>Client Name</th>
                                        <th>Total Items</th>
                                        <th>Receive Status</th>
                                        <th>Action</th>
                                    </thead>
                                    <tfoot>
                                        <th>SL No.</th>
                                        <th>Invoice Number</th>
                                        <th>Client Name</th>
                                        <th>Total Items</th>
                                        <th>Receive Status</th>
                                        <th>Action</th>
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
            $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('newpurchaseorder.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'invoice', name: 'invoice' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'total_item', name: 'total_item' },
                    { data: 'receive_status', name: 'receive_status' },
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

            if (!confirm('Are you sure you want to delete this invoice?')) {
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
                    alert(data.success || 'Invoice deleted successfully.');
                    $('#sales-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });
    </script>
@endsection
