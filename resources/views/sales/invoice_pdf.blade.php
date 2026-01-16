<?php 

function numberToWords($number) {
    $words = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    );

    if ($number < 21) {
        return $words[$number];
    } else if ($number < 100) {
        return $words[($number - $number % 10)] . (($number % 10) ? ' ' . $words[$number % 10] : '');
    } else if ($number < 1000) {
        return $words[intval($number / 100)] . ' Hundred' . (($number % 100) ? ' ' . numberToWords($number % 100) : '');
    } else if ($number < 100000) {
        return numberToWords(intval($number / 1000)) . ' Thousand' . (($number % 1000) ? ' ' . numberToWords($number % 1000) : '');
    } else if ($number < 10000000) {
        return numberToWords(intval($number / 100000)) . ' Lakh' . (($number % 100000) ? ' ' . numberToWords($number % 100000) : '');
    } else if ($number < 1000000000) {
        return numberToWords(intval($number / 10000000)) . ' Crore' . (($number % 10000000) ? ' ' . numberToWords($number % 10000000) : '');
    } else {
        return numberToWords(intval($number / 1000000000)) . ' Arab' . (($number % 1000000000) ? ' ' . numberToWords($number % 1000000000) : '');
    }
    return '';
}

function numberToWordsWithCents($amount) {
    $number = intval($amount);
    $cents = round(($amount - $number) * 100);
    
    $numberWords = numberToWords($number);
    $centsWords = numberToWords($cents);

    if ($cents > 0) {
        return $numberWords . ' and ' . $centsWords . ' Cents';
    } else {
        return $numberWords;
    }
}

