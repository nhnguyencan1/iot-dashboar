<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Smart Home Dashboard - HCMUT">
    <meta name="author" content="HCMUT">

    <title>Smart Home HCMUT - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/toast-notifications.css" rel="stylesheet">
    <link href="css/smart-home.css" rel="stylesheet">
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
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logs.php">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Logs</span>
                </a>
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

                    <!-- Page Header -->
                    <div class="page-header d-none d-sm-block">
                        <h1>Dashboard</h1>
                        <p>Giám sát & Điều khiển nhà thông minh</p>
                    </div>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <button id="btnBuzzerOff" type="button" class="btn-buzzer" disabled>
                                <i class="fas fa-bell-slash"></i>
                                <span>Tắt còi báo cháy</span>
                            </button>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- ===================== SECTION: SENSORS ===================== -->
                    <div class="section-title">
                        <i class="fas fa-chart-line"></i> Dữ liệu cảm biến
                    </div>

                    <div class="row">
                        <!-- Date & Time -->
                        <div class="col-xl-2 col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="sensor-icon icon-success">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="sensor-label text-success">Ngày & Giờ</div>
                                    <div class="sensor-value" id="timeValue">--:--:--</div>
                                    <div class="small text-gray-500" id="dateValue">--/--/----</div>
                                </div>
                            </div>
                        </div>

                        <!-- Temperature -->
                        <div class="col-xl-2 col-md-4 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="sensor-icon icon-danger">
                                        <i class="fas fa-thermometer-half"></i>
                                    </div>
                                    <div class="sensor-label text-danger">Nhiệt độ</div>
                                    <div class="sensor-value" id="tempValue">-- °C</div>
                                </div>
                            </div>
                        </div>

                        <!-- Humidity -->
                        <div class="col-xl-2 col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="sensor-icon icon-primary">
                                        <i class="fas fa-tint"></i>
                                    </div>
                                    <div class="sensor-label text-primary">Độ ẩm</div>
                                    <div class="sensor-value" id="humiValue">-- %</div>
                                </div>
                            </div>
                        </div>

                        <!-- Flame -->
                        <div class="col-xl-2 col-md-4 mb-4">
                            <div id="fireCard" class="card border-left-warning shadow h-100 py-2 fire-normal">
                                <div class="card-body">
                                    <div class="sensor-icon icon-warning">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="sensor-label text-warning">Lửa</div>
                                    <div class="sensor-value" id="fireValue">--</div>
                                </div>
                            </div>
                        </div>

                        <!-- Motion -->
                        <div class="col-xl-2 col-md-4 mb-4">
                            <div id="motionCard" class="card border-left-info shadow h-100 py-2 motion-normal">
                                <div class="card-body">
                                    <div class="sensor-icon icon-info">
                                        <i class="fas fa-walking"></i>
                                    </div>
                                    <div class="sensor-label text-info">Chuyển động</div>
                                    <div class="sensor-value" id="motionValue">--</div>
                                </div>
                            </div>
                        </div>

                        <!-- Light Sensor -->
                        <div class="col-xl-2 col-md-4 mb-4">
                            <div id="lightCard" class="card border-left-secondary shadow h-100 py-2 light-normal">
                                <div class="card-body">
                                    <div class="sensor-icon icon-secondary">
                                        <i class="fas fa-sun"></i>
                                    </div>
                                    <div class="sensor-label text-secondary">Ánh sáng</div>
                                    <div class="sensor-value" id="lightValue">--</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== SECTION: CONTROLS ===================== -->
                    <div class="section-title">
                        <i class="fas fa-sliders-h"></i> Bảng điều khiển
                    </div>

                    <div class="row">

                        <!-- LEFT: Fingerprint Control -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header card-header-gradient py-3">
                                    <h6 class="m-0 font-weight-bold text-white">
                                        <i class="fas fa-fingerprint mr-2"></i>Điều khiển Vân Tay
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label-sm">ID người dùng</label>
                                        <input type="number" class="form-control" id="fingerID" placeholder="Nhập ID (1-127)">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label-sm">Tên người dùng</label>
                                        <input type="text" class="form-control" id="fingerName" placeholder="Nhập tên người dùng">
                                    </div>

                                    <button id="btnEnroll" class="btn btn-gradient-primary btn-block mb-2">
                                        <i class="fas fa-user-plus mr-2"></i> ENROLL
                                    </button>

                                    <button id="btnDelete" class="btn btn-gradient-danger btn-block mb-2">
                                        <i class="fas fa-user-minus mr-2"></i> DELETE
                                    </button>

                                    <button id="btnDeleteAll" class="btn btn-secondary btn-block">
                                        <i class="fas fa-trash mr-2"></i> DELETE ALL
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- CENTER: Door & Light Control -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header card-header-gradient py-3">
                                    <h6 class="m-0 font-weight-bold text-white">
                                        <i class="fas fa-door-open mr-2"></i>Điều khiển Thiết bị
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Door -->
                                    <div class="mb-4">
                                        <label class="form-label-sm">Cửa chính</label>
                                        <button id="btnOpenDoor" type="button" class="btn btn-gradient-success btn-block">
                                            <i class="fas fa-door-open mr-2"></i> MỞ CỬA
                                        </button>
                                    </div>

                                    <!-- Lights -->
                                    <label class="form-label-sm">Đèn</label>
                                    
                                    <button id="btnLight1" class="btn btn-light btn-block light-btn light-off mb-2" data-state="off">
                                        <i class="far fa-lightbulb mr-2"></i> ĐÈN 1: OFF
                                    </button>

                                    <button id="btnLight2" class="btn btn-light btn-block light-btn light-off mb-2" data-state="off">
                                        <i class="far fa-lightbulb mr-2"></i> ĐÈN 2: OFF
                                    </button>

                                    <button id="btnLight3" class="btn btn-light btn-block light-btn light-off mb-2" data-state="off">
                                        <i class="far fa-lightbulb mr-2"></i> ĐÈN 3: OFF
                                    </button>

                                    <button id="btnLight4" class="btn btn-light btn-block light-btn light-off" data-state="off">
                                        <i class="far fa-lightbulb mr-2"></i> ĐÈN 4: OFF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Camera -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card camera-card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-video mr-2"></i>Camera giám sát
                                        <span class="live-badge">LIVE</span>
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="camera-feed">
                                        <img src="http://127.0.0.1:5000/video" alt="Camera Feed" style="max-width:100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== SECTION: FINGERPRINT DATA ===================== -->
                    <div class="section-title">
                        <i class="fas fa-database"></i> Quản lý Vân tay
                    </div>

                    <div class="row">

                        <!-- LEFT: Danh sách vân tay -->
                        <div class="col-xl-4 col-lg-4 col-md-12 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header card-header-gradient bg-success-gradient py-3">
                                    <h6 class="m-0 font-weight-bold text-white">
                                        <i class="fas fa-users mr-2"></i>Danh sách đã đăng ký
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-scroll">
                                        <ul class="list-group list-group-enhanced" id="fingerList">
                                            <li class="list-group-item text-muted">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Đang tải danh sách...
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Lịch sử quét vân tay -->
                        <div class="col-xl-8 col-lg-8 col-md-12 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header card-header-gradient bg-info-gradient py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-white">
                                        <i class="fas fa-history mr-2"></i>Lịch sử quét vân tay
                                    </h6>
                                    <button id="btnClearFingerLogs" class="btn btn-sm btn-light">
                                        <i class="fas fa-trash-alt mr-1"></i>Xóa lịch sử
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-scroll">
                                        <table class="table table-bordered table-sm table-enhanced" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>ID</th>
                                                    <th>Tên</th>
                                                    <th>Sự kiện</th>
                                                </tr>
                                            </thead>
                                            <tbody id="fingerLogTable">
                                                <tr>
                                                    <td colspan="4" class="text-muted text-center">
                                                        <i class="fas fa-spinner fa-spin mr-2"></i>Đang tải dữ liệu...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ===================== SECTION: CHART ===================== -->
                    <div class="section-title">
                        <i class="fas fa-chart-area"></i> Biểu đồ thống kê
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line mr-2"></i>Nhiệt độ & Độ ẩm
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="tempHumChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Page Content -->

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

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/paho-mqtt.js"></script>
    <script src="js/main.js"></script>
    <script src="js/chart-temp-hum.js?v=1"></script>

</body>

</html>