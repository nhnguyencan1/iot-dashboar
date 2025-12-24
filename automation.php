<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Automation Rules - Smart Home HCMUT</title>

    <!-- Fonts & Icons -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    
    <!-- Core CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/toast-notifications.css" rel="stylesheet">
    <link href="css/smart-home.css" rel="stylesheet">
    
    <!-- Page-specific CSS -->
    <link href="css/automation.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img src="img/logo_bk.png" style="height:70px; width:auto;">
                </div>
                <div class="sidebar-brand-text mx-3">HCMUT HOME</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
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
            <li class="nav-item active">
                <a class="nav-link" href="automation.php">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Automation</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="sidebar-heading">
                Thông tin
            </div>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Hướng dẫn</span>
                </a>
            </li>
        </ul>
        <!-- End Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <span class="live-badge">
                        <i class="fas fa-circle" style="font-size: 6px;"></i> LIVE
                    </span>
                    <div class="page-header d-none d-sm-block"> 
                        <h1>Automation Rules</h1>
                        <p>Quản lý các quy tắc tự động hóa</p>
                    </div>
                    
                    <ul class="navbar-nav ml-auto">
                         <li class="nav-item mr-3">
                            <button class="btn btn-outline-secondary" onclick="manualRefresh()" id="refreshBtn" title="Làm mới">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#ruleModal" onclick="openCreateModal()">
                                <i class="fas fa-plus mr-2"></i>Thêm Rule
                            </button>
                        </li>
                    </ul>
                </nav>
                <!-- End Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">
                    
                    <!-- Stats Cards Row -->
                    <div class="row mb-4">
                        <!-- Total Rules -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng Rules</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statTotal">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Rules -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đang hoạt động</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statActive">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Triggered -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đã kích hoạt</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statTriggered">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today Count -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Hôm nay</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statToday">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Stats Cards -->

                    <div class="row">
                        <!-- Rules List Column -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="section-title">
                                <i class="fas fa-list"></i> Danh sách Rules
                            </div>
                            <div id="rulesContainer">
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    <p class="mt-3 text-muted">Đang tải...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Logs Column -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="section-title">
                                <i class="fas fa-history"></i> Lịch sử kích hoạt
                            </div>
                            <div class="card shadow">
                                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                    <div id="logsContainer">
                                        <p class="text-muted text-center">Đang tải...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Page Content -->

            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Smart Home HCMUT © 2025</span>
                    </div>
                </div>
            </footer>

        </div>
        <!-- End Content Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- Rule Modal -->
    <div class="modal fade" id="ruleModal" tabindex="-1" role="dialog" aria-labelledby="ruleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle mr-2"></i>Thêm Rule mới
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="ruleForm">
                        <input type="hidden" id="ruleId">
                        
                        <!-- Rule Name -->
                        <div class="form-group">
                            <label class="font-weight-bold" for="ruleName">Tên Rule <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ruleName" 
                                   placeholder="VD: Bật đèn khi tối" required maxlength="100">
                        </div>
                        
                        <!-- Description -->
                        <div class="form-group">
                            <label class="font-weight-bold" for="ruleDescription">Mô tả</label>
                            <textarea class="form-control" id="ruleDescription" rows="2" 
                                      placeholder="Mô tả chi tiết rule..." maxlength="255"></textarea>
                        </div>

                        <hr>
                        
                        <!-- Trigger Section -->
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-bolt mr-2"></i>ĐIỀU KIỆN KÍCH HOẠT (Trigger)
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="triggerType">Loại trigger <span class="text-danger">*</span></label>
                                    <select class="form-control" id="triggerType" required onchange="onTriggerTypeChange()">
                                        <option value="">-- Chọn --</option>
                                        <option value="time">⏰ Thời gian</option>
                                        <option value="temperature">🌡️ Nhiệt độ</option>
                                        <option value="humidity">💧 Độ ẩm</option>
                                        <option value="motion">🚶 Chuyển động</option>
                                        <option value="light">💡 Ánh sáng</option>
                                        <option value="fire">🔥 Phát hiện cháy</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="triggerOperator">Điều kiện</label>
                                    <select class="form-control" id="triggerOperator">
                                        <option value="=">=</option>
                                        <option value=">">&gt;</option>
                                        <option value="<">&lt;</option>
                                        <option value=">=">&gt;=</option>
                                        <option value="<=">&lt;=</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="triggerValue">Giá trị <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="triggerValue" 
                                           placeholder="VD: 30, dark, 18:00" required maxlength="50">
                                </div>
                            </div>
                        </div>

                        <!-- Trigger Help -->
                        <div id="triggerHelp" class="alert alert-info small">
                            <i class="fas fa-info-circle mr-2"></i>
                            Chọn loại trigger để xem hướng dẫn
                        </div>

                        <hr>
                        
                        <!-- Action Section -->
                        <h6 class="font-weight-bold text-success mb-3">
                            <i class="fas fa-play mr-2"></i>HÀNH ĐỘNG (Action)
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="actionType">Thiết bị <span class="text-danger">*</span></label>
                                    <select class="form-control" id="actionType" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="light1">💡 Đèn 1 (Phòng khách)</option>
                                        <option value="light2">💡 Đèn 2 (Phòng ngủ)</option>
                                        <option value="light3">💡 Đèn 3 (Nhà bếp)</option>
                                        <option value="light4">💡 Đèn 4 (Hành lang)</option>
                                        <option value="all_lights">💡 Tất cả đèn</option>
                                        <option value="door">🚪 Cửa</option>
                                        <option value="buzzer">🔔 Còi báo động</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="actionValue">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-control" id="actionValue" required>
                                        <option value="on">✅ BẬT (ON)</option>
                                        <option value="off">❌ TẮT (OFF)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Auto-Revert Section -->
                        <h6 class="font-weight-bold text-info mb-3">
                            <i class="fas fa-sync-alt mr-2"></i>TỰ ĐỘNG ĐẢO NGƯỢC
                        </h6>

                        <div class="form-group">
                            <div class="custom-control custom-switch custom-switch-lg">
                                <input type="checkbox" class="custom-control-input" id="autoRevert" name="auto_revert" value="1">
                                <label class="custom-control-label" for="autoRevert">
                                    <strong>Bật tự động đảo ngược hành động</strong>
                                    <small class="form-text text-muted">
                                        Khi bật, hệ thống sẽ tự động thực hiện hành động ngược lại khi điều kiện không còn đúng.
                                        <br>
                                        <strong>Ví dụ:</strong>
                                        <ul class="mb-0 mt-1">
                                            <li>Bật đèn khi có người → <strong>Tự động tắt đèn</strong> khi không có người</li>
                                            <li>Bật đèn khi tối → <strong>Tự động tắt đèn</strong> khi sáng</li>
                                            <li>Bật quạt khi nóng (>30°C) → <strong>Tự động tắt quạt</strong> khi mát (≤30°C)</li>
                                        </ul>
                                    </small>
                                </label>
                            </div>
                        </div>

                        <!-- Warning Alert -->
                        <div class="alert alert-warning" id="autoRevertWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Cảnh báo:</strong> Không nên bật tự động đảo ngược cho cảm biến lửa/báo cháy vì lý do an toàn.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveRule()">
                        <i class="fas fa-save mr-2"></i>Lưu Rule
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Rule Modal -->

    <!-- Core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Page-specific JavaScript -->
    <script src="js/automation.js"></script>

</body>
</html>