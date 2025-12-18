<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Flomax - Inventory System') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

    <style>
        html::-webkit-scrollbar {
            width: 6px;
        }

        html::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        html::-webkit-scrollbar-thumb {
            background-color: #28a745;
            border-radius: 100%;
            border: 1px solid #f1f1f1;
        }

        html::-webkit-scrollbar-thumb:hover {
            background: #218838;
        }

        html {
            scrollbar-width: thin;
            scrollbar-color: #28a745 #f1f1f1;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #2eae4b !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" onclick="document.getElementById('logout-form').submit(); return false;"
                        class="nav-link">Logout</a>
                </li>

            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    @if (Auth::check())
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="far fa-user"></i> {{ Auth::user()->name }}
                        </a>
                    @endif
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="document.getElementById('logout-form').submit(); return false;"
                            class="dropdown-item ">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                       
                        <div class="dropdown-divider"></div>

                    </div>
                </li>

            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link" style="padding: 2px;">
                <span class="brand-text font-weight-light">
                    <img src="{{ asset('assets/flowmax1.png') }}" style="width: 247px;height: 51px;margin-top: 0px;" />
                </span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- SidebarSearch Form -->
                <div class="form-inline mt-2">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                @php
                    $permissions = session('user_permissions');
                    $role = session('user_role');
                    // dd($permissions);
                @endphp
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        @if (isset($role) && $role == 'admin')
                            <li
                                class="nav-item {{ request()->routeIs('users.index', 'users.create', 'users.show', 'users.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>
                                        Users
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('users.index', 'users.create', 'users.show', 'users.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('users.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New User</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('users.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Users</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (isset($permissions['parts']['read']) && $permissions['parts']['read'] == 1)
                            <li class="nav-item">
                                <a href="{{ route('parts.index') }}"
                                    class="nav-link {{ request()->routeIs('parts.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Parts
                                    </p>
                                </a>
                            </li>
                        @endif

                        @if (isset($permissions['products']['read']) && $permissions['products']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('products.index', 'products.create', 'products.show', 'products.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-box"></i>
                                    <p>
                                        Products
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('products.index', 'products.create', 'products.show', 'products.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('products.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New Product</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('products.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Product's</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- @if (isset($permissions['purchaseorder']['read']) && $permissions['purchaseorder']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('purchaseOrder.index', 'purchaseOrder.create', 'purchaseOrder.show', 'purchaseOrder.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-warehouse"></i>
                                    <p>
                                        Received Purchase Order
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('purchaseOrder.index', 'purchaseOrder.create', 'purchaseOrder.show', 'purchaseOrder.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('purchaseOrder.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New Purchase Order</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('purchaseOrder.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Purchase Order</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif --}}

                        @if (isset($permissions['finished products']['read']) && $permissions['finished products']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('finishedProducts.index', 'finishedProducts.create', 'finishedProducts.show', 'finishedProducts.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-box-open"></i>
                                    <p>
                                        Finished Products
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('finishedProducts.index', 'finishedProducts.create', 'finishedProducts.show', 'finishedProducts.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('finishedProducts.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add Finished Product</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('finishedProducts.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Finished Products</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        
                        @if (isset($permissions['create new purchase order']['read']) && $permissions['sales']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('newpurchaseorder.index', 'newpurchaseorder.create', 'newpurchaseorder.show', 'newpurchaseorder.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>
                                        Create Purchase Order
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('newpurchaseorder.index', 'newpurchaseorder.create', 'newpurchaseorder.show', 'newpurchaseorder.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('newpurchaseorder.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add Create Purchase Order</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('newpurchaseorder.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Create Purchase Order</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (isset($permissions['sales']['read']) && $permissions['sales']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('sales.index', 'sales.create', 'sales.show', 'sales.edit', 'sales.pending') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>
                                        Sales
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('sales.index', 'sales.create', 'sales.show', 'sales.edit', 'sales.pending') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('sales.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New Sales</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('sales.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Sales</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('sales.pending') }}" class="nav-link {{ request()->routeIs('sales.pending') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pending Report</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (isset($permissions['rejection']['read']) && $permissions['rejection']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('rejection.internalRejection', 'rejection.customerRejection', 'rejection.createCustomerRejection', 'rejection.createInternalRejection') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-recycle"></i>
                                    <p>
                                        Rejection
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('rejection.internalRejection', 'rejection.customerRejection', 'rejection.createCustomerRejection', 'rejection.createInternalRejection') ? 'block' : 'none' }};">
                                    {{-- <li class="nav-item">
                                        <a href="{{ route('rejection.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New Rejection</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('rejection.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Rejection</p>
                                        </a>
                                    </li> --}}
                                    <li class="nav-item">
                                        <a href="{{ route('rejection.internalRejection') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Internal Rejection</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('rejection.customerRejection') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Customer Rejection</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (isset($permissions['customer']['read']) && $permissions['customer']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('customer.index', 'customer.create', 'customer.show', 'customer.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        customer
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('customer.index', 'customer.create', 'customer.show', 'customer.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('customer.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New Customer</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('customer.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Customers</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (isset($permissions['support']['read']) && $permissions['support']['read'] == 1)
                            <li class="nav-item">
                                <a href="{{ route('support.index') }}"
                                    class="nav-link {{ request()->routeIs('support.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-ticket-alt"></i>
                                    <p>
                                        Support
                                    </p>
                                </a>
                            </li>
                        @endif

                
                        @if (isset($permissions['job work challan']['read']) && $permissions['job work challan']['read'] == 1)
                            <li
                                class="nav-item {{ request()->routeIs('jobworkchallans.index', 'jobworkchallans.create', 'jobworkchallans.show', 'jobworkchallans.edit') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-briefcase"></i>
                                    <p>
                                        Delivery Challan
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview"
                                    style="display: {{ request()->routeIs('jobworkchallans.index', 'jobworkchallans.create', 'jobworkchallans.show', 'jobworkchallans.edit') ? 'block' : 'none' }};">
                                    <li class="nav-item">
                                        <a href="{{ route('jobworkchallans.create') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Add New Delivery Challan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('jobworkchallans.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View Delivery Challan</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif


                        @if (isset($role) && $role == 'admin')
                            <li class="nav-item">
                                <a href="{{ route('settings.index') }}"
                                    class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Setting
                                    </p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>

                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <main class="py-4">
            @yield('content')
        </main>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2024 <a href="{{ route('dashboard') }}">Flowmax</a>.</strong>
            All rights reserved.
            {{-- <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
            </div> --}}
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->


    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('assets/plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('assets/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- Inventory System App -->
    <script src="{{ asset('assets/dist/js/adminlte.js') }}"></script>
    <!-- Inventory System for demo purposes -->
    <script src="{{ asset('assets/dist/js/demo.js') }}"></script>
    <!-- Inventory System dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('assets/dist/js/pages/dashboard.js') }}"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //Datemask dd/mm/yyyy
            $('#datemask').inputmask('dd/mm/yyyy', {
                'placeholder': 'dd/mm/yyyy'
            })
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', {
                'placeholder': 'mm/dd/yyyy'
            })
            //Money Euro
            $('[data-mask]').inputmask()

            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'L'
            });

            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                }
            });

            //Date range picker
            $('#reservation').daterangepicker()
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            })
            //Date range as a button
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                        'MMMM D, YYYY'))
                }
            )

            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })

            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            })

            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })

        })
        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })

        // DropzoneJS Demo Code Start
        Dropzone.autoDiscover = false

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template")
        previewNode.id = ""
        var previewTemplate = previewNode.parentNode.innerHTML
        previewNode.parentNode.removeChild(previewNode)

        var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
            url: "/target-url", // Set the url
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        })

        myDropzone.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(".start").onclick = function() {
                myDropzone.enqueueFile(file)
            }
        })

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {
            document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
        })

        myDropzone.on("sending", function(file) {
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1"
            // And disable the start button
            file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
        })

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function(progress) {
            document.querySelector("#total-progress").style.opacity = "0"
        })

        // Setup the buttons for all transfers
        // The "add files" button doesn't need to be setup because the config
        // `clickable` has already been specified.
        document.querySelector("#actions .start").onclick = function() {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
        }
        document.querySelector("#actions .cancel").onclick = function() {
            myDropzone.removeAllFiles(true)
        }
        // DropzoneJS Demo Code End
    </script>
</body>

</html>
