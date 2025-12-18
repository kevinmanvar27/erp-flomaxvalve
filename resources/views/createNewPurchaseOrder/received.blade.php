@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Receive Purchase Order</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('newpurchaseorder.index') }}">New Purchase Order</a></li>
                            <li class="breadcrumb-item active">Receive</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="card">
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
                    <form action="{{ route('newpurchaseorder.storeReceivedQuantity', $purchaseOrder->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="invoice" class="form-label">Invoice #</label>
                                <input type="text" class="form-control" id="invoice" value="{{ $purchaseOrder->invoice }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="client_name" class="form-label">Client Name</label>
                                <input type="text" class="form-control" id="client_name" value="{{ $purchaseOrder->customer->name }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" value="{{ $purchaseOrder->status }}" readonly>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Spare Part</th>
                                        <th>Total Quantity</th>
                                        <th>Total Received Quantity</th>
                                        <th>Received Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseOrder->items as $item)
                                        @php
                                            $totalReceivedQty = $item->quantity - $item->remaining_quantity;
                                            $isFullyReceived = $item->quantity == $totalReceivedQty;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->sparePart->name ?? 'N/A' }}</td>
                                            <td>
                                                <input type="number" class="form-control" value="{{ $item->quantity }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" value="{{ $totalReceivedQty }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="received_quantity[{{ $item->id }}]" 
                                                       class="form-control" 
                                                       min="0" 
                                                       max="{{ $item->remaining_quantity }}" 
                                                       value="0" 
                                                       {{ $isFullyReceived ? 'disabled' : '' }} 
                                                       placeholder="{{ $isFullyReceived ? 'Fully received' : 'Enter received qty' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Save Received Quantities</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