?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>FSV04-24-25.xlsx</title>
    <meta name="author" content="Flomax Valve" />
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0px;
            background-color: #fff;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            /* border: 2px solid black; */
            position: relative;
        }

        .s1 {
            color: #79C44F;
            font-family: Verdana, sans-serif;
            font-style: italic;
            font-weight: normal;
            text-decoration: none;
            font-size: 10.5pt;
        }

        .s2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 7pt;
        }

        .s3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8pt;
            margin: 5px;
        }

        .s4 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 7pt;
        }

        .s5 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 9pt;
        }

        .s6 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7pt;
        }

        .s7 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 6.5pt;
        }

        .s8 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8.5pt;
        }

        .s9 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8pt;
        }

        .s10 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 6.5pt;
            vertical-align: 1pt;
        }

        .s11 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 11.5pt;
        }

        .s12 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: bold;
            text-decoration: none;
            font-size: 7pt;
        }

        .s13 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: normal;
            text-decoration: none;
            font-size: 7pt;
        }

        li {
            display: block;
        }

        #l1 {
            padding-left: 0pt;
            counter-reset: c1 1;
        }

        #l1>li>*:first-child:before {
            counter-increment: c1;
            content: counter(c1, decimal)". ";
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: bold;
            text-decoration: none;
            font-size: 7pt;
        }

        #l1>li:first-child>*:first-child:before {
            counter-increment: c1 0;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }

        #mainTable {
            border-collapse: collapse;
            width: 100%;
        }

        .header {
            overflow: hidden;
            padding-bottom: 10px;
            border: 2px solid black;
            border-bottom: 0px;
        }

       
        


        #product-details-table {
            width: 100%;
            border-collapse: collapse;
            margin: -1px auto;
            max-width: 1000px;
            border:2px solid black;
            font-size: 7pt;
            font-weight: 700;
            border-top: 0px solid white;
        }

        #product-details-table th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        #product-details-table th {
            border: 2px solid black;
            text-align: center;
            font-weight: bold;
            font-size: 7pt;
            border-top: 0px;
        }

        #product-details-table .right-align {
            text-align: right;
        }

        #product-details-table .center-align {
            text-align: center;
        }

        #product-details-table .no-border {
            border: none;
        }

        #product-details-table .total-row td {
            font-weight: bold;
        }

        /* Styling for Footer */
        #product-details-table .footer {
            max-width: 1000px;
            margin: 0 auto;
            border-top: 1px solid black;
            padding: 10px 0;
            text-align: center;
        }

        #product-details-table .footer p {
            margin: 5px 0;
        }

        /* P & F / Courier Charges Style */
        #product-details-table .courier-row td {
            padding: 10px;
            text-align: right;
        }

        .productRecordsBottomBorderHide{
            border: 0px;
            border-right: 1px solid black;
        }

        .bankDetails td{
            border: 0px;
        }

        .bankDetailsGst td{
            border: 0px;
            padding: 0px;
            padding-bottom: 20px;
        }

        .logo {
            float: left;
            width: 50%;
        }

        .company-details {
            float: left;
            width: 50%; 
            padding-left: 20px;
            margin-top:10px;
        }

        .company-details p {
            font-size: 10px;
            line-height: 1.5;
            font-weight: bold;
            text-align: center;
            margin-right: 45px;
        }

        .company-details h2 {
            font-size: 15px;
            color: rgb(121 197 79);
            margin-bottom: -11px;
            font-weight: bold;
            text-align: center;
            margin-right: 45px;
        }
        .header::after {
            content: "";
            display: table;
            clear: both;
        }
        .header .logo img {
            width: 200px;
            max-width: 200px;
            margin-left: 32px;
            margin-top: 20px;
        }


        @media print {
            td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

    </style>
</head>

<body>
    <?php 
        $subTotal = 0;
        $totalQty = 0;
        $totalGstAmount =0;
    ?>
    <div class="invoice-container">
        <div class="header">
            <div class="logo">
                <img src="{{ $logoPath }}" style="width: 200px; margin-left: 32px; margin-top: 20px;" />
            </div>
            <div class="company-details">
                <h2><i><b style="text-transform:uppercase;">{{$settings->company_name}}</b></i></h2>
                <p> 
                    {{$settings->address}}, <br> {{$settings->city}}, {{$settings->state}}, {{$settings->pin_code}}.
                    <br>
                    Cell No. {{$settings->mobile_number}}
                </p>
            </div>
        </div>
        <table id="mainTable" style="border-bottom: 0px !important;">
            <tr>
                <td style="padding:2px;width:170pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt; border-right: 0px;"
                    colspan="2" bgcolor="#D8D8D8">
                    <p class="s3" style="padding-left: 52pt;text-indent: 0pt;text-align: left;">Debit Memo
                    </p>
                </td>
                
                <td style="border-left: 0px;border-right: 0px;padding:2px;width:150pt;border-top-style:solid;border-top-width:2pt;border-bottom-style:solid;border-bottom-width:2pt"
                    colspan="2" bgcolor="#D8D8D8">
                    <p class="s3" style="padding-left: 64pt;text-indent: 0pt;text-align: left;">TAX INVOICE
                    </p>
                </td>

                <td style="border-left: 0px;padding:2px;width:200pt;border-top-style:solid;border-top-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="4" bgcolor="#D8D8D8">
                    <p class="s3" style="padding-left: 106pt;text-indent: 0pt;text-align: left;">Original
                    </p>
                </td>
            </tr>
            <tr style="height:75pt">
                <td style="padding:2px;width:300pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="5">
                    <p class="s4" style="margin-top:0px;margin-bottom:3px;margin-left:0px;text-indent: 0pt;line-height: 7pt;text-align: left;">M/s.:-
                    </p>
                    <p class="s5" style="margin-top:0px;margin-bottom:3px;margin-left:0px;text-indent: 0pt;text-align: left;">{{ $invoice->customer->name }} {{ $invoice->customer->last_name }}</p>
                    <p class="s4"
                        style="margin-top:0px;margin-bottom:3px;margin-left:0px;padding-right: 177pt;text-indent: 0pt;line-height: 112%;text-align: left;">
                        {{ $invoice->address }}
                        <br/>
                    GST No : {{ $invoice->customer->GSTIN }}
                    <br/>
                    State : {{ $invoice->customer->state }}
                    <br/>
                    State Code : {{ $invoice->customer->state_code }}
                    
                        </p>
                    {{-- <p class="s4" style="margin-top:0px;margin-bottom:3px;margin-left:0px;text-indent: 0pt;text-align: left;">GSTIN No. :
                        24AAHFU2045A1ZQ
                    </p>
                    <p class="s4"
                        style="margin-top:0px;margin-bottom:3px;margin-left:0px;text-indent: 0pt;line-height: 112%;text-align: left;">
                        State : Gujarat State Code : 24</p> --}}
                </td>
                <td style="padding:0px;border-bottom: 1px solid white;width:230pt; border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="3">
                    <p class="s6" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;line-height: 164%;text-align: left;">
                        Invoice No. <b>: {{ $invoice->invoice }} </b>
                    </p>
                    
                    
                    <p class="s6" style="padding-left: 1pt;text-indent: 0pt;text-align: left;">    Date <b style="margin-left: 27px;">: {{ \Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y') }}</b>
                    <p class="s6" style="padding-left: 1pt;text-indent: 0pt;text-align: left;">    Due Date <b style="margin-left: 10px;">: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</b>
                    <p class="s6" style="padding-left: 1pt;text-indent: 0pt;text-align: left;">Order No. <b style="margin-left: 5.5px;">: {{ $invoice->orderno }}</b>
                    <p class="s6" style="padding-left: 1pt;text-indent: 0pt;text-align: left;">Transport <b style="margin-left: 7px;">: {{ $invoice->transport }}</b>
                    </p>
                </td>
                
                
                
            </tr>
            <tr style="height:11pt">
                <td style="padding: 0px;width:244pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="3">
                    <p class="s2" style="padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        <!--State: {{isset($invoice->customer->state)?$invoice->customer->state:''}}-->
                        State: {{isset($settings->state)?$settings->state:''}}
                    </p>
                </td>
                <td style="padding: 0px;width:89pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="2">
                    <p class="s2" style="padding-left: 20pt;text-indent: 0pt;text-align: left;">
                        <!--State Code: {{isset($invoice->customer->state_code)?$invoice->customer->state_code:''}}-->
                        State Code: 24
                    </p>
                </td>
                <td style="padding: 0px;width:188pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="3">
                    <?php if( $invoice->lrno != '' ) { ?>
                    <p class="s6" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">L.R. No.
                        <b>:{{ isset($invoice->lrno)?$invoice->lrno:'' }}</b>
                    </p>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <table id="product-details-table">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Product Name</th>
                    <th>HSN/SAC</th>
                    <th>Qty.</th>
                    <th>Rate</th>
                    <th>GST%</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $key =>$item)
                    <?php 
                        $subTotal +=$item->amount; 
                        $totalQty +=$item->quantity;
                        $totalGstAmount += (($item->quantity*$item->price)/100)*($item->invoice->cgst+$item->invoice->sgst+$item->invoice->igst);
                    ?>
                    <tr id="{{$key+1}}">
                        <td class="center-align productRecordsBottomBorderHide">{{$key+1}}</td>
                        <td class="productRecordsBottomBorderHide">{{$item->product->name}}<br/>{{ $item['remark'] }} </td>
                        <td class="center-align productRecordsBottomBorderHide">{{$item->product->hsn_code}}</td>
                        <td class="center-align productRecordsBottomBorderHide">{{$item->quantity}}</td>
                        <td class="center-align productRecordsBottomBorderHide">{{$item->price}}</td>
                        <td class="center-align productRecordsBottomBorderHide">{{$item->invoice->cgst+$item->invoice->sgst+$item->invoice->igst}}</td>
                        <td class="center-align productRecordsBottomBorderHide">{{$item->amount}}</td>
                    </tr>    
                @endforeach
                
                <tr>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                </tr>

                <tr>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                </tr>

                <tr>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                </tr>
                <?php if($invoice->pfcouriercharge > 0) { ?>
                <tr style="margin-top:10px;">
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="productRecordsBottomBorderHide" style="text-align: right;">P & F Charge</td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide">{{$invoice->pfcouriercharge}}%</td>
                    <td class="center-align productRecordsBottomBorderHide">{{number_format(($subTotal*$invoice->pfcouriercharge)/100, 2)}}</td>
                </tr>
                <?php } ?>
                <?php 
                    $grandTotal = $subTotal+(($subTotal*$invoice->pfcouriercharge)/100);
                    
                    // Get courier charges (new fields)
                    $courierBeforeGst = $invoice->courier_charge_before_gst ?? 0;
                    $courierAfterGst = $invoice->courier_charge_after_gst ?? 0;
                    $totalGstRate = ($invoice->cgst + $invoice->sgst + $invoice->igst);
                    
                    // Calculate discount amount
                    $discountAmount = 0;
                    if ($invoice->discount > 0) {
                        if ($invoice->discount_type == 'percentage') {
                            $discountAmount = ($grandTotal * $invoice->discount) / 100;
                        } else {
                            $discountAmount = $invoice->discount;
                        }
                    }
                    // Grand total after discount includes courier_before_gst (GST will be applied on this)
                    $grandTotalAfterDiscount = $grandTotal - $discountAmount + $courierBeforeGst;
                    
                    // Calculate GST on courier_before_gst
                    $courierGstAmount = 0;
                    if ($courierBeforeGst > 0) {
                        $courierGstAmount = ($courierBeforeGst * $totalGstRate) / 100;
                    }
                    $totalCourierWithGst = $courierBeforeGst + $courierGstAmount + $courierAfterGst;
                ?>
                <?php if($invoice->discount > 0) { ?>
                <tr style="margin-top:10px;">
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="productRecordsBottomBorderHide" style="text-align: right;">Discount ({{ $invoice->discount_type == 'percentage' ? $invoice->discount . '%' : 'Flat' }})</td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide">-{{number_format($discountAmount, 2)}}</td>
                </tr>
                <?php } ?>
                <?php if($courierBeforeGst > 0) { ?>
                <tr style="margin-top:10px;">
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="productRecordsBottomBorderHide" style="text-align: right;">Courier (With GST)</td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide"></td>
                    <td class="center-align productRecordsBottomBorderHide">{{number_format($courierBeforeGst, 2)}}</td>
                </tr>
                <?php } ?>
                <tr class="courier-row" style="font-weight: bold;">
                    <td colspan="2" style="text-align: left; background-color: rgb(216, 216, 216);">
                        <!--GSTIN No.: {{isset($invoice->customer->GSTIN)?$invoice->customer->GSTIN:''}}-->
                        GSTIN No.: {{isset($settings->purchase_order_gstin)?$settings->purchase_order_gstin:''}}
                    </td>
                    <td class="center-align">Total Qty:</td>
                    <td class="center-align" style="text-align:center;">{{$totalQty}}</td>
                    <td class="center-align" colspan="2" style="text-align: left;">Sub Total.:</td>
                    <td class="center-align" style="text-align:center;">{{number_format($grandTotalAfterDiscount,2)}}</td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 0px;">
                        <table class="bankDetails">
                            <tr>
                                <td>
                                    Bank Name
                                </td>
                                <td>: {{isset($invoice->customer->bank_name)?$invoice->customer->bank_name:''}}</td>
                            </tr>
                            <tr>
                                <td>
                                    Bank A/c. No.
                                </td>
                                <td>: {{isset($invoice->customer->bank_account_no)?$invoice->customer->bank_account_no:''}}</td>
                            </tr>
                            <tr>
                                <td>
                                    RTGS/IFSC Code
                                </td>
                                <td>: {{isset($invoice->customer->ifsc_code)?$invoice->customer->ifsc_code:''}}</td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="3" style="border-bottom:1px solid white;">
                        <table class="bankDetails">
                            <tr>
                                <td>
                                    
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php 
                    $totalGst = (($grandTotalAfterDiscount*$invoice->sgst)/100)+(($grandTotalAfterDiscount*$invoice->cgst)/100)+(($grandTotalAfterDiscount*$invoice->igst)/100);
                ?>
                <tr>
                    <td colspan="4" style="padding: 0px;">
                        <table class="bankDetailsGst">
                            <tr>
                                <td > Total GST.: <?php echo numberToWordsWithCents($totalGst); ?>   </td>
                            </tr>
                        </table> 
                    </td>
                    <td colspan="3" style="padding: 0px 0px 0px 4px;border: none;">
                        <span>Taxable Amount: {{number_format($grandTotalAfterDiscount, 2)}}</span>
                        
                        
                        <?php if($invoice->cgst != 0 ) {  ?>
                        <table style="width: 100%;">
                            <tr>
                                <td style="border:none !important;padding-left:0px;padding: 0px;  width:90px;">CGST</td>
                                <td style="border:none !important;padding: 0px; width:90px;">{{$invoice->cgst}}%</td>
                                <td style="border:none !important;padding: 0px; text-align: right;">
                                    {{number_format(($grandTotalAfterDiscount*$invoice->cgst)/100,2)}} &nbsp;
                                </td>
                            </tr>
                        </table>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 0px;">
                        <table class="bankDetailsGst">
                            <tr>
                                <td > Bill Amount.: <?php 
                                    $totalGst = $invoice->balance;
                                    $difference = round($totalGst) - $totalGst;
                                echo numberToWordsWithCents(round($totalGst)); ?> </td>
                            </tr>
                        </table> 
                    </td>
                    <td colspan="3" style="padding: 0px 0px 0px 4px;border: none; border-bottom: 1px solid white;">
                        <?php if( $invoice->sgst != 0 ) { ?>
                        <table style="width: 100%; ">
                            <tr>
                                <td style="border:none !important;padding-left:0px;padding: 0px; width:90px;">SGST</td>
                                <td style="border:none !important;padding: 0px; width:90px;">{{$invoice->sgst}}%</td>
                                <td style="border:none !important;padding: 0px; text-align: right;">
                                    {{number_format(($grandTotalAfterDiscount*$invoice->sgst)/100,2)}} &nbsp;
                                </td>                            
                            </tr>
                        </table>
                        <?php } ?>
                        
                        
                        <?php if( $invoice->igst != 0 ) { ?>
                        <table style="width: 100%; ">
                            <tr>
                                <td style="border:none !important;padding-left:0px;padding: 0px; width:90px;">IGST</td>
                                <td style="border:none !important;padding: 0px; width:90px;">{{$invoice->igst}}%</td>
                                <td style="border:none !important;padding: 0px; text-align: right;">
                                    {{number_format(($grandTotalAfterDiscount*$invoice->igst)/100,2)}} &nbsp;
                                </td>                            
                            </tr>
                        </table>
                        <?php } ?>
                        
                        <?php if($courierAfterGst > 0) { ?>
                        <table style="width: 100%; ">
                            <tr>
                                <td style="border:none !important;padding-left:0px;padding: 0px; width:115px;">Courier (Without GST)</td>
                                <td style="border:none !important;padding: 0px; width:90px;"></td>
                                <td style="border:none !important;padding: 0px; text-align: right;">{{number_format($courierAfterGst, 2)}} &nbsp;</td>                            
                            </tr>
                        </table>
                        <?php } ?>

                        <table style="width: 100%;">
                          
                            <tr>
                                <td colspan="2" style="border:none !important;padding:0px;padding-top:4px;  width:90px;">Round Off</td>
                                <td style="border:none !important;padding: 0px;padding-top:4px;text-align: right;">
                                    <?php
                                    // Use saved round_off value if available, otherwise calculate
                                    if (isset($invoice->round_off) && $invoice->round_off != 0) {
                                        $roundOffValue = $invoice->round_off;
                                        echo ($roundOffValue >= 0 ? "+" : "") . number_format($roundOffValue, 2);
                                    } else {
                                        // grandTotalAfterDiscount already includes courierBeforeGst
                                        // Add GST amounts + courier GST + courier after GST
                                        $r_off_total = $grandTotalAfterDiscount + (($grandTotalAfterDiscount*$invoice->cgst)/100) + (($grandTotalAfterDiscount*$invoice->sgst)/100) + (($grandTotalAfterDiscount*$invoice->igst)/100) + $courierAfterGst;
                                        $difference = round($r_off_total) - $r_off_total;
                                        echo ($difference !== 0 ? ($difference > 0 ? "+" : "") . number_format($difference, 2) : "0");
                                    }
                                    ?>&nbsp;
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 0px;">
                        <table class="bankDetailsGst">
                            <tr>
                                <td > Note.: {{isset($invoice->note)?$invoice->note:''}}  </td>
                            </tr>
                        </table> 
                    </td>
                    <td colspan="3" style="padding: 0px;">
                        <table style="width: 100%;border-top:1px solid white; ">
                            <tr>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                            </tr>
                            <tr>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                            </tr>
                            <tr>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                                <td style="border:none !important;padding:0px;padding-top:4px;"></td>
                            </tr>
                         
                        </table>
                        <table style="width: 100%; background-color: rgb(216, 216, 216);border-top: 2px solid;">
                          
                            <tr>
                                <td style="border:none !important;padding: 3px 19px 3px 7px;line-height: 20px;">Grand Total</td>
                                <td style="border:none !important;"></td>
                                <td style="border:none !important; font-size: 18px;text-align: right;">{{ round($totalGst) }} &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
          
        </table>
        <table id="mainTable">
           
            <tr style="height:78pt">
                <td style="padding:2px 0px 0px 3px;width:297pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt"
                    colspan="5">
                    <p class="s2" style="padding-left: 1pt;text-indent: 0pt;text-align: left;">Terms
                        &amp;
                        Condition :</p>
                    <ol id="l1">
                        <li data-list-text="1.">
                            <p class="s12"
                                style="padding-left: 9pt;text-indent: -8pt;text-align: left;">
                                Goods once sold will not be taken back.</p>
                        </li>
                        <li data-list-text="2.">
                            <p class="s12"
                                style="padding-left: 9pt;text-indent: -8pt;text-align: left;">
                                Rate charged will be at the delivery time.</p>
                        </li>
                        <li data-list-text="3.">
                            <p class="s12"
                                style="padding-left: 9pt;padding-right: 122pt;text-indent: -8pt;line-height: 164%;text-align: left;">
                                Payment is requested within 45 days. If not paid in time interest will be charged at 18%
                                P.A.</p>
                        </li>
                        <li data-list-text="4.">
                            <p class="s12"
                                style="padding-left: 9pt;text-indent: -8pt;line-height: 7pt;text-align: left;">
                                Subject to rajkot jurisdiction.</p>
                        </li>
                    </ol>
                </td>
                <!--<td-->
                <!--    style="width:36pt;border-top-style:solid;border-top-width:2pt;border-bottom-style:solid;border-bottom-width:2pt">-->
                <!--    <p style="text-indent: 0pt;text-align: left;"><br /></p>-->
                <!--</td>-->
                <td style="width:188pt;border-top-style:solid;border-top-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
                    colspan="3">
                    <p class="s2"
                        style="padding-top: 3pt;padding-left: 12pt;padding-right: 1pt;text-indent: 0pt;text-align: center;">
                        For, FLOMAX SOLENOID VALVE</p>
                    <p style="text-indent: 0pt;text-align: center;">
                        @if(!empty($signaturePath))
                            <img src="{{ $signaturePath }}" style="width:70%;"/>
                        @endif
                    </p>
                    <p class="s13" style="padding-left: 12pt;text-indent: 0pt;line-height: 8pt;text-align: center;">
                        (Authorised Signatory)</p>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
