@extends('layouts.app')

@section('content')
    <style>
        span.select2-selection.select2-selection--single {
            padding-bottom: 29px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 68% !important;
        }

        #garbageSearchBtn {
            margin-top: 31px;
            width: 66%;
        }
    </style>
    <div class="content-wrapper">
        <section class="content">
            
            <div class="card">
                <div class="card-header">
                    <h3>Internal Rejection</h3>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
              
                <form method="POST" action="{{ route('rejection.internalStore') }}">
                    @csrf
                    <div class="card-body">
                        <input type="hidden" name="removed_spare_parts" id="removed_spare_parts" value="" />

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h4 class="mb-3"><button type="button"
                                        class="btn btn-primary add-row float-right"><i class="fas fa-plus"></i> Add
                                        New</button></h4>
                                        <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>Parts Name</th>
                                                <th>Quantity</th>
                                                <th>Reason</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-control select2" style="width:100%;"
                                                        name="parts_name[]">
                                                        <option value="">Select Spare Part</option>
                                                        @foreach ($parts as $part)
                                                            <option value="{{ $part->id }}">{{ $part->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control" name="quantity[]"
                                                        min="0" value="0" required></td>
                                              
                                                <td><input type="text" class="form-control" name="reason[]" ></td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                            class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-success float-right">Submit</button>
                            </div>
                        </div>
                    </div>
                   
                </form>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener for delete buttons
            document.querySelectorAll('.delete-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var row = this.closest('tr'); // Get the row containing the button
                    row.classList.add('d-none'); // Hide the row instead of removing it

                    // Add hidden input to mark the spare part for removal
                    var sparePartId = this.getAttribute('data-id');
                    var removedPartsInput = document.getElementById('removed_spare_parts');
                    if (!removedPartsInput) {
                        console.log('Test');
                        removedPartsInput = document.createElement('input');
                        removedPartsInput.type = 'hidden';
                        removedPartsInput.name = 'removed_spare_parts[]';
                        removedPartsInput.id = 'removed_spare_parts';
                        document.querySelector('form').appendChild(removedPartsInput);
                    }
                    var currentValue = removedPartsInput.value ? removedPartsInput.value.split(',') : [];
                    if (!currentValue.includes(sparePartId)) {
                        currentValue.push(sparePartId);
                        removedPartsInput.value = currentValue.join(',');
                    }
                });
            });
        });
    </script>

<script>
    $(document).ready(function() {
        // Function to initialize Select2
        function initializeSelect2() {
            $('.select2').select2({
                placeholder: 'Select Spare Part',
                allowClear: true
            });
        }

        // Initialize Select2 on existing select elements
        initializeSelect2();

        // Add row function
        $(".add-row").click(function() {
            var newRow = `
        <tr>
            <td>
                <select class="form-control select2" style="width:100%;" name="parts_name[]">
                    <option value="">Select Spare Part</option>
                    @foreach ($parts as $part)
                        <option value="{{ $part->id }}">{{ $part->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" class="form-control" name="quantity[]" min="0" value="0" required></td>
            <td><input type="text" class="form-control" name="reason[]" ></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `;
            $("#inventoryTable tbody").append(newRow);

            // Reinitialize Select2 on newly added select elements
            initializeSelect2();
        });

        // Remove row function
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
  
@endsection
