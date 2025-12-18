@extends('layouts.app')
<style>
    span.select2-selection.select2-selection--single {
        padding-bottom: 29px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        top: 68% !important;
    }

    .custom-file-container {
        position: relative !important;
    }

    .custom-file-input {
        display: none !important;
    }

    .custom-file-label {
        display: inline-block !important;
        padding: 10px 20px !important;
        background-color: #2eae4b !important;
        color: white !important;
        border-radius: 50px !important;
        cursor: pointer !important;
        text-align: center !important;
        font-size: 16px !important;
        font-weight: bold !important;
        transition: background-color 0.3s ease !important;
    }

    .custom-file-label:hover {
        background-color: #249c40 !important;
    }

    .custom-file-label .plus-icon {
        background-color: white !important;
        color: #2eae4b !important;
        padding: 0px 10px !important;
        border-radius: 50% !important;
        font-size: 20px !important;
        margin-right: 10px !important;
        font-weight: bold !important;
        display: inline-block !important;
        margin-top: -6px;
    }

    .file-preview {
        width: 120px !important;
        height: auto !important;
        text-align: center !important;
        position: relative !important;
    }

    .file-preview img {
        width: 60px !important;
        height: auto !important;
    }

    .remove-file {
        position: absolute !important;
        top: 5px !important;
        right: 5px !important;
        background-color: red !important;
        color: white !important;
        border-radius: 50% !important;
        padding: 2px 5px !important;
        cursor: pointer !important;
    }
