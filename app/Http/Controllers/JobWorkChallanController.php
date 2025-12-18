<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\JobWorkChallan;
use App\Models\JobWorkChallanItem;
use App\Models\Setting;
use App\Models\SparePart;
use App\Models\StakeHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Tcpdf\Fpdi;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class JobWorkChallanController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission('job work challan', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        $jobWorkChallans = JobWorkChallan::with('user')->get();
        
        return view('jobworkchallan.index', compact('jobWorkChallans'));
    }

    public function create()
    {
        if (!PermissionHelper::hasPermission('job work challan', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Fetch clients from the stake_holder table
        $clients = StakeHolder::where('user_type', 'Supplier')->get();

        // Fetch products from the products table
        $spareParts = SparePart::all(); // Adjust 'Product' if your model name is different

        // Determine the financial year
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->format('m');
        $financialYearStart = $currentDate->month >= 4 ? $currentDate->year : $currentDate->year - 1;
        $financialYearEnd = $financialYearStart + 1;
        $financialYear = substr($financialYearStart, -2) . '-' . substr($financialYearEnd, -2);
        
        // Generate the next invoice number
        $latestInvoice = JobWorkChallan::latest('id')->first();

        if ($latestInvoice) {
            // Extract the number part from the latest invoice
            $latestInvoiceNumber = $latestInvoice->po_no;
            $latestInvoiceNumberParts = explode('/', $latestInvoiceNumber);
            $latestInvoiceNumberPrefix = $latestInvoiceNumberParts[0]; // Get the prefix part (e.g., FDC-01)
            $number = (int)substr($latestInvoiceNumberPrefix, -4); // Get the last two digits of the prefix
            
            

            // Increment the number
            $newNumber = str_pad($number + 1, 2, '0', STR_PAD_LEFT);
            $invoiceNumber = 'FSVDC-'  .$newNumber .'-' . $currentMonth .  '/' . $financialYear;
        } else {
            // Generate the first invoice number
            $invoiceNumber = 'FSVDC-01/' . $financialYear;
        }

        // Pass the invoice number to the view
        return view('jobworkchallan.create', compact('clients', 'spareParts', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|exists:stake_holders,id',
            'job_work_name' => 'required|string|max:255',
            'pdf_files.*' => 'required|file|mimes:pdf|max:2048',
        ]);

        $pdfFiles = [];
        if ($request->hasFile('pdf_files')) {
            if (!file_exists(public_path('jobworkChallan'))) {
                mkdir(public_path('jobworkChallan'), 0755, true);
            }
            
            // Initialize FPDI with TCPDF
            $pdf = new Fpdi();
        
            foreach ($request->file('pdf_files') as $file) {
                // Get real path of each PDF file
                $filePath = $file->getRealPath();
        
                // Add each PDF page to the FPDI instance
                $pageCount = $pdf->setSourceFile($filePath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            }
        
            // Define a unique file name for the merged PDF
            $mergedFileName = time() . '_merged.pdf';
        
            // Save the merged PDF in the public/jobworkChallan folder
            $mergedFilePath = public_path('jobworkChallan/' . $mergedFileName);
            $pdf->Output($mergedFilePath, 'F');
        
            // Store only the merged PDF file name
            $pdfFiles[] = $mergedFileName;
        }
        

        // Store the job work challan details
        $invoice = JobWorkChallan::create([
            'address' => $request->input('address'),
            'job_work_name' => $request->job_work_name,
            'note' => $request->input('note'),
            'pdf_files' => $pdfFiles, // Only the merged file is stored
            'user_id' => Auth::id(),
            'customer_id' => $request->input('client_name'),
            'po_no' => $request->input('po_no'),
            'prno' => $request->input('prno'),
            'po_revision_and_date' => $request->input('po_revision_and_date'),
            'reason_of_revision' => $request->input('reason_of_revision'),
            'quotation_ref_no' => $request->input('quotation_ref_no'),
            'remarks' => $request->input('remarks'),
            'pr_date' => $request->input('pr_date'),
        ]);

        // Insert the invoice items
        $items = $request->input('item');
        $quantities = $request->input('quantity');
        $wt_pcs = $request->input('wtpc');
        $remarks = $request->input('remark');
        $material_specification = $request->input('material_specification');
        foreach ($items as $index => $item) {
            JobWorkChallanItem::create([
                'job_work_challans_id' => $invoice->id,
                'spare_part_id' => $item,
                'quantity' => $quantities[$index],
                'remaining_quantity' => $quantities[$index],
                'wt_pc' => $wt_pcs[$index],
                'material_specification' => $material_specification[$index],
                'remark' => $remarks[$index],
            ]);
        }

        return redirect()->route('jobworkchallans.index')->with('success', 'Job Work Challan created successfully.');
    }

  public function getData(Request $request)
{
    $query = JobWorkChallan::with(['user', 'items']); // Eager load the user and items relationships

    // Apply filtering
    if ($request->has('search') && is_array($request->input('search'))) {
        $search = $request->input('search')['value'];
        $query->where(function ($q) use ($search) {
            $q->where('job_work_name', 'like', "%{$search}%")
                ->orWhere('pdf_files', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%"); // Assuming 'name' is the column in users table
                });
        });
    }

    $filteredRecords = $query->count();

    // Apply sorting
    $column = $request->input('order.0.column');
    $direction = $request->input('order.0.dir');
    $columns = ['id', 'job_work_name', 'uploaded_by', 'pdf_files', 'created_at'];
    if (isset($columns[$column])) {
        $query->orderBy($columns[$column], $direction);
    }

    // Pagination
    $length = $request->input('length');
    $start = $request->input('start');
    $data = $query->skip($start)->take($length)->get();

    $totalRecords = JobWorkChallan::count();

    // Format data
    $data = $data->isEmpty() ? [] : $data->map(function ($challan, $index) use ($start) {
        $pdfLinks = '';
        if (!empty($challan->pdf_files) && is_array($challan->pdf_files)) {
            foreach ($challan->pdf_files as $file) {
                $pdfLinks .= '<a href="' . asset('jobworkChallan/' . $file) . '" target="_blank">View PDF</a><br>';
            }
        }

        // Check if all items have remaining_quantity set to 0
        $allItemsReceived = $challan->items->every(fn($item) => $item->remaining_quantity == 0);

        // Define the action buttons
        $actionButtons = '
            <a href="' . route('jobworkchallans.download', $challan->id) . '" class="btn btn-success btn-sm">Download PDF</a>
            <a href="' . route('jobworkchallans.edit', $challan->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit fa-sm"></i></a>
            <form action="' . route('jobworkchallans.destroy', $challan->id) . '" method="POST" style="display:inline;">
                ' . csrf_field() . '
                ' . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash fa-sm"></i></button>
            </form>';

        // Add "Receive" button if not all items have remaining_quantity as 0
        if (!$allItemsReceived) {
            $actionButtons = '<a href="' . route('jobworkchallans.receive', $challan->id) . '" class="btn btn-warning btn-sm">Receive</a> ' . $actionButtons;
        }

        return [
            'id' => $challan->id,
            'job_work_name' => $challan->job_work_name,
            'po_no' => $challan->po_no,
            'uploaded_by' => $challan->user ? $challan->user->name : 'N/A', // Access user name
            'pdf_files' => $pdfLinks, // Links to view PDFs
            'created_at' => $challan->created_at->format('Y-m-d H:i:s'),
            'action' => $actionButtons,
        ];
    });

    // Return JSON response
    return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ]);
}


    public function edit($id)
    {
        $challan = JobWorkChallan::with('items')->findOrFail($id);
        $clients = StakeHolder::all();
        $spareParts = SparePart::all();
        return view('jobworkchallan.edit', compact('challan','spareParts','clients'));
    }

    public function update(Request $request, $id)
    {
        if (!PermissionHelper::hasPermission('job work challan', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $challan = JobWorkChallan::findOrFail($id);

        // Validate the request
        $request->validate([
            'job_work_name' => 'required|string|max:255',
            'po_no' => 'required|string',
            'pdf_files.*' => 'nullable|mimes:pdf|max:2048',
            'quantity.*' => 'required|numeric|min:1',
        ]);

        // Update job work name
        $challan->job_work_name = $request->input('job_work_name');

        // Handle new PDF files
        if ($request->hasFile('pdf_files')) {
            $newFiles = $request->file('pdf_files');
            $existingFiles = $challan->pdf_files; // Retrieve existing files

            // Store new files
            foreach ($newFiles as $file) {
                $fileName = $file->store('pdf_files', 'public');
                $existingFiles[] = $fileName;
            }

            // Update the `pdf_files` attribute with the new list
            $challan->pdf_files = $existingFiles;
        }

        // Update the record
        $challan->save();

        // Handle file deletions
        if ($request->input('delete_files')) {
            $filesToDelete = $request->input('delete_files');
            $existingFiles = $challan->pdf_files;

            // Remove deleted files from storage and database
            foreach ($filesToDelete as $fileToDelete) {
                if (($key = array_search($fileToDelete, $existingFiles)) !== false) {
                    unset($existingFiles[$key]);
                    Storage::disk('public')->delete($fileToDelete);
                }
            }

            // Update the `pdf_files` attribute with the remaining files
            $challan->pdf_files = array_values($existingFiles);
            $challan->save();
        }

        // Find the invoice record
        $invoice = JobWorkChallan::findOrFail($id);

        // Update the invoice record
        $invoice->update([
            'address' => $request->input('address'),
            'job_work_name' => $request->job_work_name,
            'note' => $request->input('note'),
            'user_id' => Auth::id(),
            'customer_id' => $request->input('client_name'),
            'po_no' => $request->input('po_no'),
            'prno' => $request->input('prno'),
            'po_revision_and_date' => $request->input('po_revision_and_date'),
            'reason_of_revision' => $request->input('reason_of_revision'),
            'quotation_ref_no' => $request->input('quotation_ref_no'),
            'remarks' => $request->input('remarks'),
            'pr_date' => $request->input('pr_date'),
        ]);
        // Delete existing invoice items
        $invoice->items()->delete();
        
        // Insert the invoice items
        $items = $request->input('item');
        $quantities = $request->input('quantity');
        // $productUnits = $request->input('product_unit');
        $wt_pcs = $request->input('wtpc');
        $remarks = $request->input('remark');
        $material_specification = $request->input('material_specification');
        foreach ($items as $index => $item) {
            JobWorkChallanItem::create([
                'job_work_challans_id' => $invoice->id,
                'spare_part_id' => $item,
                'quantity' => $quantities[$index],
                'remaining_quantity' => $quantities[$index],
                'wt_pc' => $wt_pcs[$index],
                'material_specification' => $material_specification[$index],
                'remark' => $remarks[$index],
            ]);
        }
        
        return redirect()->route('jobworkchallans.index')->with('success', 'Job Work Challan updated successfully!');
    }

    public function downloadPDF($id)
    {
        $settings = Setting::first();
    
        // Get logo path and convert to base64
        $logoPath = public_path($settings->purchase_order_logo ?? 'assets/flowmax.png');
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoData = file_get_contents($logoPath);
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
    
        // Get authorized signatory path and convert to base64
        $signaturePath = public_path($settings->authorized_signatory ?? 'assets/default_signature.png');
        $signatureType = pathinfo($signaturePath, PATHINFO_EXTENSION);
        $signatureData = file_get_contents($signaturePath);
        $signatureBase64 = 'data:image/' . $signatureType . ';base64,' . base64_encode($signatureData);
    
        $signaturePathprepared_by = public_path($settings->prepared_by ?? 'assets/default_signature.png');
        $signatureTypeprepared_by = pathinfo($signaturePathprepared_by, PATHINFO_EXTENSION);
        $signatureDataprepared_by = file_get_contents($signaturePathprepared_by);
        $signatureprepared_byBase64 = 'data:image/' . $signatureTypeprepared_by . ';base64,' . base64_encode($signatureDataprepared_by);
    
        $signaturePathapproved_by = public_path($settings->approved_by ?? 'assets/default_signature.png');
        $signatureTypeapproved_by = pathinfo($signaturePathapproved_by, PATHINFO_EXTENSION);
        $signatureDataapproved_by = file_get_contents($signaturePathapproved_by);
        $signatureapproved_byBase64 = 'data:image/' . $signatureTypeapproved_by . ';base64,' . base64_encode($signatureDataapproved_by);
    
        // Fetch invoice data with related customer and items
        $invoice = JobWorkChallan::with('items', 'customer')->findOrFail($id);
    
        // Render the HTML view and create the new PDF
        $html = view('jobworkchallan.invoice_pdf', [
            'invoice' => $invoice,
            'logoBase64' => $logoBase64,
            'signatureBase64' => $signatureBase64,
            'signatureprepared_byBase64' => $signatureprepared_byBase64,
            'signatureapproved_byBase64' => $signatureapproved_byBase64,
            'settings' => $settings
        ])->render();
    
        $pdf = Pdf::loadHTML($html);
    
        // Define the path for the new folder
        $folderPath = public_path('jobworkChallan');
        
        // Ensure the directory exists
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
    
        // Define the path to save the new PDF
        $newPdfPath = $folderPath . '/' . str_replace('/','-', $invoice->po_no) . '.pdf';
    
        // Save the new PDF
        $pdf->save($newPdfPath);
    
        // Check if there's an existing PDF file
        if (!empty($invoice->pdf_files) && file_exists(public_path('jobworkChallan/' . $invoice->pdf_files[0]))) {
            // Define the path to the existing PDF
            $existingPdfPath = public_path('jobworkChallan/' . $invoice->pdf_files[0]);
            // Define the path for the merged PDF
            $mergedPdfPath = $folderPath . '/Delivery_Challans_' . $invoice->id . '.pdf';
    
            // Merge the PDFs
            try {
                $this->mergePDFs([$newPdfPath, $existingPdfPath], $mergedPdfPath);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error merging PDFs: ' . $e->getMessage()
                ], 500);
            }
    
            // Return the merged PDF for download
            return response()->download($mergedPdfPath)->deleteFileAfterSend(true);
        }
    
        // If there's no existing PDF, return the new PDF directly
        return response()->download($newPdfPath)->deleteFileAfterSend(true);
    }

    public function mergePDFs(array $pdfPaths, string $outputPath)
    {
       
        try {
            // Initialize PDFMerger
            $oMerger = PDFMerger::init();

            // Add PDFs to the merger
            foreach ($pdfPaths as $pdfPath) {
                // Check if the file exists
                if (!File::exists($pdfPath)) {
                    throw new \Exception("File does not exist: " . $pdfPath);
                }

                // Add PDF to the merger
                $oMerger->addPDF($pdfPath, 'all'); // or specify pages, e.g., [1,2]
            }

            // Merge PDFs
            $oMerger->merge();

            // Ensure output directory exists
            $outputDir = dirname($outputPath);
            if (!File::exists($outputDir)) {
                File::makeDirectory($outputDir, 0755, true);
            }

            // Save the merged PDF
            $oMerger->save($outputPath);

            // Download the merged PDF
            $response = response()->download($outputPath);

            // Delete the New_Delivery_Challan PDF after download
            if (File::exists($pdfPaths[0])) {
                File::delete($pdfPaths[0]);
            }

            return $response->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Log or handle errors
            Log::error('PDF merge error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('create new purchase order', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            // Find the invoice by ID
            $invoice = JobWorkChallan::findOrFail($id);

            // Delete all associated invoice items
            $invoice->items()->delete();

            // Delete the invoice
            $invoice->delete();

            // Return a success response
            return response()->json(['success' => 'Job Work Challan and its items deleted successfully!'], 200);
        } catch (\Exception $e) {
            // If there is an exception (e.g., invoice not found), return an error response
            return response()->json(['error' => 'Failed to delete the Job Work Challan.'], 500);
        }
    }
    
    public function showReceiveForm($id)
    {
        $challan = JobWorkChallan::with('items.sparePart')->findOrFail($id);
        return view('jobworkchallan.receive', compact('challan'));
    }

    
    public function storeReceivedQuantity(Request $request, $id)
    {
        $request->validate([
            'received_quantity.*' => 'required|numeric|min:0',
        ]);
    
        $challan = JobWorkChallan::findOrFail($id);
    
        // Update remaining quantities based on received quantities
        foreach ($request->input('received_quantity') as $itemId => $receivedQty) {
            $item = JobWorkChallanItem::find($itemId);
    
            if ($item) {
                $newRemainingQty = max($item->remaining_quantity - $receivedQty, 0);
                $item->remaining_quantity = $newRemainingQty;
                $item->save();
            }
        }
    
        return redirect()->route('jobworkchallans.index')->with('success', 'Received quantities updated successfully.');
    }

}
