<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Smart Home HCMUT - System Logs</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/toast-notifications.css" rel="stylesheet">
    <link href="css/smart-home.css" rel="stylesheet">

    <style>
        /* Custom styles for logs table */
        .logs-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .logs-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .logs-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .logs-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e3e6f0;
            font-size: 13px;
        }

        .logs-table tbody tr:hover {
            background-color: #f8f9fc;
            transition: background-color 0.2s;
        }

        .logs-table code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        .logs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logs-header h3 {
            margin: 0;
            color: #5a5c69;
        }

        .logs-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .refresh-indicator {
            font-size: 12px;
            color: #858796;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .refresh-indicator i {
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

</head>

<body id="page-top" >

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img src="img/logo_bk.png" style="height:70px; width:auto;">
                </div>
                <div class="sidebar-brand-text mx-3">HCMUT HOME</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            
            <li class="nav-item active">
                <a class="nav-link" href="logs.php">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Logs</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="automation.php">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Automation</span>
                </a>
            </li>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-arrow-left mr-1"></i>
                                <span>Quay lại Dashboard</span>
                            </a>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="logs-header">
                        <div>
                            <h3>
                                <i class="fas fa-list-alt mr-2"></i>
                                System Logs
                            </h3>
                            <p class="text-muted mb-0" style="font-size: 13px;">
                                Real-time system logs monitoring
                            </p>
                        </div>

                        <div class="logs-controls">
                            <div class="refresh-indicator">
                                <i class="fas fa-sync-alt"></i>
                                <span>Auto-refresh: 3s</span>
                            </div>

                            <button id="btnClearLogs" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt mr-1"></i>
                                Xóa tất cả logs
                            </button>
                        </div>
                    </div>

                    <!-- Logs Table Card -->
                    <div class="card shadow mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="logs-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 160px;">
                                                <i class="fas fa-clock mr-1"></i>
                                                Time
                                            </th>
                                            <th style="width: 120px;">
                                                <i class="fas fa-server mr-1"></i>
                                                Source
                                            </th>
                                            <th style="width: 180px;">
                                                <i class="fas fa-tag mr-1"></i>
                                                Topic
                                            </th>
                                            <th style="width: 100px;">
                                                <i class="fas fa-flag mr-1"></i>
                                                Level
                                            </th>
                                            <th>
                                                <i class="fas fa-comment-dots mr-1"></i>
                                                Message
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="logs">
                                        <!-- Logs will be loaded here dynamically -->
                                        <tr>
                                            <td colspan="5" class="text-center text-muted" style="padding: 30px;">
                                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                                <br>
                                                Đang tải logs...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Info Cards 
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Auto Refresh
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Every 3s
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Logs Limit
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                50 Latest
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-database fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Sources
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ESP32 + Web
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Storage
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                MySQL DB
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                 End Page Content -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Smart Home HCMUT © 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- ⭐ LOGS PAGE SCRIPT -->
    <script src="js/logs.js"></script>

</body>

</html>