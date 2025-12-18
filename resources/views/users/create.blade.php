@extends('layouts.app')
<style>
   
    .form-check-input{
        margin-left: 0.75rem !important;
    }

    .btn-primary{
        width: 100% !important;
    }
    .table thead{
        background:#928b8b80;
    }
    .table td{
        padding: 0.45rem !important;
    }
</style>
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Create Users</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
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
                    <div class="card-header">
                        <h4 class="card-title text-center">Create Users</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <!-- Row for user fields side by side -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">User Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="usercode">User Code</label>
                                        <input type="number" 
                                               name="usercode" 
                                               class="form-control" 
                                               placeholder="Enter user code" 
                                               value="{{ $nextUserCode }}" 
                                               min="1"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="role">User Role</label>
                                        <select name="role" class="form-control" required>
                                            <option value="admin">Admin</option>
                                            <option value="sales">Sales</option>
                                            <option value="account">Account</option>
                                            <option value="purchase">Purchase</option>
                                            <option value="production">Production</option>
                                            <option value="dispatch">Dispatch</option>
                                            <option value="assembly">Assembly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Permissions</label>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Module</th>
                                                    <th>Read</th>
                                                    <th>Write</th>
                                                    <th>Update</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Loop through modules -->
                                                @php
                                                    $modules = ['Parts', 'Products', 'PurchaseOrder', 'Sales', 'Rejection', 'Customer', 'Support','Setting','Job Work Challan', 'Create New Purchase Order', 'Finished Products'];
                                                @endphp
        
                                                @foreach($modules as $module)
                                                    <tr>
                                                        <td>{{ $module }}</td>
                                                        <td>
                                                            <input type="checkbox" name="permissions[{{ strtolower($module) }}][read]" value="1" class="form-check-input">
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="permissions[{{ strtolower($module) }}][write]" value="1" class="form-check-input">
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="permissions[{{ strtolower($module) }}][update]" value="1" class="form-check-input">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Centered Permissions Section -->
                            
                    
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Create User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userCodeInput = document.querySelector('input[name="usercode"]');
            
            userCodeInput.addEventListener('blur', function() {
                // Pad with leading zeros if less than 3 digits
                let value = this.value.trim();
                if (value !== '') {
                    value = String(parseInt(value)).padStart(3, '0');
                    this.value = value;
                }
            });
        
            // Format initial value with leading zeros
            if (userCodeInput.value) {
                userCodeInput.value = String(parseInt(userCodeInput.value)).padStart(3, '0');
            }
        });
        </script>
@endsection
