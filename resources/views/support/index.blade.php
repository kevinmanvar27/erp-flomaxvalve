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
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
       
        <!-- Content Header (Page header) -->
        <div class="content-header">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#supportModal">
                                Add Support
                            </button>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Support</li>
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
                                <h3 class="card-title">Supports Details</h3>
                            </div>
                            <div class="card-body">
                                <table id="supportTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Problem</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Problem</th>
                                            <th>Status</th>
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

    
    <!-- Add/Edit Support Modal -->
    <div class="modal fade" id="supportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('support.store') }}" method="POST" id="supportForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="supportModalLabel">Add Support</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="supportId" name="id">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select class="form-control select2" id="product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="problem">Problem</label>
                            <textarea class="form-control" id="problem" name="problem" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Add/Edit Support Modal -->
<div class="modal fade" id="supportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="supportForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Add Support</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="supportId" name="id">
                    <div class="form-group">
                        <label for="product_id">Product</label>
                        <select class="form-control select2" id="product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="problem">Problem</label>
                        <textarea class="form-control" id="problem" name="problem" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


    

    <script>
        $(document).ready(function() {
            $('#supportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('support.data') }}',
                columns: [
                    {data: 'id', name: 'id' },
                    {data: 'product'},
                    {data: 'problem'},
                    {data: 'status'},
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                paging: true, // Ensure paging is enabled
                lengthChange: true, // Allow users to change page length
                pageLength: 10, // Number of records per page
                searching: true, // Enable search
                ordering: true, // Enable sorting
                info: true, // Show table information
                autoWidth: false, // Disable automatic column width calculation
            });
        });
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#addSparePartForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('parts.store') }}", // Adjust this route as necessary
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Handle success response
                        alert('Spare part added successfully');
                        location.reload(); // Reload the page to see the new data
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        alert('Error: ' + error);
                    }
                });
            });
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this support?')) {
                return;
            }

            var form = $(this).closest('form');
            var url = form.attr('action');

            $.ajax({
                url: url,
                method: 'POST', // Use POST to send DELETE request
                data: {
                    _method: 'DELETE', // Specify DELETE method
                    _token: '{{ csrf_token() }}' // Include CSRF token
                },
                success: function(response) {
                    alert(response.success);

                    // Refresh DataTable
                    $('#supportTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting support:', error);
                }
            });
        });


        function openEditModal(id) {
            $.ajax({
                url: "{{ url('support') }}/" + id + "/edit",
                method: 'GET',
                success: function(data) {
                    // Populate the modal fields with the response data
                    $('#supportModal #supportId').val(data.id);
                    $('#supportModal #product_id').val(data.product_id).trigger('change');
                    $('#supportModal #problem').val(data.problem);
                    $('#supportModal #status').val(data.status);

                    // Update modal title and form action
                    $('#supportModal .modal-title').text('Edit Support');
                    $('#supportForm').attr('action', "{{ url('support') }}/" + data.id);
                    $('#supportForm').append('<input name="_method" type="hidden" value="PUT">');

                    // Open the modal
                    $('#supportModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching support data:', error);
                }
            });
        }


    </script>
@endsection
