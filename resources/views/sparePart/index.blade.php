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
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addSparePartModal">
                                Add Parts
                            </button>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Parts</li>
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
                                <h3 class="card-title">Parts</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="sparePartsTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Weight</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>Minimum Qty</th>
                                            <th>Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Weight</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>Minimum Qty</th>
                                            <th>Rate</th>
                                            <th>Action</th>
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

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="editSparePartModal" tabindex="-1" role="dialog" aria-labelledby="editSparePartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSparePartModalLabel">Edit Spare Part</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form to edit spare part -->
                    <form id="editSparePartForm">
                        <input type="hidden" id="editSparePartId">
                        <div class="form-group">
                            <label for="editName">Name</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="form-group">
                            <label for="editType">Type</label>
                            <input type="text" class="form-control" id="editType" required>
                        </div>
                        <div class="form-group">
                            <label for="editSize">Size</label>
                            <input type="text" class="form-control" id="editSize" required>
                        </div>
                        <div class="form-group">
                            <label for="editWeight">Weight</label>
                            <input type="number" step="0.001" class="form-control" id="editWeight" required>
                        </div>
                        <div class="form-group">
                            <label for="editUnit">Unit</label>
                            <input type="text" class="form-control" id="editUnit">
                        </div>
                        <div class="form-group">
                            <label for="editQuantity">Quantity</label>
                            <input type="number" class="form-control" id="editQuantity">
                        </div>
                        <div class="form-group">
                            <label for="editMinimumQuantity">Minimum Quantity</label>
                            <input type="number" class="form-control" id="editMinimumQuantity">
                        </div>
                        
                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <input type="number" step='0.001' class="form-control" id="editRate" name="rate" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Part Modal -->
    <div class="modal fade" id="addSparePartModal" tabindex="-1" role="dialog" aria-labelledby="addSparePartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSparePartModalLabel">Add Part</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addSparePartForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Form fields -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <input type="text" class="form-control" id="type" name="type" required>
                        </div>
                        <div class="form-group">
                            <label for="size">Size</label>
                            <input type="text" class="form-control" id="size" name="size" required>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight</label>
                            <input type="number" step="0.001" class="form-control" id="weight" name="weight"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="unit">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>
                        <div class="form-group">
                            <label for="qty">Quantity</label>
                            <input type="number" class="form-control" id="qty" name="qty" required>
                        </div>
                        <div class="form-group">
                            <label for="minimum_qty">Minimum Quantity</label>
                            <input type="number" class="form-control" id="minimum_qty" name="minimum_qty" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <input type="number" step='0.001' class="form-control" id="rate" name="rate" required>
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
            $('#sparePartsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('parts.data') }}',
                columns: [
                    {data: 'id', name: 'id' },
                    {data: 'name'},
                    {data: 'type'},
                    {data: 'size'},
                    {data: 'weight'},
                    {data: 'unit'},
                    {data: 'qty'},
                    {data: 'minimum_qty'},
                    {data: 'rate'},
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

        function openEditModal(id) {
            // Fetch the spare part data using AJAX
            $.ajax({
                url: `/parts/${id}/edit`,
                method: 'GET',
                success: function(data) {
                    // Populate the form fields with the fetched data
                    $('#editSparePartId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editType').val(data.type);
                    $('#editSize').val(data.size);
                    $('#editWeight').val(data.weight);
                    $('#editUnit').val(data.unit);
                    $('#editQuantity').val(data.qty);
                    $('#editMinimumQuantity').val(data.minimum_qty);
                    $('#editRate').val(data.rate);
                    
                    // Open the modal
                    $('#editSparePartModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching spare part data:', error);
                }
            });
        }

        // Handle form submission
        $('#editSparePartForm').submit(function(event) {
            event.preventDefault();

            var id = $('#editSparePartId').val();

            // Send the updated data via AJAX
            $.ajax({
                url: `/parts/${id}`,
                method: 'PUT',
                data: {
                    name: $('#editName').val(),
                    type: $('#editType').val(),
                    size: $('#editSize').val(),
                    weight: $('#editWeight').val(),
                    unit: $('#editUnit').val(),
                    qty: $('#editQuantity').val(),
                    rate: $('#editRate').val(),
                    minimum_qty: $('#editMinimumQuantity').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    // Close the modal
                    $('#editSparePartModal').modal('hide');

                    // Refresh DataTable
                    $('#sparePartsTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error updating spare part data:', error);
                }
            });
        });

        // Handle AJAX delete
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this spare part?')) {
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

                    // Refresh DataTable
                    $('#sparePartsTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting spare part:', error);
                }
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
    </script>
@endsection
