@extends('layouts.app')

@section('content')
    <style>
        span.select2-selection.select2-selection--single {
            padding-bottom: 29px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 68% !important;
        }
    </style>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Create Finished Product</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('finishedProducts.index') }}">Finished
                                    Products</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add New Finished Product</h3>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('finishedProducts.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_id">Select Product</label>
                                                <select class="form-control select2" id="product_id" name="product_id"
                                                    required>
                                                    <option value="">Select a product...</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quantity">Quantity</label>
                                                <input type="number" class="form-control" id="quantity" name="quantity"
                                                    min="1" value="1" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="parts-info" class="mt-4" style="display: none;">
                                        <h4>Required Parts</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Part Name</th>
                                                        <th>Required Quantity</th>
                                                        <th>Available Quantity</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="parts-list">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        {{-- <button type="button" id="check-inventory" class="btn btn-info mr-2">Check
                                            Inventory</button> --}}
                                        <button type="submit" class="btn btn-success">Create Finished Product</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#check-inventory').click(function() {
                const productId = $('#product_id').val();
                const quantity = $('#quantity').val();

                if (!productId) {
                    alert('Please select a product first');
                    return;
                }

                $.ajax({
                    url: '{{ route('finishedProducts.checkInventory') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        $('#parts-info').show();
                        $('#parts-list').empty();

                        response.parts.forEach(function(part) {
                            const row = `
                                <tr>
                                    <td>${part.name}</td>
                                    <td>${part.required_quantity}</td>
                                    <td>${part.available_quantity}</td>
                                    <td>
                                        <span class="badge badge-${part.sufficient ? 'success' : 'danger'}">
                                            ${part.sufficient ? 'Available' : 'Insufficient'}
                                        </span>
                                    </td>
                                </tr>
                            `;
                            $('#parts-list').append(row);
                        });
                    },
                    error: function(xhr) {
                        alert('Error checking inventory: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
