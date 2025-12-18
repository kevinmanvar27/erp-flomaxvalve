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
                <h2 style="text-align: center;font-size: 18px; margin-bottom: 0px; color: #000;">DELIVERY CHALLAN</h2>
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
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">D.C. No. &nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->po_no}}</p>
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">D.C. Date &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y') }}
                    </p>
                </div>
                <div class="secondDiv">
                    <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">PO Revision & Date &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y') }}</p>
                    
                    @if($invoice->po_revision_and_date)
                        <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">Reason of Revision &nbsp;&nbsp;: {{$invoice->po_revision_and_date}}</p>
                    @endif
                    
                    @if($invoice->reason_of_revision)
                        <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">Quotation Ref. No. &nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->reason_of_revision}}</p>
                    @endif
                    
                    @if($invoice->remarks)
                        <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">Remarks &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->remarks}}</p>
                    @endif
                    
                    @if($invoice->prno)
                        <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">P. R. No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$invoice->prno}}</p>
                    @endif
                    
                    @if($invoice->pr_date)
                        <p style="margin: 0px; font-size: 13px; margin-left: 2px; font-weight: 700; margin-bottom: 4px;">P. R. Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ \Carbon\Carbon::parse($invoice->pr_date)->format('d-m-Y') }}</p>
                    @endif

                </div>
            </div>
        </div>

        <table class="items" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">SR NO.</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Description</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Material / Specification</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Wt./PC</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">QTY</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; padding: 10px; text-align: center; ">Net Weight</th>
                    <th style="border: 1px solid #000;font-size:10px; border-top: 0px solid #000 !important; border-left: 0px solid #000 !important; border-right: 0px solid #000 !important; padding: 10px; text-align: center; ">Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $key =>$item)
                    <?php 
                        $totalQty +=$item->quantity;
                        $totalWeight +=($item->wt_pc*$item->quantity);
                    ?>
                    <tr id="{{$key+1}}">
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$key+1}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px;">{{$item->sparePart->name}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px;">{{$item->material_specification}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->wt_pc}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->quantity}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;">{{$item->wt_pc*$item->quantity}}</td>
                        <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; border-right: 0px solid #000 !important; text-align: center;">{{$item->remark}}</td>
                    </tr>
                @endforeach
               
                <tr>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"><b>{{$totalQty}}</b></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px; text-align: center;"><b>{{$totalWeight}}</b></td>
                    <td style="border: 1px solid #000; border-left: 0px solid #000 !important; padding: 8px; font-size:10px;border-right: 0px solid #000 !important; text-align: right;"></td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>

        <div class="po-details" style="display: flex; justify-content: space-between; height:233px; border-bottom: 2px solid #000;">
            <div class="left" style="border-right: 2px solid #000; width: 48%;float:left;height:233px;">
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Terms & Conditions :</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Payment &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 45 days</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Mode of Payment&nbsp;&nbsp;&nbsp;: By Cheque</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Mode of Shipment&nbsp;&nbsp;: By Road</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">PO Acceptance&nbsp;&nbsp;: </p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Inspection&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: By Us</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Test Certification&nbsp;&nbsp;&nbsp;&nbsp;: MTC Report</p>
                <p class="Address" style="font-size: 12px; margin-left: 2px; font-weight: 700;">Jurisdiction area&nbsp;&nbsp;&nbsp;&nbsp;: RAJKOT</p>
            </div>
            <div class="right" style="margin: 0px; width: 52%;float:left;">
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;"><strong>Additional Remarks :</strong></p>
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;">1. This soft copy of PO doesn't require any self attestation.</p>
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;">2. LD: 0.5% Per Week & 5% at max.</p>
                <p style="font-size: 12px; margin-left: 2px; font-weight: 700;">3. Kindly confirm the order acceptance by return mail.</p>
            </div>
        </div>

        <div class="signatures" style="display: flex; justify-content: space-between;font-size: 12px;height:90px;">
            <div class="prepared-by" style="border-right: 2px solid #000; width: 48%;float:left;height:90px;">
                <p style="font-size: 12px; margin-left: 2px; margin-top:-1px;font-weight: 700;">Prepared By: Bhargav Akabari</p>
                <p style="font-size: 12px; margin-left: 8px; margin-top:-1px;font-weight: 700;">
                    <img src="{{$signatureprepared_byBase64}}" alt="Flomax Logo" style="max-width: 30%;margin-top:-3px; display: block;">
                </p>

            </div>
            <div class="approved-by" style="margin: 0px; width: 52%;float:left;">
                <p style="font-size: 12px; margin-left: 2px;  margin-top:-1px;font-weight: 700;">Issued By : Sumit Chandvaniya</p>
                <p style="font-size: 12px; margin-left: 8px;  margin-top:-1px;font-weight: 700;">
                    <img src="{{$signatureapproved_byBase64}}" alt="Flomax Logo" style="max-width: 30%; margin-top:-3px; display: block;">
                </p>
                
            </div>
        </div>
    </div>
</body>

</html>
