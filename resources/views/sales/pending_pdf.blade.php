<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pending Sales Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filter-info {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filter-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4a90d9;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
        .pending-amount {
            color: #dc3545;
            font-weight: bold;
        }
        .summary-box {
            display: inline-block;
            width: 23%;
            margin: 0 1%;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            color: white;
        }
        .summary-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .bg-info { background-color: #17a2b8; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #333; }
        .bg-danger { background-color: #dc3545; }
        .summary-box h3 {
            margin: 0;
            font-size: 18px;
        }
        .summary-box p {
            margin: 5px 0 0;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pending Sales Report</h1>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    @if($filterClient || $dateFrom || $dateTo)
    <div class="filter-info">
        <strong>Applied Filters:</strong>
        @if($filterClient)
            <p>Client: {{ $filterClient->name }} {{ $filterClient->business_name ? '(' . $filterClient->business_name . ')' : '' }}</p>
        @endif
        @if($dateFrom)
            <p>From Date: {{ $dateFrom }}</p>
        @endif
        @if($dateTo)
            <p>To Date: {{ $dateTo }}</p>
        @endif
    </div>
    @endif

    <div class="summary-container">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 5px; width: 25%;">
                    <div style="background: #17a2b8; color: white; padding: 10px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0;">{{ $invoices->count() }}</h3>
                        <p style="margin: 5px 0 0; font-size: 10px;">Total Invoices</p>
                    </div>
                </td>
                <td style="border: none; padding: 5px; width: 25%;">
                    <div style="background: #28a745; color: white; padding: 10px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0;">Rs. {{ number_format($totalAmount, 2) }}</h3>
                        <p style="margin: 5px 0 0; font-size: 10px;">Total Amount</p>
                    </div>
                </td>
                <td style="border: none; padding: 5px; width: 25%;">
                    <div style="background: #ffc107; color: #333; padding: 10px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0;">Rs. {{ number_format($totalReceived, 2) }}</h3>
                        <p style="margin: 5px 0 0; font-size: 10px;">Received Amount</p>
                    </div>
                </td>
                <td style="border: none; padding: 5px; width: 25%;">
                    <div style="background: #dc3545; color: white; padding: 10px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0;">Rs. {{ number_format($totalPending, 2) }}</h3>
                        <p style="margin: 5px 0 0; font-size: 10px;">Pending Amount</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">SL</th>
                <th style="width: 12%;">Invoice Date</th>
                <th style="width: 15%;">Invoice No.</th>
                <th style="width: 20%;">Client Name</th>
                <th class="text-right" style="width: 14%;">Total Amount</th>
                <th class="text-right" style="width: 14%;">Received</th>
                <th class="text-right" style="width: 14%;">Pending</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $invoice)
                @php
                    $receivedAmount = $invoice->received_amount ?? 0;
                    $pendingAmount = $invoice->sub_total - $receivedAmount;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $invoice->create_date }}</td>
                    <td>{{ $invoice->invoice }}</td>
                    <td>{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
                    <td class="text-right">Rs. {{ number_format($invoice->sub_total, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($receivedAmount, 2) }}</td>
                    <td class="text-right pending-amount">Rs. {{ number_format($pendingAmount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right"><strong>Rs. {{ number_format($totalAmount, 2) }}</strong></td>
                <td class="text-right"><strong>Rs. {{ number_format($totalReceived, 2) }}</strong></td>
                <td class="text-right pending-amount"><strong>Rs. {{ number_format($totalPending, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>