</style>
@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Create Delivery Challan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Create Delivery Challan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">

                    <div class="card-body">
                        <form action="{{ route('jobworkchallans.store') }}" method="POST" enctype="multipart/form-data"
                            id="jobWorkForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="job_work_name">Process</label>
                                        <input type="text" name="job_work_name" class="form-control"
                                            placeholder="Enter process name" required>
                                    </div>
                                </div>

                                <!-- Custom File Input Design -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pdf_files">Upload PDF Files ( Optional ) </label>
                                        <div class="custom-file-container">
                                            <input type="file" name="pdf_files[]" id="pdfFiles" class="custom-file-input" accept=".pdf" multiple >
                                            <label for="pdfFiles" class="custom-file-label">
                                                <span class="plus-icon">+</span> Upload PDF Files
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="pdfPreview" class="d-flex flex-wrap"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="client_name" class="form-label">Client Name</label>
                                    <select class="custom-select select2" id="client_name" name="client_name" required>
                                        <option value="">Select Client</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="po_no" class="form-label">Invoice #</label>
                                    <input type="text" class="form-control" id="po_no" name="po_no" value="{{ $invoiceNumber }}"  required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="date" class="form-label">PO Revision & Date</label>
                                    <input type="date" class="form-control" id="po_revision_and_date"
                                        name="po_revision_and_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="prno" class="form-label">Reason of Revision</label>
                                    <input type="text" class="form-control" id="reason_of_revision"
                                        name="reason_of_revision" value="">
                                </div>
                                <div class="col-md-2">
                                    <label for="prno" class="form-label">Quotation Ref. No.</label>
                                    <input type="text" class="form-control" id="quotation_ref_no" name="quotation_ref_no"
                                        value="">
                                </div>
                                <div class="col-md-2">
                                    <label for="prno" class="form-label">Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks"
                                        value="">
                                </div>
                                <div class="col-md-2">
                                    <label for="prno" class="form-label">P.R. No</label>
                                    <input type="text" class="form-control" id="prno" name="prno"
                                        value="">
                                </div>
                                <div class="col-md-2">
                                    <label for="pr_date" class="form-label">P.R. Date</label>
                                    <input type="date" class="form-control" id="pr_date" name="pr_date" required>
                                </div>
                            </div>
                            <!-- Inventory Table -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary add-row mb-2"><i
                                            class="fas fa-plus"></i>
                                        Add Row</button>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="inventoryTable">
                                            <thead>
                                                <tr>
                                                    <th>Spare Part</th>
                                                    <th>Material / Specification</th>
                                                    <th>Quantity</th>
                                                    <th>Wt./PC</th>
                                                    <th>Net Weight</th>
                                                    <th>Remark</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select class="custom-select select2 spare-part " style="width:100%;"
                                                            name="item[]">
                                                            <option value="">Select Spare Part</option>
                                                            @foreach ($spareParts as $part)
                                                                <option value="{{ $part->id }}">{{ $part->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="material_specification[]"></td>
                                                    <td><input type="number" class="form-control" name="quantity[]" min="1" value="1" required></td>
                                                    <td><input type="number" class="form-control" name="wtpc[]" step="0.001" min="0" value="0" required></td>
                                                    <td><input type="number" class="form-control amount" name="netweight[]" readonly></td>
                                                    <td><input type="text" class="form-control" name="remark[]"></td>
                                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><iclass="fas fa-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Submit Delivery Challan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.getElementById('pdfFiles').addEventListener('change', function(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('pdfPreview');
            previewContainer.innerHTML = ''; // Clear previous previews

            Array.from(files).forEach((file, index) => {
                const fileReader = new FileReader();

                fileReader.onload = function() {
                    const fileDiv = document.createElement('div');
                    fileDiv.classList.add('file-preview', 'm-2', 'position-relative');
                    fileDiv.innerHTML = `
                    <div class="card text-center" style="width: 8rem;">
                        <div class="card-body">
                            <img src="https://img.icons8.com/plasticine/100/000000/pdf.png" class="img-fluid" alt="PDF Icon">
                            <p class="card-text mt-2">${file.name}</p>
                        </div>
                        <button type="button" class="close remove-file" aria-label="Close" data-index="${index}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                    previewContainer.appendChild(fileDiv);
                };

                fileReader.readAsDataURL(file);
            });
        });

        document.addEventListener('click', function(event) {
            if (event.target.closest('.remove-file')) {
                const index = event.target.closest('.remove-file').dataset.index;
                const inputFileElement = document.getElementById('pdfFiles');
                const dataTransfer = new DataTransfer();

                Array.from(inputFileElement.files).forEach((file, i) => {
                    if (i != index) {
                        dataTransfer.items.add(file);
                    }
                });

                inputFileElement.files = dataTransfer.files;

                const previewContainer = document.getElementById('pdfPreview');
                previewContainer.children[index].remove(); // Remove the preview
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Function to calculate amounts
            function calculateAmounts() {
                let subtotal = 0;
                $('#inventoryTable tbody tr').each(function() {
                    const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
                    const price = parseFloat($(this).find('input[name="wtpc[]"]').val()) || 0;
                    const amount = qty * price;
                    subtotal += amount;
                    $(this).find('input.amount').val(amount.toFixed(2));
                });
              
                $('#subtotal').val(subtotal.toFixed(2));

            }

            // Calculate amounts on change
            $(document).on('change',
                'input[name="quantity[]"], input[name="wtpc[]"]',
                calculateAmounts);

            function initializeSelect2() {
                $('.select2').select2({
                    placeholder: 'Select Spare Part',
                    allowClear: true
                });
            }

            // Initialize Select2 on existing select elements
            initializeSelect2();

            // Add new row
            $(".add-row").click(function() {
                let newRow = `
        <tr>
            <td>
                <select class="custom-select select2 spare-part" style="width:100%;" name="item[]">
                    <option value="">Select Spare Part</option>
                    @foreach ($spareParts as $part)
                        <option value="{{ $part->id }}">{{ $part->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" class="form-control" name="material_specification[]"></td>
            <td><input type="number" class="form-control" name="quantity[]" min="1" value="1" required></td>
            <td><input type="number" class="form-control" name="wtpc[]" step="0.001" min="0" value="0" required></td>
            <td><input type="number" class="form-control amount" name="netweight[]" readonly></td>
            <td><input type="text" class="form-control" name="remark[]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
        </tr>`;
                $('#inventoryTable tbody').append(newRow);
                // Reinitialize Select2 on newly added selects
                initializeSelect2();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateAmounts();
            });
        });
    </script>
    
    
    <script>
        $(document).ready(function () {
            $(document).on('change', '.spare-part', function () {
                var sparePartId = $(this).val();
                var row = $(this).closest('tr');
    
                if (sparePartId) {
                    $.ajax({
                        url: `/newpurchaseorder/get-part-details/${sparePartId}`,
                        type: 'get',
                        success: function (response) {
                            if (response.success) {
                                const sparePart = response.sparePart;
                                row.find('input[name="wtpc[]"]').val(sparePart.weight);
                                updateRowCalculations(row);
                            }
                        },
                        error: function () {
                            alert('Please Select Again');
                        }
                    });
                }
            });
           
            $('#client_name').change(function () {
                    var clientId = $(this).val();
    
                    if (clientId) {
                        $.ajax({
                            url: `/newpurchaseorder/get-client-details/${clientId}`,
                            type: 'get',
                            success: function (response) {
                                if (response.success) {
                                    const sparePart = response.clientId;
                                    $('#address').val(response.address);
                                    
                                }
                            },
                            error: function () {
                                alert('Please Select Again');
                            }
                        });
                    }
                });
            $(document).on('change', 'input', function () {
                var row = $(this).closest('tr'); // Get the current row where the input changed
                updateRowCalculations(row); // Recalculate Total Weight and Amount
            });
            
            function updateRowCalculations(row) {
                const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
                const perPcWeight = parseFloat(row.find('input[name="wtpc[]"]').val()) || 0;
                // Calculate and update Total Weight (qty * Per Pc Weight)
                const totalWeight = qty * perPcWeight;
                row.find('input[name="netweight[]"]').val(totalWeight.toFixed(3));
            }
        });
    </script>

    
    
@endsection
