@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('rejection.createInternalRejection') }}">
                                <button type="button" class="btn btn-success">
                                    Add Internal Rejection
                                </button>
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Internal Rejection</li>
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
                                <h3 class="card-title">Internal Rejection Products</h3>
                            </div>
                            <div class="card-body">
                                <!-- Date Range Filter -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <input type="date" id="from-date" class="form-control" placeholder="From Date">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" id="to-date" class="form-control" placeholder="To Date">
                                    </div>
                                    <div class="col-md-2">
                                        <button id="apply-filter" class="btn btn-primary">Apply Filter</button>
                                    </div>
                                </div>

                                <table id="rejection-table" class="display">
                                    <thead>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>User Code</th>
                                            <th>Part Name</th>
                                            <th>Quantity</th>
                                            <th>Reason</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>User Code</th>
                                            <th>Part Name</th>
                                            <th>Quantity</th>
                                            <th>Reason</th>
                                            <th>Date</th>
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
            // Initialize DataTable
            var table = $('#rejection-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('rejection.internalGetData') }}',
                    data: function(d) {
                        d.from_date = $('#from-date').val(); // Add from date filter to the request
                        d.to_date = $('#to-date').val();   // Add to date filter to the request
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user_code', name: 'user_code' },
                    { data: 'part_name', name: 'part_name' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'reason', name: 'reason' },
                    { data: 'date', name: 'date' },
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

            // Apply filter when the button is clicked
            $('#apply-filter').click(function() {
                table.draw();
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();

                if (!confirm('Are you sure you want to delete this internal rejection?')) {
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
                        alert(data.success);
                        console.log(JSON.stringify(data.data, null, 2)); 
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'An error occurred while deleting the internal rejection.';
                        alert(errorMessage);
                    }
                });
            });
        });
    </script>

@endsection
