@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Finished Product Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('finishedProducts.index') }}">Finished Products</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Product Information</h3>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Product Name:</th>
                                        <td>{{ $finishedProduct->product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity:</th>
                                        <td>{{ $finishedProduct->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created By:</th>
                                        <td>{{ $finishedProduct->creator->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At:</th>
                                        <td>{{ $finishedProduct->updated_at->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Parts Used</h3>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Part Name</th>
                                            <th>Quantity Per Unit</th>
                                            <th>Total Quantity Used</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productItems as $item)
                                            <tr>
                                                <td>{{ $item->sparePart->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->quantity * $finishedProduct->quantity }}</td>
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