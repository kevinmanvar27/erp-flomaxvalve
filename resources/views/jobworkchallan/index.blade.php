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
                            <a href="{{ route('jobworkchallans.create') }}">
                                <button type="button" class="btn btn-success">
                                    Add New Job Work
                                </button>
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Job Work Challan</li>
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
                                <h3 class="card-title">All Job Work Challans</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="jobWorkChallanTable" class="display">
                                    <thead>
                                        <th>ID</th>
                                        <th>Job Work Name</th>
                                        <th>Challan No</th>
                                        <th>Uploaded By</th>
                                        <th>PDF Files</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </thead>
                                    <tfoot>
                                        <th>ID</th>
                                        <th>Job Work Name</th>
                                        <th>Challan No</th>
                                        <th>Uploaded By</th>
                                        <th>PDF Files</th>
                                        <th>Created At</th>
                                        <th>Action</th>
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
            $('#jobWorkChallanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('jobworkchallans.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'job_work_name', name: 'job_work_name' },
                    { data: 'po_no', name: 'po_no' },
                    { data: 'uploaded_by', name: 'uploaded_by' },
                    { data: 'pdf_files', name: 'pdf_files' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
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
            if (!confirm('Are you sure you want to delete this product?')) {
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
                    alert(data.success || 'Product deleted successfully.');

                    $('#jobWorkChallanTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });

    </script>

@endsection
