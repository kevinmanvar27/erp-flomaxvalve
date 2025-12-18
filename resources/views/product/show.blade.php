@extends('layouts.app')

@section('content')
<?php 
$productDetails = $product->toArray();
$productItems = $productDetails['items'];
$totalParts = 0;
?>

@foreach($productItems as $key => $sparePart)
    <?php $totalParts += $sparePart['quantity']; ?>
@endforeach


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('products.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add Product
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Product Details</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-6">Product Name :</dt>
                                    <dd class="col-sm-6">{{ $product->name }}</dd>
                                    <dt class="col-sm-6">Total Parts :</dt>
                                    <dd class="col-sm-6">{{ $totalParts }}</dd>
                                    <dt class="col-sm-6">Valve Type :</dt>
                                    <dd class="col-sm-6">{{ $product->valve_type }}</dd>
                                    {{-- <dt class="col-sm-6">Primary Material of Construction :</dt>
                                    <dd class="col-sm-6">{{ $product->primary_material_of_construction }}</dd> --}}
                                    <dt class="col-sm-6">Actuation :</dt>
                                    <dd class="col-sm-6">{{ $product->actuation }}</dd>
                                    <dt class="col-sm-6">Body Material :</dt>
                                    <dd class="col-sm-6">{{ $product->body_material }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-6">Media :</dt>
                                    <dd class="col-sm-6">{{ $product->media }}</dd>
                                    <dt class="col-sm-6">Valve Size :</dt>
                                    <dd class="col-sm-6">{{ $product->valve_size }} {{ $product->valve_size_rate }}</dd>
                                    {{-- <dt class="col-sm-6">Flow :</dt>
                                    <dd class="col-sm-6">{{ $product->flow }}</dd> --}}
                                    <dt class="col-sm-6">Pressure Rating :</dt>
                                    <dd class="col-sm-6">{{ $product->pressure_rating }}</dd>
                                    <dt class="col-sm-6">HSN code :</dt>
                                    <dd class="col-sm-6">{{ $product->hsn_code }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-6">Media Temperature :</dt>
                                    <dd class="col-sm-6">{{ $product->media_temperature }} {{ $product->media_temperature_rate }}</dd>
                                    <dt class="col-sm-6">Product Code :</dt>
                                    <dd class="col-sm-6">{{ $product->product_code }}</dd>
                                    <dt class="col-sm-6">SKU Code :</dt>
                                    <dd class="col-sm-6">{{ $product->sku_code }}</dd>
                                    <dt class="col-sm-6">MRP :</dt>
                                    <dd class="col-sm-6">{{ $product->mrp }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header">Spare Parts</div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Size</th>
                                            <th scope="col">Weight</th>
                                            <th scope="col">Updated At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        

                                        @foreach($productItems as $key=>$sparePart)
                                        <tr>
                                            <th scope="row">{{ ($key+1) }}</th>
                                            <td>{{ $sparePart['spare_part']['name'] }}</td>
                                            <td>{{ $sparePart['quantity'] }}</td>
                                            <td>{{ $sparePart['spare_part']['type'] }}</td>
                                            <td>{{ $sparePart['spare_part']['size'] }}</td>
                                            <td>{{ $sparePart['spare_part']['weight'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sparePart['spare_part']['updated_at'])->format('Y-m-d') }}</td>

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
