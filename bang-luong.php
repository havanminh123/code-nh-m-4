<?php

// create session
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['level'])) {
  // include file
  include('../layouts/header.php');
  include('../layouts/topbar.php');
  include('../layouts/sidebar.php');

  // tinh thang tinh luong
  $thangTinhLuong = date_create(date("Y-m-d"));
  $thangTLFormat = date_format($thangTinhLuong, 'm/Y');


  if (isset($_POST['chiTietLuong'])) {
    $maNhanVien = $_POST['maNhanVien'];
    echo "<script>location.href='chi-tiet-luong.php?p=salary&a=salary&id=" . $maNhanVien . "'</script>";
  }

  if (isset($_POST['tinhLuong'])) {
    $id = $_POST['idNhanVien'];
    echo "<script>location.href='tinh-luong.php?p=salary&a=salary&id=" . $id . "'</script>";
  }

  // show data
  $showData = "SELECT ma_luong, hinh_anh, nv.id as idNhanVien, ten_nv, ten_chuc_vu, luong_thang, ngay_cong, phu_cap, khoan_nop, tam_ung, thuc_lanh, ngay_cham FROM luong l, nhanvien nv, chuc_vu cv WHERE nv.id = l.nhanvien_id AND nv.chuc_vu_id = cv.id ORDER BY l.id DESC";
  $result = mysqli_query($conn, $showData);
  $arrShow = array();
  while ($row = mysqli_fetch_array($result)) {
    $arrShow[] = $row;
  }

  // xoa record luong
  if (isset($_POST['delete'])) {
    $maLuong = $_POST['maLuong'];
    $xoaLuong = "DELETE FROM luong WHERE ma_luong = '$maLuong'";
    $resultXoaLuong = mysqli_query($conn, $xoaLuong);
    if ($resultXoaLuong) {
      $showMess = true;
      $success['success'] = 'Delete record Salary Success.';
      echo '<script>setTimeout("window.location=\'bang-luong.php?p=salary&a=salary\'",1000);</script>';
    }
  }

?>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <span style="font-size: 18px;">Notification</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="maLuong">
                    Do you really want to Delete This?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="delete">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Calculate salary staff
        </h1>
        <ol class="breadcrumb">
            <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Overview</a></li>
            <li><a href="bang-luong.php?p=salary&a=salary">Payroll</a></li>
            <li class="active">Calculate salary staff</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>SAL</h3>
                        <p>Calculate salary</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="tinh-luong.php?p=salary&a=salary" class="small-box-footer">
                        Nhấn vào để calculate salary <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>EXCEL</h3>
                        <p>Excel</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-file"></i>
                    </div>
                    <a href="export-bang-luong.php" class="small-box-footer">
                        Nhấn vào xuất file excel <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div> -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Payroll</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <div class="d-flex" style="margin-bottom: 15px; display: flex; justify-content: end;">
                        <a href="tinh-luong.php?p=salary&a=salary" class="btn btn-primary">
                          <i class="fa fa-plus" aria-hidden="true" style="margin-right: 5px;"></i>Calculate salary</a>
                        <a href="export-bang-luong.php" class="btn btn-success" style="margin-left: 7px;">
                          <i class="fa fa-file-excel-o" aria-hidden="true" style="margin-right: 5px;"></i>Excel
                        </a>
                      </div>
                        <?php
                        // show error
                        if ($row_acc['quyen'] != 1) {
                            echo "<div class='alert alert-warning alert-dismissible'>";
                            echo "<h4><i class='icon fa fa-ban'></i> Notification!</h4>";
                            echo 'You <b> do not have permission</b> to perform this function.';
                            echo '</div>';
                        }
                        ?>

                        <?php
                        // show error
                        if (isset($error)) {
                            if ($showMess == false) {
                                echo "<div class='alert alert-danger alert-dismissible'>";
                                echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
                                echo "<h4><i class='icon fa fa-ban'></i> Error!</h4>";
                                foreach ($error as $err) {
                                    echo $err . '<br/>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
                        <?php
                        // show success
                        if (isset($success)) {
                            if ($showMess == true) {
                                echo "<div class='alert alert-success alert-dismissible'>";
                                echo "<h4><i class='icon fa fa-check'></i> Success!</h4>";
                                foreach ($success as $suc) {
                                    echo $suc . '<br/>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Code Salary</th>
                                        <th>Username</th>
                                        <th>position</th>
                                        <th>Salary month</th>
                                        <th>Work day</th>
                                        <th>Truly cool</th>
                                        <th>Dot date</th>
                                        <th>Detail</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                    $count = 1;
                    foreach ($arrShow as $arrS) {
                    ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $arrS['ma_luong']; ?></td>
                                        <td><?php echo $arrS['ten_nv']; ?></td>
                                        <td><?php echo $arrS['ten_chuc_vu']; ?></td>
                                        <td><?php echo number_format($arrS['luong_thang']) . ' VNĐ'; ?></td>
                                        <td class="text-center"><?php echo $arrS['ngay_cong']; ?></td>
                                        <td class="text-danger" style="font-weight: bold;"><?php echo number_format($arrS['thuc_lanh']) . ' VNĐ'; ?></td>
                                        <td class="text-center">
                                            <?php echo date_format(date_create($arrS['ngay_cham']), 'd-m-Y'); ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row_acc['quyen'] == 1) {
                                                echo "<form method='POST'>";
                                                echo "<input type='hidden' value='" . $arrS['idNhanVien'] . "' name='maNhanVien'/>";
                                                echo "<button type='submit' class='btn btn-primary btn-flat'  name='chiTietLuong'><i class='fa fa-eye'></i></button>";
                                                echo '</form>';
                                            } else {
                                                echo "<button type='button' class='btn btn-primary btn-flat' disabled><i class='fa fa-eye'></i></button>";
                                            }
                                            ?>

                                        </td>
                                        <td>
                                            <?php
                                            if ($row_acc['quyen'] == 1) {
                                                echo "<button type='button' class='btn bg-maroon btn-flat' data-toggle='modal' data-target='#exampleModal' data-whatever='" . $arrS['ma_luong'] . "'><i class='fa fa-trash'></i></button>";
                                            } else {
                                                echo "<button type='button' class='btn bg-maroon btn-flat' disabled><i class='fa fa-trash'></i></button>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                      $count++;
                    }
                    ?>
                                </tbody>
                            </table>
                        </div>
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
  // include
  include('../layouts/footer.php');
} else {
  // go to pages login
  header('Location: dang-nhap.php');
}

?>
