<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
</head>
<?php 
$totalQty = 0;
$totalWeight = 0;
$totalAmount = 0;
?>
<body style="font-family: Arial, sans-serif; margin: 0px; padding: 0px; background-color: #fff;">
    <div class="container" style="width: 100%; padding:0px; border: 2px solid black; position: relative;">
        <div class="header" style="display: flex; flex-direction: column; align-items: center; padding-bottom: 0px; border-bottom: 2px solid #000;">
            <div class="logo" style="text-align: center;">
                <img src="{{$logoBase64}}" alt="Flomax Logo" style="max-width: 60%; margin: 5px auto 0; display: block;">
            </div>
            <div class="company-details" style="text-align: center; margin-top: 10px; width: 100%;">
                <p class="headerAddress" style="font-size: 13px; font-weight: 500;"><b>{{$settings->purchase_order_address}}</b></p>
                <p class="headerAddress" style="font-size: 13px; font-weight: 500;"><b>GSTIN No.: {{$settings->purchase_order_gstin}} | Cell No.: {{$settings->purchase_order_mobile_number}}</b></p>
                <p class="headerAddress" style="font-size: 13px; font-weight: 500;"><b>Email: {{$settings->purchase_order_email}}</b></p>
                <h2 style="text-align: center;font-size: 18px; margin-bottom: 0px; color: #000;">PURCHASE ORDER</h2>
            </div>
        </div>
        <div class="po-details" style="display: flex; justify-content: space-between; border-bottom: 2px solid #000;height:160px;">
            <div class="left" style="border-right: 2px solid #000; width: 48%;float:left;height:160px;">
                <p class="to" style="margin: 0px; font-size: 14px; margin-left: 2px;"><strong>TO,</strong></p>
                <p class="Address" style="font-size: 18px; margin-left: 2px; margin-bottom: -13px;font-family: Arial, sans-serif !important;text-transform: uppercase;"><b>{{$invoice->customer->business_name}}</b></p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">{{$invoice->customer->address}}</p>
                <p class="gstNo" style="font-size: 12px; margin-left: 2px; font-weight: 700; margin-top: -10px;">GSTIN No. &nbsp;&nbsp;: {{$invoice->customer->GSTIN}}</p>
                <p class="gstNo" style="font-size: 12px; margin-left: 2px; font-weight: 700; margin-top: -10px;">State.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->customer->state}}</p>
                <p class="gstNo" style="font-size: 12px; margin-left: 2px; font-weight: 700; margin-top: -10px;">State Code.&nbsp;: {{$invoice->customer->state_code}}</p>
            </div>
            <div class="right" style="margin: 0px; width: 52%;float:left;">
                <div class="firstDiv" style="border-bottom: 2px solid #000;">
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">P.O. No. &nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->invoice}}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">P.O. Date &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y') }}
                    </p>
                </div>
                <div class="secondDiv">
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">PO Revision & Date &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y') }}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">Reason of Revision &nbsp;&nbsp;: {{$invoice->po_revision_and_date}}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">Quotation Ref. No. &nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->reason_of_revision}}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">Remarks &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->remarks}}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">P. R. No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->prno}}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">P. R. Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ \Carbon\Carbon::parse($invoice->pr_date)->format('d-m-Y') }}</p>
                </div>
            </div>
        </div>

        <table class="items" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">SR NO.</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Description</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Material / Specification</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Qty</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Unit</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Rate/PC</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Per Pc Weight</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Total Weight</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Amount</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Delivery Date</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $key =>$item)
                    <?php 
                        $totalQty +=$item->quantity;
                        $totalWeight += $item->total_weight;
                        $totalAmount += $item->amount;
                    ?>
                    <tr id="{{$key+1}}">
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$key+1}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px;">{{$item->sparePart->name}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px;">{{$item->material_specification}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->quantity}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->unit}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->rate_kgs}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->per_pc_weight}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{($item->total_weight)}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{($item->amount)}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->delivery_date}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->remark}}</td>
                    </tr>
                @endforeach
               
                <tr>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: right;"><b>Total:-</b></td>
                    <td colspan="2" style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"><b>{{$totalQty}} </b></td>
                    <td colspan="2" style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: right;"><b>Approx Weight:- </b></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"><b>{{$totalWeight}} </b></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"><b>{{number_format($totalAmount,2)}} </b></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>

        <div class="po-details" style="display: flex; justify-content: space-between; height:170px; border-bottom: 2px solid #000;">
            <div class="left" style="border-right: 2px solid #000; width: 48%;float:left;">
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Terms & Conditions :</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Payment &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 45 days</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Mode of Payment&nbsp;&nbsp;&nbsp;: By Cheque</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Mode of Shipment&nbsp;&nbsp;: By Road</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Inspection&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: By Us</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Test Certification&nbsp;&nbsp;&nbsp;&nbsp;: MTC Report</p>
            </div>
            <div class="right" style="margin: 0px; width: 52%;float:left;">
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;"><strong>Additional Remarks :</strong></p>
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;">1. This soft copy of PO doesn't require any self attestation.</p>
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;">2. LD: 0.5% Per Week & 5% at max.</p>
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;">3. Kindly confirm the order acceptance by return mail.</p>
            </div>
        </div>

        <div class="signatures" style="display: flex; justify-content: space-between;font-size: 12px;height:130px;">
            <div class="prepared-by" style="border-right: 2px solid #000; width: 48%;float:left; height:130px;">
                <p style="font-size: 12px; margin-left: 2px; margin-top:-1px;font-weight: 700;">Prepared By: Raj Shanghani</p>
                <p style="font-size: 12px; margin-left: 8px; margin-top:-1px;font-weight: 700;">
                    <img src="{{$signatureprepared_byBase64}}" alt="Flomax Logo" style="max-width: 50%;margin-top:-10px; height:100px; display: block;">
                </p>

            </div>
            <div class="approved-by" style="margin: 0px; width: 52%;float:left;">
                <p style="font-size: 12px; margin-left: 2px;  margin-top:-1px;font-weight: 700;">Approved By: Sumit Chandvaniya</p>
                <p style="font-size: 12px; margin-left: 8px;  margin-top:-1px;font-weight: 700;">
                    <img src="{{$signatureapproved_byBase64}}" alt="Flomax Logo" style="max-width: 50%; margin-top:-10px;height:100px; display: block;">
                </p>
                
            </div>
        </div>
    </div>
</body>

</html>
