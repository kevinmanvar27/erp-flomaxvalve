<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Total Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            padding: 15px 20px;
        }
        
        /* Company Header */
        .company-header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .logo-container {
            margin-bottom: 10px;
        }
        .logo-container img {
            /* max-height: 100px; */
            max-width: 200px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .report-title {
            font-size: 16px;
            color: #7f8c8d;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-date {
            font-size: 9px;
            color: #95a5a6;
            margin-top: 5px;
        }
        
        /* Client Section */
        .client-section {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            background-color: #3498db;
            color: white;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .client-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        .client-name {
            font-size: 18px;
            font-weight: bold;
            margin-top: 3px;
        }
        .client-business {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 2px;
        }
        
        /* Filter Info */
        .filter-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        .filter-title {
            font-size: 10px;
            font-weight: bold;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .filter-item-label {
            font-size: 8px;
            color: #6c757d;
            text-transform: uppercase;
        }
        .filter-item-value {
            font-size: 10px;
            color: #212529;
            font-weight: 600;
        }
        
        /* Summary Cards */
        .summary-section {
            margin-bottom: 15px;
        }
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .summary-table td {
            border: none;
            padding: 0;
        }
        .summary-card {
            text-align: center;
            padding: 12px 8px;
            border-radius: 6px;
            color: white;
        }
        .summary-card .value {
            font-size: 13px;
            font-weight: bold;
            display: block;
        }
        .summary-card .label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-top: 3px;
            display: block;
        }
        .card-invoices { background-color: #17a2b8; }
        .card-subtotal { background-color: #6c757d; }
        .card-gst { background-color: #ffc107; color: #333; }
        .card-total { background-color: #007bff; }
        .card-received { background-color: #28a745; }
        .card-pending { background-color: #dc3545; }
        
        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        .data-table thead tr {
            background-color: #2c3e50;
            color: white;
        }
        .data-table th {
            padding: 10px 6px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 8px;
            border: none;
        }
        .data-table th.text-right,
        .data-table td.text-right {
            text-align: right;
        }
        .data-table th.text-center,
        .data-table td.text-center {
            text-align: center;
        }
        .data-table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .data-table td {
            padding: 8px 6px;
            vertical-align: middle;
        }
        .data-table tfoot tr {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .data-table tfoot td {
            padding: 10px 6px;
            border-top: 2px solid #2c3e50;
        }
        
        /* Amount Styling */
        .amount-received {
            color: #28a745;
            font-weight: 600;
        }
        .amount-pending {
            color: #dc3545;
            font-weight: 600;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-partial {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .status-unpaid {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }
        .footer-text {
            font-size: 8px;
            color: #6c757d;
        }
        .footer-note {
            font-size: 7px;
            color: #adb5bd;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="logo-container">
                <img src="{{ public_path('logos/flowmax.png') }}" alt="Flomax Valve Logo">
            </div>
            <!-- <div class="company-name">Flomax Valve</div> -->
            <div class="report-title">Total Sales Report</div>
            <div class="report-date">Generated on: {{ date('d F Y, h:i A') }}</div>
        </div>

        <!-- Client Section (if filtered by client) -->
        @if($filterClient)
        <div class="client-section">
            <div class="client-label">Sales Report For</div>
            <div class="client-name">{{ $filterClient->name }}</div>
            @if($filterClient->business_name)
            <div class="client-business">{{ $filterClient->business_name }}</div>
            @endif
        </div>
        @endif

        <!-- Filter Information -->
        @if($dateFrom || $dateTo || $paymentStatus)
        <div class="filter-section">
            <div class="filter-title">Applied Filters</div>
            <table style="width: 100%; border: none;">
                <tr>
                    @if($dateFrom)
                    <td style="width: 33%; padding: 5px 0; border: none;">
                        <div class="filter-item-label">From Date</div>
                        <div class="filter-item-value">{{ date('d M Y', strtotime($dateFrom)) }}</div>
                    </td>
                    @endif
                    @if($dateTo)
                    <td style="width: 33%; padding: 5px 0; border: none;">
                        <div class="filter-item-label">To Date</div>
                        <div class="filter-item-value">{{ date('d M Y', strtotime($dateTo)) }}</div>
                    </td>
                    @endif
                    @if($paymentStatus)
                    <td style="width: 33%; padding: 5px 0; border: none;">
                        <div class="filter-item-label">Payment Status</div>
                        <div class="filter-item-value">{{ ucfirst($paymentStatus) }}</div>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="summary-section">
            <table class="summary-table" style="border: none;">
                <tr>
                    <td style="width: 16.66%; border: none;">
                        <div class="summary-card card-invoices">
                            <span class="value">{{ $invoices->count() }}</span>
                            <span class="label">Total Invoices</span>
                        </div>
                    </td>
                    <td style="width: 16.66%; border: none;">
                        <div class="summary-card card-subtotal">
                            <span class="value">Rs. {{ number_format($totalSubTotal, 2) }}</span>
                            <span class="label">Sub Total</span>
                        </div>
                    </td>
                    <td style="width: 16.66%; border: none;">
                        <div class="summary-card card-gst">
                            <span class="value">Rs. {{ number_format($totalGst, 2) }}</span>
                            <span class="label">GST Amount</span>
                        </div>
                    </td>
                    <td style="width: 16.66%; border: none;">
                        <div class="summary-card card-total">
                            <span class="value">Rs. {{ number_format($totalAmount, 2) }}</span>
                            <span class="label">Total Sales</span>
                        </div>
                    </td>
                    <td style="width: 16.66%; border: none;">
                        <div class="summary-card card-received">
                            <span class="value">Rs. {{ number_format($totalReceived, 2) }}</span>
                            <span class="label">Received</span>
                        </div>
                    </td>
                    <td style="width: 16.66%; border: none;">
                        <div class="summary-card card-pending">
                            <span class="value">Rs. {{ number_format($totalPending, 2) }}</span>
                            <span class="label">Pending</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 4%;">SL</th>
                    <th style="width: 8%;">Date</th>
                    <th style="width: 12%;">Invoice No.</th>
                    @if(!$filterClient)
                    <th style="width: 12%;">Client Name</th>
                    @endif
                    <th class="text-right" style="width: 9%;">Sub Total</th>
                    <th class="text-right" style="width: 8%;">GST</th>
                    <th class="text-right" style="width: 9%;">Total</th>
                    <th class="text-right" style="width: 9%;">Received</th>
                    <th class="text-right" style="width: 9%;">Pending</th>
                    <th style="width: 8%;">Pay Date</th>
                    <th class="text-center" style="width: 6%;">User</th>
                    <th class="text-center" style="width: 6%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $index => $invoice)
                    @php
                        $receivedAmount = $invoice->received_amount ?? 0;
                        $pendingAmount = $invoice->balance - $receivedAmount;
                        $gstAmount = $invoice->balance - $invoice->sub_total;
                        
                        $statusClass = 'status-unpaid';
                        $statusText = 'Unpaid';
                        if ($pendingAmount <= 0) {
                            $statusClass = 'status-paid';
                            $statusText = 'Paid';
                        } elseif ($receivedAmount > 0) {
                            $statusClass = 'status-partial';
                            $statusText = 'Partial';
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($invoice->create_date)) }}</td>
                        <td>{{ $invoice->invoice }}</td>
                        @if(!$filterClient)
                        <td>{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
                        @endif
                        <td class="text-right">Rs. {{ number_format($invoice->sub_total, 2) }}</td>
                        <td class="text-right">Rs. {{ number_format($gstAmount, 2) }}</td>
                        <td class="text-right">Rs. {{ number_format($invoice->balance, 2) }}</td>
                        <td class="text-right amount-received">Rs. {{ number_format($receivedAmount, 2) }}</td>
                        <td class="text-right amount-pending">Rs. {{ number_format($pendingAmount, 2) }}</td>
                        <td>{{ $invoice->payment_date ? date('d-m-Y', strtotime($invoice->payment_date)) : '-' }}</td>
                        <td class="text-center">{{ $invoice->payment_user_code ?? '-' }}</td>
                        <td class="text-center"><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $filterClient ? 3 : 4 }}" class="text-right"><strong>Grand Total:</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalSubTotal, 2) }}</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalGst, 2) }}</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalAmount, 2) }}</strong></td>
                    <td class="text-right amount-received"><strong>Rs. {{ number_format($totalReceived, 2) }}</strong></td>
                    <td class="text-right amount-pending"><strong>Rs. {{ number_format($totalPending, 2) }}</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                Total Records: {{ $invoices->count() }} | Report Period: {{ $dateFrom ? date('d M Y', strtotime($dateFrom)) : 'All Time' }} - {{ $dateTo ? date('d M Y', strtotime($dateTo)) : 'Present' }}
            </div>
            <div class="footer-note">
                This is a computer-generated document. No signature is required.
            </div>
        </div>
    </div>
</body>
</html>
