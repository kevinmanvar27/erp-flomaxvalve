@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @include('dashboard.common')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                        <h3 class="card-title">Low Stock Spare Parts</h3>
                        <a href="/parts" class="btn btn-primary btn-sm float-right">View All Spare Parts</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10px">No.</th>
                                <th>Name</th>
                                <th style="width: 40px">Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if($lowStockSpareParts->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center">No Low Stock Spare Parts</td>
                                    </tr>
                                @else
                                    @foreach($lowStockSpareParts as $index => $part)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $part->name }}</td>
                                            <td><span class="badge bg-danger">{{ $part->qty }}</span></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
