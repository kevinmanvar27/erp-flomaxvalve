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
                    <h3>Customer Rejection</h3>
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
                <div class="card-body">
                    <form method="GET" action="{{ route('rejection.createCustomerRejection') }}">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="customer_id">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control select2" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"  {{ request('customer_id') == $customer->id ? 'selected' : '' }} >{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            
                            <div class="col-md-3">
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-control select2" style="width: 100%;" id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                           
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ request('quantity') }}" required/>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary" id="garbageSearchBtn">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <form method="POST" action="{{ route('rejection.customerStore') }}">
                    @csrf
                    <div class="card-body">
                        <input type="hidden" name="removed_spare_parts" id="removed_spare_parts" value="" />
                        <input type="hidden" name="rejectionQty" id="rejectionQty" value="{{$quantituSearch}}" />

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Spare Parts</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Weight</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            @if (request('product_id'))
                                @if ($spareParts->isEmpty())
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center">No data available.</td>
                                        </tr>
                                    </tbody>
                                @else
                                    <tbody>
                                        <input type="hidden" name="productId" value="{{ request('product_id') }}">
                                        <input type="hidden" name="quantity" value="{{ request('quantity') }}">
                                        @foreach ($spareParts as $part)
                                            <tr>
                                                <th scope="row">{{ $part->name }}</th>
                                                <td>{{ $part->type }}</td>
                                                <td>{{ $part->size }}</td>
                                                <td>{{ $part->weight }}</td>
                                                <td>
                                                    <input type="number" class="form-control" name="spare_parts[{{ $part->id }}][quantity]" value="{{ $part->quantity ?? request('quantity') }}" />
                                                    <input type="hidden" name="spare_parts[{{ $part->id }}][id]" value="{{ $part->id }}" />
                                                    <input type="hidden" name="spare_parts[{{ $part->id }}][type]" value="{{ $part->type }}" />
                                                    <input type="hidden" name="spare_parts[{{ $part->id }}][size]" value="{{ $part->size }}" />
                                                    <input type="hidden" name="spare_parts[{{ $part->id }}][weight]" value="{{ $part->weight }}" />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $part->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endif
                            @else
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center">Please select a product to view spare parts.</td>
                                    </tr>
                                </tbody>
                            @endif
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 float-right" style="width: 40%;">
                            <div class="col-md-12">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" rows="6" name="note"></textarea>
                            </div>
                            <div class="col-md-12 mt-3">
                                <input type="submit" value="Save" class="btn btn-success float-right" style="width:35%;font-weight: 600;">
                            </div>
                        </div>
                        <div class="row mb-3 float-right" style="width: 40%;"></div>
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
@endsection

