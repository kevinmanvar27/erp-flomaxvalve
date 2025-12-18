@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('rejection.createCustomerRejection') }}">
                                <button type="button" class="btn btn-success">
                                    Add Customer Rejection
                                </button>
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Rejection</li>
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
                                <h3 class="card-title">Rejection Products</h3>
                            </div>
                            <div class="card-body">
                                
                                <!-- Date Range Filter -->
                                
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <input type="date" id="from_date" class="form-control" placeholder="From Date">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" id="to_date" class="form-control" placeholder="To Date">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="filter" class="btn btn-primary">Apply Filter</button>
                                    </div>
                                </div>
                                
                                <table id="garbage-table" class="display">
                                    <thead>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>Product Name</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Date</th> 
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>Product Name</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
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

    <!-- Modal to Show Garbage Spare Parts -->
    <div class="modal fade" id="garbageModal" tabindex="-1" role="dialog" aria-labelledby="garbageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="garbageModalLabel">Customer Rejection Spare Parts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Spare Part Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Weight</th>
                                <th>Rejected Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="spare-parts-body">
                            <!-- Spare parts details will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#garbage-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('rejection.customerGetData') }}',
                    data: function(d) {
                        d.from_date = $('#from_date').val();  // Passing 'from_date' to the server
                        d.to_date = $('#to_date').val();  // Passing 'to_date' to the server
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'product_name', name: 'products.name' }, // Update to show product_name
                    { data: 'type', name: 'type' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'date', name: 'created_at' },
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
    
            // Apply filter button click
            $('#filter').click(function() {
                table.draw();  // Redraw table based on new filters
            });
            
            
            // View button click event
            $('#garbage-table').on('click', '.view-garbage', function() {
                var garbageId = $(this).data('id');
                $.ajax({
                    url: '{{ route('rejection.spare_parts') }}',
                    method: 'GET',
                    data: { id: garbageId },
                    success: function(response) {
                        var sparePartsHtml = '';
                        $.each(response.spare_parts, function(index, sparePart) {
                            sparePartsHtml += '<tr>' +
                                '<td>' + sparePart.name + '</td>' + // Update to show product_name
                                '<td>' + sparePart.type + '</td>' +
                                '<td>' + sparePart.size + '</td>' +
                                '<td>' + sparePart.weight + '</td>' +
                                '<td>' + sparePart.quantity + '</td>' +
                                '</tr>';
                        });
                        $('#spare-parts-body').html(sparePartsHtml);
                        $('#garbageModal').modal('show');
                    }
                });
            });
            
        });
    </script>

    
@endsection
