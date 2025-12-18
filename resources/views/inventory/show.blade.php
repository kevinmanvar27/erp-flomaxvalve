@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('purchaseOrder.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add New Received Purchase Order
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('purchaseOrder.index') }}">Purchase Order</a></li>
                            <li class="breadcrumb-item active">Purchase Details</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Inventory Details -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-6">Create Date :</dt>
                                    <dd class="col-sm-6">{{ $inventory->create_date }}</dd>
                                    <dt class="col-sm-6">Invoice Number :</dt>
                                    <dd class="col-sm-6">{{ $inventory->invoice_number }}</dd>
                                    <dt class="col-sm-6">Supplier :</dt>
                                    <dd class="col-sm-6">{{ $inventory->supplier->name }}</dd>
                                    @if ($inventory->purchase_order_invoice)
                                    <dt class="col-sm-6">Show File :</dt>
                                    <dd class="col-sm-6"><a href="{{ route('purchaseOrder.showFile', $inventory->id) }}" target="_blank">Click here</a></dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spare Parts Details -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header">Spare Parts</div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Parts Name</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">SKU Code</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventory->items as $item)
                                        <tr>
                                            <th scope="row">{{ $item->id }}</th>
                                            <td>{{ $item->sparePart->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->sku_code }}</td>
                                            <td>{{ $item->amount }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
