<?php 

// Tạo session
session_start();

if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
    // Include file
    include('../layouts/header.php');
    include('../layouts/topbar.php');
    include('../layouts/sidebar.php');

    // Đếm số lượng nhân viên
    $nv = "SELECT count(id) as soluong FROM nhanvien";
    $resultNV = mysqli_query($conn, $nv);
    $rowNV = mysqli_fetch_array($resultNV);
    $tongNV = $rowNV['soluong'];

    // Đếm số lượng nhân viên nghỉ việc
    $nghiViec = "SELECT count(id) as soluong FROM nhanvien WHERE trang_thai = 0";
    $resultNghiViec = mysqli_query($conn, $nghiViec);
    $rowNghiViec = mysqli_fetch_array($resultNghiViec);
    $tongNghiViec = $rowNghiViec['soluong'];

    // Đếm số phòng ban
    $pb = "SELECT count(id) as soluong FROM phong_ban";
    $resultPB = mysqli_query($conn, $pb);
    $rowPB = mysqli_fetch_array($resultPB);
    $tongPB = $rowPB['soluong'];

    // Đếm số tài khoản
    $tk = "SELECT count(id) as soluong FROM tai_khoan";
    $resultTK = mysqli_query($conn, $tk);
    $rowTK = mysqli_fetch_array($resultTK);
    $tongTK = $rowTK['soluong'];

    // Danh sách phòng ban
    $phongBan = "SELECT ma_phong_ban, ten_phong_ban, ngay_tao FROM phong_ban ORDER BY id DESC";
    $resultPhongBan = mysqli_query($conn, $phongBan);
    $arrPhongBan = array();
    while ($rowPhongBan = mysqli_fetch_array($resultPhongBan)) 
    {
        $arrPhongBan[] = $rowPhongBan;
    }

    // Danh sách chức vụ
    $chucVu = "SELECT ma_chuc_vu, ten_chuc_vu, ngay_tao FROM chuc_vu ORDER BY id DESC";
    $resultChucVu = mysqli_query($conn, $chucVu);
    $arrChucVu = array();
    while ($rowChucVu = mysqli_fetch_array($resultChucVu)) 
    {
        $arrChucVu[] = $rowChucVu;
    }

?>

<!-- Thêm CSS cải tiến -->
<style>
    body {
        background-color: #f4f6f9; /* Màu nền nhẹ */
        font-family: 'Arial', sans-serif;
    }
    .content-wrapper {
        background: #ffffff; /* Màu nền trắng cho content */
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .header-title {
        font-weight: bold; /* Đậm chữ tiêu đề */
        font-size: 24px;
        color: #333;
    }
    .small-box {
        border-radius: 10px; /* Góc bo tròn */
        padding: 20px;
        color: white;
        transition: transform 0.3s;
    }
    .small-box:hover {
        transform: scale(1.05); /* Hiệu ứng phóng to khi hover */
    }
    .bg-info { background-color: #17a2b8; }
    .bg-warning { background-color: #ffc107; }
    .bg-success { background-color: #28a745; }
    .bg-danger { background-color: #dc3545; }
    .table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }
    .table th, .table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #dee2e6; /* Đường viền bảng */
    }
    .table th {
        background-color: #007bff; /* Màu nền tiêu đề */
        color: white; /* Màu chữ tiêu đề */
    }
    .table-responsive {
        overflow-x: auto; /* Cho phép cuộn khi cần */
    }
    .badge {
        border-radius: 5px; /* Bo tròn badge */
        padding: 5px 10px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="header-title">
        QUẢN LÝ NHÂN SỰ
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
        <li class="active">Thống kê</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $tongNV; ?></h3>
              <p>Nhân viên</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="danh-sach-nhan-vien.php?p=staff&a=list-staff" class="small-box-footer">Danh sách nhân viên <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?php echo $tongPB; ?></h3>
              <p>Phòng ban</p>
            </div>
            <div class="icon">
              <i class="fa fa-bank"></i>
            </div>
            <a href="phong-ban.php?p=staff&a=room" class="small-box-footer">Danh sách phòng ban <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?php echo $tongTK; ?></h3>
              <p>Tài khoản người dùng</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="ds-tai-khoan.php?p=account&a=list-account" class="small-box-footer">Danh sách tài khoản <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?php echo $tongNghiViec; ?></h3>
              <p>Nhân viên nghỉ việc</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer" onclick="return false;">Danh sách nghỉ việc <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Main row -->
      <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Danh sách phòng ban</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã Phòng</th>
                                    <th>Tên phòng</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $count = 1;
                                    foreach ($arrPhongBan as $pb) 
                                    {
                                ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $pb['ma_phong_ban']; ?></td>
                                        <td><?php echo $pb['ten_phong_ban']; ?></td>
                                        <td><?php echo $pb['ngay_tao']; ?></td>
                                    </tr>
                                <?php
                                    $count++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Danh sách chức vụ</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã chức vụ</th>
                                    <th>Tên chức vụ</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $count = 1;
                                    foreach ($arrChucVu as $cv) 
                                    {
                                ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $cv['ma_chuc_vu']; ?></td>
                                        <td><?php echo $cv['ten_chuc_vu']; ?></td>
                                        <td><?php echo $cv['ngay_tao']; ?></td>
                                    </tr>
                                <?php
                                    $count++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </section>
</div>
<!-- /.content-wrapper -->
<?php
    // Include
    include('../layouts/footer.php');
}
else
{
    // Chuyển hướng đến trang đăng nhập
    header('Location: dang-nhap.php');
}
?>