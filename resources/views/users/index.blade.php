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
                            <a href="{{ route('users.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add User
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Users</li>
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
                            <div class="card-header">
                                <h3 class="card-title">Users</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="users-table" class="display">
                                    <thead>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>User Code</th>
                                        <th>Role</th>
                                        {{-- <th>Permissions</th> --}}
                                        <th>Actions</th>
                                    </thead>
                                    <tfoot>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>User Code</th>
                                        <th>Role</th>
                                        {{-- <th>Permissions</th> --}}
                                        <th>Actions</th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('users.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'usercode', name: 'usercode' },
                    { data: 'role', name: 'role' },
                    // { data: 'permissions', name: 'permissions' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
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

            // Confirm deletion with the user
            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }

            var form = $(this).closest('form');
            var url = form.attr('action');

            // Send the AJAX DELETE request
            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    alert(data.success || 'User deleted successfully.');

                    $('#users-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });

    </script>

@endsection
