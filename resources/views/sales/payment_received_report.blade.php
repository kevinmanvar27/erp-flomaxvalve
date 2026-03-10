@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Payment Received Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                            <li class="breadcrumb-item active">Payment Received Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Info Alert -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5><i class="icon fas fa-info-circle"></i> About This Report</h5>
                            This report shows payments based on the <strong>month they were received</strong>, not the invoice date. 
                            Use the date filters to select the payment received date range.
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_filter">Select Client</label>
                                            <select class="custom-select select2" id="client_filter" style="width: 100%;">
                                                <option value="">All Clients</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">
                                                        {{ $client->name }} {{ $client->business_name ? '(' . $client->business_name . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_from">Payment From Date</label>
                                            <input type="date" class="form-control" id="date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_to">Payment To Date</label>
                                            <input type="date" class="form-control" id="date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="btn-group btn-block">
                                                <button type="button" class="btn btn-primary" id="applyFilter">
                                                    <i class="fas fa-filter"></i> Apply
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="resetFilter">
                                                    <i class="fas fa-undo"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="total_payments">0</h3>
                                <p>Total Payments</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>₹<span id="total_sub_total">0.00</span></h3>
                                <p>Sub Total</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>₹<span id="total_gst">0.00</span></h3>
                                <p>GST Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percent"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>₹<span id="total_invoice_amount">0.00</span></h3>
                                <p>Total Invoice Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>₹<span id="total_received">0.00</span></h3>
                                <p>Total Received Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Received Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-table"></i> Payment Details</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm" id="exportExcel">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="payment-received-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No.</th>
                                            <th>Payment Date</th>
                                            <th>Invoice Date</th>
                                            <th>Invoice Number</th>
                                            <th>Client Name</th>
                                            <th>GST Number</th>
                                            <th>Sub Total</th>
                                            <th>GST Amount</th>
                                            <th>Total Amount</th>
                                            <th>Received</th>
                                            <th>Pending</th>
                                            <th>Payment User</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light font-weight-bold">
                                            <th colspan="6" class="text-right">Grand Total:</th>
                                            <th id="footer_sub_total">₹0.00</th>
                                            <th id="footer_gst">₹0.00</th>
                                            <th id="footer_total">₹0.00</th>
                                            <th id="footer_received">₹0.00</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
                    theme: 'bootstrap4',
                    allowClear: true,
                    placeholder: 'Select Client'
                });

                // Initialize DataTable
                var table = $('#payment-received-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('sales.paymentReceived.data') }}',
                        data: function(d) {
                            d.client_id = $('#client_filter').val();
                            d.date_from = $('#date_from').val();
                            d.date_to = $('#date_to').val();
                        }
                    },
                    columns: [
                        { data: 'sl_no', name: 'sl_no', orderable: false, searchable: false },
                        { data: 'payment_date', name: 'payment_date' },
                        { data: 'invoice_date', name: 'invoice_date' },
                        { data: 'invoice', name: 'invoice' },
                        { data: 'customer_name', name: 'customer_name' },
                        { data: 'gst_number', name: 'gst_number', orderable: false },
                        { data: 'sub_total', name: 'sub_total' },
                        { data: 'gst_amount', name: 'gst_amount', orderable: false },
                        { data: 'total_amount', name: 'balance' },
                        { data: 'received_amount', name: 'received_amount' },
                        { data: 'pending_amount', name: 'pending_amount', orderable: false },
                        { data: 'payment_user_code', name: 'payment_user_code' },
                        { data: 'status', name: 'status', orderable: false, searchable: false }
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

                // Reset Filter
                $('#resetFilter').on('click', function() {
                    $('#client_filter').val('').trigger('change');
                    $('#date_from').val('');
                    $('#date_to').val('');
                    table.ajax.reload();
                    updateSummary();
                });

                // Update summary cards
                function updateSummary() {
                    $.ajax({
                        url: '{{ route('sales.paymentReceived.summary') }}',
                        data: {
                            client_id: $('#client_filter').val(),
                            date_from: $('#date_from').val(),
                            date_to: $('#date_to').val()
                        },
                        success: function(response) {
                            $('#total_payments').text(response.total_payments);
                            $('#total_sub_total').text(formatNumber(response.total_sub_total));
                            $('#total_gst').text(formatNumber(response.total_gst));
                            $('#total_invoice_amount').text(formatNumber(response.total_invoice_amount));
                            $('#total_received').text(formatNumber(response.total_received));
                            
                            // Update footer totals
                            $('#footer_sub_total').text('₹' + formatNumber(response.total_sub_total));
                            $('#footer_gst').text('₹' + formatNumber(response.total_gst));
                            $('#footer_total').text('₹' + formatNumber(response.total_invoice_amount));
                            $('#footer_received').text('₹' + formatNumber(response.total_received));
                        }
                    });
                }

                // Format number with commas (Indian format)
                function formatNumber(num) {
                    return parseFloat(num).toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                // Initial summary load
                updateSummary();

                // Export Excel
                $('#exportExcel').on('click', function() {
                    var params = $.param({
                        client_id: $('#client_filter').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val()
                    });
                    window.location.href = '{{ route('sales.paymentReceived.export') }}?' + params;
                });
            });
        </script>
    </div>
@endsection
