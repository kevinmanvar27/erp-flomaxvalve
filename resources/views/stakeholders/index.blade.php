@extends('layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="{{ route('customer.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add Customer
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Customer</li>
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
                    <div class="col-12">
                        <div class="card">
                         
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="StakeHolderssTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            {{-- <th>Last Name</th> --}}
                                            <th>Email</th>
                                            <th>User Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>First Name</th>
                                            {{-- <th>Last Name</th> --}}
                                            <th>Email</th>
                                            <th>User Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <script>
        $(document).ready(function() {
            $('#StakeHolderssTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('customer.data') }}',
                    dataSrc: function(json) {
                        if (json.message) {
                            // Display the "No data found" message
                            $('#StakeHolderssTable').html('<tr><td colspan="6" class="text-center">' +
                                json.message + '</td></tr>');
                            return [];
                        }
                        return json.data;
                    }
                },
                columns: [{
                        data: 'name'
                    },
                    // {
                    //     data: 'last_name'
                    // },
                    {
                        data: 'email'
                    },
                    {
                        data: 'user_type'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                paging: true,
                lengthChange: true,
                pageLength: 10,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
            });
           
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this Customer?')) {
                return;
            }

            var form = $(this).closest('form');
            var url = form.attr('action');

            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    alert('Customer deleted successfully');
                    $('#StakeHolderssTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting customer:', error);
                }
            });
        });
    </script>
@endsection
