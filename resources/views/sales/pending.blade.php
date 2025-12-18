@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pending Sales Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                            <li class="breadcrumb-item active">Pending Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Filter Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Filter by Client</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_filter">Select Client</label>
                                            <select class="custom-select select2" id="client_filter" style="width: 100%;">
                                                <option value="">All Clients</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->name }} {{ $client->business_name ? '(' . $client->business_name . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_from">From Date</label>
                                            <input type="date" class="form-control" id="date_from" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_to">To Date</label>
                                            <input type="date" class="form-control" id="date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="applyFilter">
                                                <i class="fas fa-filter"></i> Apply Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="total_invoices">0</h3>
                                <p>Total Pending Invoices</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>₹<span id="total_amount">0.00</span></h3>
                                <p>Total Invoice Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>₹<span id="total_received">0.00</span></h3>
                                <p>Total Received Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>₹<span id="grand_total_pending">0.00</span></h3>
                                <p>Grand Total Pending</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Sales Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Pending Sales Details</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm" id="exportExcel">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" id="exportPdf">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="pending-sales-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>Invoice Date</th>
                                            <th>Invoice Number</th>
                                            <th>Client Name</th>
                                            <th>Total Amount</th>
                                            <th>Received Amount</th>
                                            <th>Pending Amount</th>
                                            <th>Days Overdue</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light font-weight-bold">
                                            <th colspan="4" class="text-right">Grand Total:</th>
                                            <th id="footer_total_amount">₹0.00</th>
                                            <th id="footer_received_amount">₹0.00</th>
                                            <th id="footer_pending_amount">₹0.00</th>
                                            <th colspan="2"></th>
                                        </tr>
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
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₹</span>
                                            </div>
                                            <input type="text" class="form-control" id="modal_total_amount" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Already Received</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₹</span>
                                            </div>
                                            <input type="text" class="form-control text-success" id="modal_received_amount" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pending Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₹</span>
                                            </div>
                                            <input type="text" class="form-control text-danger font-weight-bold" id="modal_pending_amount" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="receive_amount">Amount to Receive <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" class="form-control" id="receive_amount" name="amount" step="0.01" min="0.01" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-success" id="receiveFullAmount" title="Receive Full Pending Amount">
                                            <i class="fas fa-check-double"></i> Full
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Enter the amount you want to receive or click "Full" to receive the entire pending amount</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="submitReceiveAmount"><i class="fas fa-hand-holding-usd"></i> Receive Amount</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Helper function to show alerts
            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                
                // Remove existing alerts
                $('.custom-alert').remove();
                
                var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show custom-alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                    '<i class="fas ' + iconClass + ' mr-2"></i>' + message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';
                
                $('body').append(alertHtml);
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('.custom-alert').fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({
                    theme: 'bootstrap4'
                });

                // Initialize DataTable
                var table = $('#pending-sales-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('sales.pending.data') }}',
                        data: function(d) {
                            d.client_id = $('#client_filter').val();
                            d.date_from = $('#date_from').val();
                            d.date_to = $('#date_to').val();
                        }
                    },
                    columns: [
                        { data: 'sl_no', name: 'sl_no', orderable: false, searchable: false },
                        { data: 'create_date', name: 'create_date' },
                        { data: 'invoice', name: 'invoice' },
                        { data: 'customer_name', name: 'customer_name' },
                        { data: 'total_amount', name: 'sub_total' },
                        { data: 'received_amount', name: 'received_amount' },
                        { data: 'pending_amount', name: 'pending_amount', orderable: false },
                        { data: 'days_overdue', name: 'days_overdue', orderable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    order: [[1, 'desc']],
                    paging: true,
                    lengthChange: true,
                    pageLength: 25,
                    searching: true,
                    info: true,
                    autoWidth: false,
                    drawCallback: function(settings) {
                        // Update summary after table draw
                        updateSummary();
                    }
                });

                // Apply Filter
                $('#applyFilter').on('click', function() {
                    table.ajax.reload();
                    updateSummary();
                });

                // Update summary cards
                function updateSummary() {
                    $.ajax({
                        url: '{{ route('sales.pending.summary') }}',
                        data: {
                            client_id: $('#client_filter').val(),
                            date_from: $('#date_from').val(),
                            date_to: $('#date_to').val()
                        },
                        success: function(response) {
                            $('#total_invoices').text(response.total_invoices);
                            $('#total_amount').text(formatNumber(response.total_amount));
                            $('#total_received').text(formatNumber(response.total_received));
                            $('#grand_total_pending').text(formatNumber(response.total_pending));
                            
                            // Update footer totals
                            $('#footer_total_amount').text('₹' + formatNumber(response.total_amount));
                            $('#footer_received_amount').text('₹' + formatNumber(response.total_received));
                            $('#footer_pending_amount').text('₹' + formatNumber(response.total_pending));
                        }
                    });
                }

                // Format number with commas
                function formatNumber(num) {
                    return parseFloat(num).toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                // Initial summary load
                updateSummary();

                // Handle receive amount button click
                $(document).on('click', '.btn-receive-amount', function() {
                    var invoiceId = $(this).data('id');
                    var invoiceNumber = $(this).data('invoice');
                    var totalAmount = $(this).data('total');
                    var receivedAmount = $(this).data('received');
                    var pendingAmount = $(this).data('pending');

                    $('#invoice_id').val(invoiceId);
                    $('#modal_invoice_number').val(invoiceNumber);
                    $('#modal_total_amount').val(parseFloat(totalAmount).toFixed(2));
                    $('#modal_received_amount').val(parseFloat(receivedAmount).toFixed(2));
                    $('#modal_pending_amount').val(parseFloat(pendingAmount).toFixed(2));
                    $('#receive_amount').val('');
                    $('#receive_amount').attr('max', pendingAmount);

                    $('#receiveAmountModal').modal('show');
                });

                // Handle submit receive amount
                $('#submitReceiveAmount').on('click', function() {
                    var invoiceId = $('#invoice_id').val();
                    var amount = $('#receive_amount').val();
                    var pendingAmount = parseFloat($('#modal_pending_amount').val());

                    if (!amount || parseFloat(amount) <= 0) {
                        showAlert('error', 'Please enter a valid amount.');
                        return;
                    }

                    if (parseFloat(amount) > pendingAmount) {
                        showAlert('error', 'Amount cannot exceed pending amount of ₹' + pendingAmount.toFixed(2));
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                    $.ajax({
                        url: '{{ url("sales") }}/' + invoiceId + '/receive-amount',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            amount: amount
                        },
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message || 'Amount received successfully.');
                                $('#receiveAmountModal').modal('hide');
                                // Reload table data without resetting pagination
                                table.ajax.reload(null, false);
                                // Update summary cards
                                updateSummary();
                            } else {
                                showAlert('error', response.message || 'Failed to receive amount.');
                            }
                        },
                        error: function(xhr) {
                            var message = 'An error occurred.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                message = Object.values(errors).flat().join(', ');
                            }
                            showAlert('error', message);
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html('<i class="fas fa-hand-holding-usd"></i> Receive Amount');
                        }
                    });
                });

                // Handle "Receive Full Amount" button
                $('#receiveFullAmount').on('click', function() {
                    var pendingAmount = parseFloat($('#modal_pending_amount').val());
                    if (pendingAmount > 0) {
                        $('#receive_amount').val(pendingAmount.toFixed(2));
                    }
                });

                // Reset form when modal is closed
                $('#receiveAmountModal').on('hidden.bs.modal', function() {
                    $('#receiveAmountForm')[0].reset();
                });

                // Export functionality (basic - can be enhanced)
                $('#exportExcel').on('click', function() {
                    var params = $.param({
                        client_id: $('#client_filter').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        export: 'excel'
                    });
                    window.location.href = '{{ route('sales.pending.export') }}?' + params;
                });

                $('#exportPdf').on('click', function() {
                    var params = $.param({
                        client_id: $('#client_filter').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        export: 'pdf'
                    });
                    window.location.href = '{{ route('sales.pending.export') }}?' + params;
                });
            });
        </script>
    </div>
@endsection
