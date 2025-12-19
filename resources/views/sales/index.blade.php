@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('sales.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add Sales
                                </button>
                            </a>
                            <a href="{{ route('sales.pending') }}">
                                <button type="button" class="btn btn-warning ml-2">
                                    <i class="fas fa-file-invoice-dollar"></i> Pending Report
                                </button>
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Sales</li>
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
                                <h3 class="card-title">Sales</h3>
                            </div>
                            <div class="card-body">
                                <table id="sales-table" class="display">
                                    <thead>
                                        <th>SL No.</th>
                                        <th>Create Date</th>
                                        <th>Invoice Number</th>
                                        <th>Client Name</th>
                                        <th>Amount</th>
                                        <th>Received Amount</th>
                                        <th>Pending Amount</th>
                                        <th>Total Items</th>
                                        <th>Action</th>
                                    </thead>
                                    <tfoot>
                                        <th>SL No.</th>
                                        <th>Create Date</th>
                                        <th>Invoice Number</th>
                                        <th>Client Name</th>
                                        <th>Amount</th>
                                        <th>Received Amount</th>
                                        <th>Pending Amount</th>
                                        <th>Total Items</th>
                                        <th>Action</th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Receive Amount Modal -->
        <div class="modal fade" id="receiveAmountModal" tabindex="-1" role="dialog" aria-labelledby="receiveAmountModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="receiveAmountModalLabel">Receive Amount</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="receiveAmountForm">
                            <input type="hidden" id="invoice_id" name="invoice_id">
                            
                            <div class="form-group">
                                <label>Invoice Number</label>
                                <input type="text" class="form-control" id="modal_invoice_number" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Total Amount</label>
                                <input type="text" class="form-control" id="modal_total_amount" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Already Received</label>
                                <input type="text" class="form-control" id="modal_received_amount" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Pending Amount</label>
                                <input type="text" class="form-control" id="modal_pending_amount" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="receive_amount">Amount to Receive <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="receive_amount" name="amount" step="0.01" min="0.01" required>
                                <small class="text-muted">Enter the amount you want to receive</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="submitReceiveAmount">Receive Amount</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Products Modal -->
        <div class="modal fade" id="returnProductsModal" tabindex="-1" role="dialog" aria-labelledby="returnProductsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="returnProductsModalLabel">
                            <i class="fas fa-undo"></i> Return Products
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label><strong>Invoice Number:</strong></label>
                                <p id="return_invoice_number" class="text-primary font-weight-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label><strong>Customer:</strong></label>
                                <p id="return_customer_name"></p>
                            </div>
                            <div class="col-md-4">
                                <label><strong>Date:</strong></label>
                                <p id="return_create_date"></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div id="returnProductsLoading" class="text-center py-4" style="display: none;">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Loading products...</p>
                        </div>
                        
                        <form id="returnProductsForm">
                            <input type="hidden" id="return_invoice_id" name="invoice_id">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="returnProductsTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Product</th>
                                            <th>Product Code</th>
                                            <th class="text-center">Sold Qty</th>
                                            <th class="text-center">Already Returned</th>
                                            <th class="text-center">Returnable Qty</th>
                                            <th class="text-center" style="width: 150px;">Return Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="returnProductsTableBody">
                                        <!-- Items will be populated dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" id="submitReturnProducts">
                            <i class="fas fa-undo"></i> Process Return
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#sales-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('sales.data') }}',
                        type: 'GET'
                    },
                    columns: [
                        { data: 'id', name: 'id', orderable: false, searchable: false },
                        { data: 'create_date', name: 'create_date' },
                        { data: 'invoice', name: 'invoice' },
                        { data: 'customer_name', name: 'customer_name' },
                        { data: 'amount', name: 'amount' },
                        { data: 'received_amount', name: 'received_amount' },
                        { data: 'pending_amount', name: 'pending_amount' },
                        { data: 'total_item', name: 'total_item' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    paging: true,
                    lengthChange: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    searching: true,
                    ordering: true,
                    order: [[2, 'desc']], // Default order by invoice number descending
                    info: true,
                    autoWidth: false,
                    responsive: true
                });
            });

            // Handle delete button click
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

            // Handle receive amount button click - open modal
            $(document).on('click', '.btn-receive-amount', function() {
                var invoiceId = $(this).data('id');
                var invoiceNumber = $(this).data('invoice');
                var totalAmount = $(this).data('total');
                var receivedAmount = $(this).data('received');
                var pendingAmount = $(this).data('pending');

                // Populate modal fields
                $('#invoice_id').val(invoiceId);
                $('#modal_invoice_number').val(invoiceNumber);
                $('#modal_total_amount').val(parseFloat(totalAmount).toFixed(2));
                $('#modal_received_amount').val(parseFloat(receivedAmount).toFixed(2));
                $('#modal_pending_amount').val(parseFloat(pendingAmount).toFixed(2));
                $('#receive_amount').val('');
                $('#receive_amount').attr('max', pendingAmount);

                // Show modal
                $('#receiveAmountModal').modal('show');
            });

            // Handle submit receive amount
            $('#submitReceiveAmount').on('click', function() {
                var invoiceId = $('#invoice_id').val();
                var amount = $('#receive_amount').val();
                var pendingAmount = parseFloat($('#modal_pending_amount').val());

                // Validate amount
                if (!amount || parseFloat(amount) <= 0) {
                    alert('Please enter a valid amount.');
                    return;
                }

                if (parseFloat(amount) > pendingAmount) {
                    alert('Amount cannot exceed pending amount of ' + pendingAmount.toFixed(2));
                    return;
                }

                // Disable button to prevent double submission
                $(this).prop('disabled', true).text('Processing...');

                $.ajax({
                    url: '/sales/' + invoiceId + '/receive-amount',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: amount
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#receiveAmountModal').modal('hide');
                            $('#sales-table').DataTable().ajax.reload();
                        } else {
                            alert(response.message || 'Failed to receive amount.');
                        }
                    },
                    error: function(xhr) {
                        var message = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    },
                    complete: function() {
                        $('#submitReceiveAmount').prop('disabled', false).text('Receive Amount');
                    }
                });
            });

            // Reset form when modal is closed
            $('#receiveAmountModal').on('hidden.bs.modal', function() {
                $('#receiveAmountForm')[0].reset();
            });

            // Handle return products button click - open modal
            $(document).on('click', '.btn-return-products', function() {
                var invoiceId = $(this).data('id');
                
                // Show loading and clear previous data
                $('#returnProductsLoading').show();
                $('#returnProductsTableBody').empty();
                $('#return_invoice_id').val(invoiceId);
                $('#return_invoice_number').text('');
                $('#return_customer_name').text('');
                $('#return_create_date').text('');
                
                // Show modal
                $('#returnProductsModal').modal('show');
                
                // Fetch invoice items
                $.ajax({
                    url: '/sales/' + invoiceId + '/items',
                    method: 'GET',
                    success: function(response) {
                        $('#returnProductsLoading').hide();
                        
                        if (response.success) {
                            // Populate invoice info
                            $('#return_invoice_number').text(response.invoice.invoice_number);
                            $('#return_customer_name').text(response.invoice.customer_name);
                            $('#return_create_date').text(response.invoice.create_date);
                            
                            // Populate items table
                            var tbody = $('#returnProductsTableBody');
                            tbody.empty();
                            
                            if (response.items.length === 0) {
                                tbody.append('<tr><td colspan="6" class="text-center">No items found</td></tr>');
                                return;
                            }
                            
                            response.items.forEach(function(item) {
                                var isDisabled = item.returnable_quantity <= 0 ? 'disabled' : '';
                                var qtyClass = item.returnable_quantity > 0 ? 'text-success font-weight-bold' : 'text-muted';
                                
                                var row = '<tr>' +
                                    '<td>' + item.product_name + '</td>' +
                                    '<td>' + item.product_code + '</td>' +
                                    '<td class="text-center">' + item.quantity + '</td>' +
                                    '<td class="text-center">' + item.returned_quantity + '</td>' +
                                    '<td class="text-center"><span class="' + qtyClass + '">' + item.returnable_quantity + '</span></td>' +
                                    '<td>' +
                                        '<input type="hidden" name="returns[' + item.id + '][item_id]" value="' + item.id + '">' +
                                        '<input type="number" class="form-control form-control-sm return-qty-input" ' +
                                               'name="returns[' + item.id + '][return_quantity]" ' +
                                               'min="0" max="' + item.returnable_quantity + '" value="0" ' + isDisabled + '>' +
                                    '</td>' +
                                '</tr>';
                                tbody.append(row);
                            });
                        } else {
                            alert(response.message || 'Failed to fetch items.');
                            $('#returnProductsModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        $('#returnProductsLoading').hide();
                        var message = 'An error occurred while fetching items.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                        $('#returnProductsModal').modal('hide');
                    }
                });
            });

            // Handle submit return products
            $('#submitReturnProducts').on('click', function() {
                var invoiceId = $('#return_invoice_id').val();
                var formData = [];
                var hasValidReturn = false;
                var hasError = false;
                
                // Collect return quantities
                $('#returnProductsTableBody tr').each(function() {
                    var itemIdInput = $(this).find('input[name*="[item_id]"]');
                    var returnQtyInput = $(this).find('input[name*="[return_quantity]"]');
                    
                    if (itemIdInput.length === 0) return; // Skip if no inputs (e.g., "No items found" row)
                    
                    var itemId = itemIdInput.val();
                    var returnQty = parseFloat(returnQtyInput.val()) || 0;
                    var maxQty = parseFloat(returnQtyInput.attr('max')) || 0;
                    
                    if (returnQty > maxQty) {
                        alert('Return quantity cannot exceed returnable quantity (' + maxQty + ').');
                        hasError = true;
                        return false; // Break the loop
                    }
                    
                    if (returnQty > 0) {
                        hasValidReturn = true;
                    }
                    
                    formData.push({
                        item_id: itemId,
                        return_quantity: returnQty
                    });
                });
                
                if (hasError) {
                    return;
                }
                
                if (!hasValidReturn) {
                    alert('Please enter at least one valid return quantity.');
                    return;
                }
                
                // Confirm before processing
                if (!confirm('Are you sure you want to process this return? The quantities will be added back to finished products inventory and invoice amounts will be recalculated.')) {
                    return;
                }
                
                // Disable button to prevent double submission
                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                $.ajax({
                    url: '/sales/' + invoiceId + '/return-products',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        returns: formData
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#returnProductsModal').modal('hide');
                            $('#sales-table').DataTable().ajax.reload();
                        } else {
                            alert(response.message || 'Failed to process return.');
                        }
                    },
                    error: function(xhr) {
                        var message = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-undo"></i> Process Return');
                    }
                });
            });

            // Reset form when return modal is closed
            $('#returnProductsModal').on('hidden.bs.modal', function() {
                $('#returnProductsTableBody').empty();
                $('#return_invoice_id').val('');
            });
        </script>
    </div>
@endsection
