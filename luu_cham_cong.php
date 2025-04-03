<?php 
// Create session
session_start();
include('../layouts/db_connection.php');
if (isset($_SESSION['username']) && isset($_SESSION['level'])) {
    // Include file
    include('../layouts/header.php');
    include('../layouts/topbar.php');
    include('../layouts/sidebar.php');

    // Khai báo mặc định
    $selectedEmployeeId = '';
    $selectedDate = '';
    $arrShow = [];

    // Lấy danh sách nhân viên để chọn
    $employeeListQuery = "SELECT id, ma_nv, ten_nv FROM quanly_nhansu.nhanvien";
    $employeeListResult = mysqli_query($conn, $employeeListQuery);
    
    // Xử lý kiểm tra yêu cầu kiểm tra chấm công theo nhân viên và ngày
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'check_attendance') {
        $selectedEmployeeId = $_POST['employee_id'];
        $selectedDate = $_POST['check_date'];

        // Lấy dữ liệu chấm công theo nhân viên và ngày được chọn
        $showData = "
    SELECT 
        nhanvien.ma_nv, 
        nhanvien.ten_nv, 
        chan_cong.check_in, 
        chan_cong.check_out 
    FROM 
        chan_cong 
    JOIN 
        quanly_nhansu.nhanvien 
    ON 
        chan_cong.employee_id = nhanvien.id 
    WHERE 
        DATE(chan_cong.check_in) = '$selectedDate' 
        AND chan_cong.employee_id = '$selectedEmployeeId' 
    ORDER BY 
        chan_cong.check_in ASC
";

$result = mysqli_query($conn, $showData);

if (!$result) {
    die("Lỗi thực thi truy vấn: " . mysqli_error($conn));
}

        $result = mysqli_query($conn, $showData);

        while ($row = mysqli_fetch_array($result)) {
            $arrShow[] = [
                'ma_nv' => $row['ma_nv'],
                'ten_nv' => $row['ten_nv'],
                'check_in' => $row['check_in'],
                'check_out' => $row['check_out'],
            ];
        }
    }

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Kiểm soát chấm công
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
            <li class="active">Kiểm soát chấm công</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Thông tin chấm công</h3>
                    </div>
                    <!-- /.box-header -->
                    
                    <div class="box-body">
                        <form action="" method="post" style="margin-bottom: 20px;">
                            <div class="form-group">
                                <label for="employee_id">Chọn nhân viên:</label>
                                <select name="employee_id" id="employee_id" class="form-control" required>
                                    <option value="">-- Chọn nhân viên --</option>
                                    <?php while ($employee = mysqli_fetch_array($employeeListResult)) { ?>
                                        <option value="<?php echo $employee['id']; ?>" <?php echo $selectedEmployeeId == $employee['id'] ? 'selected' : ''; ?>>
                                            <?php echo $employee['ten_nv']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="check_date">Chọn ngày:</label>
                                <input type="date" name="check_date" id="check_date" class="form-control" value="<?php echo $selectedDate; ?>" required>
                            </div>
                            <input type="hidden" name="action" value="check_attendance">
                            <button type="submit" class="btn btn-primary">Kiểm tra</button>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã nhân viên</th>
                                        <th>Tên nhân viên</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($arrShow as $arrS) {
                                    ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $arrS['ma_nv']; ?></td>
                                        <td><?php echo $arrS['ten_nv']; ?></td>
                                        <td><?php echo $arrS['check_in'] ? $arrS['check_in'] : 'Không có'; ?></td>
                                        <td><?php echo $arrS['check_out'] ? $arrS['check_out'] : 'Không có'; ?></td>
                                    </tr>
                                    <?php
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php
                        // Nếu có yêu cầu sao chép dữ liệu
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'copy_data') {
                            $copyData = "
                                INSERT INTO daily_working_hours (employee_id, work_date, check_in, check_out)
                                SELECT 
                                    employee_id,
                                    DATE(check_in) AS work_date, 
                                    MIN(check_in) AS check_in, 
                                    MAX(check_out) AS check_out
                                FROM 
                                    chan_cong
                                WHERE 
                                    check_out IS NOT NULL
                                GROUP BY 
                                    employee_id, work_date
                                HAVING 
                                    MIN(check_in) IS NOT NULL AND MAX(check_out) IS NOT NULL
                            ";

                            // Kiểm tra và thực hiện sao chép
                            if (mysqli_query($conn, $copyData)) {
                                echo "<div class='alert alert-success'>Sao chép dữ liệu thành công!</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Lỗi khi sao chép dữ liệu: " . mysqli_error($conn) . "</div>";
                            }
                        }
                        ?>
                        
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<?php
    // Include footer
    include('../layouts/footer.php');
} else {
    // Chuyển hướng đến trang đăng nhập nếu không xác thực
    header('Location: dang-nhap.php');
}
?>