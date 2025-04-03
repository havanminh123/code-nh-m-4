<?php 

// create session
session_start();

if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
  // include file
  include('../layouts/header.php');
  include('../layouts/topbar.php');
  include('../layouts/sidebar.php');


  // tao bien mac dinh
  $salaryCode = "ML" . time();

  // show data
  $nv = "SELECT id, ma_nv, ten_nv FROM nhanvien WHERE trang_thai <> 0";
  $resultNV = mysqli_query($conn, $nv);
  $arrNV = array();
  while($rowNV = mysqli_fetch_array($resultNV)){
    $arrNV[] = $rowNV;
  }

  // thang tinh luong
  $thang = date_create(date("Y-m-d"));
  $thangFormat = date_format($thang, "m/Y");

  // tinh luong nhan vien
  if(isset($_POST['tinhLuong']))
  {
    // tao cac gia tri mac dinh
    $showMess = false;
    $error = array();
    $success = array();

    // lay gia tri tren form
    $maNhanVien = $_POST['maNhanVien'];
    $soNgayCong = $_POST['soNgayCong'];
    $phuCap = $_POST['phuCap'];
    $tamUng = $_POST['tamUng'];
    $moTa = $_POST['moTa'];
    $ngayTinhLuong = $_POST['ngayTinhLuong'];
    $user_id = $row_acc['id'];
    $ngayTao = date("Y-m-d H:i:s");

    // validate
    if(empty($soNgayCong))
      $error['soNgayCong'] = 'error';
    if($maNhanVien == 'chon')
      $error['maNhanVien'] = 'error';
    if($phuCap == "")
      $error['phuCap'] = 'error';
    if(!empty($soNgayCong) && !is_numeric($soNgayCong))
      $error['kiemTraKieuSo'] = 'error';
    if(!empty($phuCap) && !is_numeric($phuCap))
      $error['phuCapSo'] = 'error';

    // lay luong ngay cua nhan vien theo chuc vu
    $luongNgay = "SELECT luong_ngay FROM nhanvien nv, chuc_vu cv WHERE nv.chuc_vu_id = cv.id AND nv.id = $maNhanVien";
    $resultLuongNgay = mysqli_query($conn, $luongNgay);
    $rowLuongNgay = mysqli_fetch_array($resultLuongNgay);
    $getLuongNgay = $rowLuongNgay['luong_ngay'];

    // tao bien thuc lanh
    $thucLanh = 0;

    // tinh luong co ban
    if($soNgayCong <= 25)
    {
      $luongThang = $soNgayCong * $getLuongNgay;
    }
    else
    {
      // neu lon hon 25 thi cac ngay con lai x2
      $luongThang = (25 + ($soNgayCong - 25)*2) * $getLuongNgay;
    }

    // tinh cac khoan phai nop lai
    // bao hiem xa hoi: 8%
    $baoHiemXaHoi = $luongThang * (8/100);
    // bao hiem y te : 1,5%
    $baoHiemYTe = $luongThang * (1.5/100);
    // bao hiem that nghiep
    $baoHiemThatNghiep = $luongThang * (1/100);
    // tinh tong cac khoan tru
    $tongKhoanTru = $baoHiemXaHoi + $baoHiemYTe + $baoHiemThatNghiep;

    // tam ung
    if((2/3*$luongThang) <= $tamUng)
    {
      $error['tamUngQuaLon'] = 'error';
      $tamUngChoPhep = 2/3*$luongThang;
    }

    // tinh thuc lanh
    $thucLanh = $luongThang + $phuCap - $tongKhoanTru - $tamUng;


    if(!$error)
    {
      // them vao db
      $insert = "INSERT INTO luong(ma_luong, nhanvien_id, luong_thang, ngay_cong, phu_cap, khoan_nop, tam_ung, thuc_lanh, ngay_cham, ghi_chu, nguoi_tao_id, ngay_tao, nguoi_sua_id, ngay_sua) VALUES('$salaryCode', $maNhanVien, $luongThang, $soNgayCong, $phuCap, $tongKhoanTru, $tamUng, $thucLanh, '$ngayTinhLuong', '$moTa', $user_id, '$ngayTao', $user_id, '$ngayTao')";
      $result = mysqli_query($conn, $insert);

      if($result)
      {
        $showMess = true;
        $success['success'] = 'Calculate salary Success';
        echo '<script>setTimeout("window.location=\'bang-luong.php?p=salary&a=salary\'",1000);</script>';
      }
      else
      {
        echo "<script>alert('Lõii');</script>";
      }
    }

  }

?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Calculate salary
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Overview</a></li>
        <li><a href="tinh-luong.php?p=salary&a=salary">Calculate salary</a></li>
        <li class="active">Calculate salary staff</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Calculate salary staff</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php 
                // show error
                if($row_acc['quyen'] != 1) 
                {
                  echo "<div class='alert alert-warning alert-dismissible'>";
                  echo "<h4><i class='icon fa fa-ban'></i> Notification!</h4>";
                  echo "You <b> do not have permission</b> to perform this function.";
                  echo "</div>";
                }
              ?>

              <?php 
                // show error
                if(isset($error2)) 
                {
                  if($showMess == false)
                  {
                    echo "<div class='alert alert-danger alert-dismissible'>";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
                    echo "<h4><i class='icon fa fa-ban'></i> Error!</h4>";
                    foreach ($error2 as $err2) 
                    {
                      echo $err2 . "<br/>";
                    }
                    echo "</div>";
                  }
                }
              ?>
              <?php 
                // show success
                if(isset($success)) 
                {
                  if($showMess == true)
                  {
                    echo "<div class='alert alert-success alert-dismissible'>";
                    echo "<h4><i class='icon fa fa-check'></i> Success!</h4>";
                    foreach ($success as $suc) 
                    {
                      echo $suc . "<br/>";
                    }
                    echo "</div>";
                  }
                }
              ?>
              <form action="" method="POST">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Code Salary: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" name="maLuong" value="<?php echo $salaryCode; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Staff: </label>
                      <select class="form-control" name="maNhanVien" id="idNhanVien">
                        <option value="chon">--- Select staff ---</option>
                        <?php 
                          foreach ($arrNV as $nv)
                          {
                            echo "<option value='".$nv['id']."'>" .$nv['ma_nv']. " - " .$nv['ten_nv']."</option>";
                          } 
                        ?>
                      </select>
                      <small style="color: red;"><?php if(isset($error['maNhanVien'])){ echo 'Please select staff'; } ?></small>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1"> Work day<span style="color: red;">*</span> : </label>
                      <input type="text" class="form-control" placeholder="Input  Work day" name="soNgayCong" value="<?php echo isset($_POST['soNgayCong']) ? $_POST['soNgayCong'] : ''; ?>" id="soNgayCong">
                      <small style="color: red;"><?php if(isset($error['soNgayCong'])){ echo ' Work day không được để trống'; } ?></small>
                      <small style="color: red;"><?php if(isset($error['kiemTraKieuSo'])){ echo 'Please input '; } ?></small>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Allowances (Allowances position, gas, lunch,...): </label>
                      <div class="row">
                        <div class="col-md-4">
                          <input type="text" class="form-control" placeholder="Select 'Calculate Allowances' để biết  money Allowances" name="phuCap" id="phuCap">
                        </div>
                        <div class="col-md-8">
                          <button type="button" class="btn btn-primary btn-flat" id="tinhPhuCap"><i class="fa fa-calculator"></i> Calculate Allowances</button>
                        </div>
                      </div>
                      <small style="color: red;"><?php if(isset($error['phuCap'])){ echo 'Please select Calculate Allowances'; } ?></small>
                      <small style="color: red;"><?php if(isset($error['phuCapSo'])){ echo 'Please input '; } ?></small>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Advance: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" name="tamUng" placeholder="Input  money muốn Advance" value="0">
                      <small style="color: red;"><?php if(isset($error['tamUngQuaLon'])){ echo 'Bạn đã Advance vượt quá 2/3 Salary month. Chỉ Advance tối đa: ' . number_format(ceil($tamUngChoPhep))."vnđ"; } ?></small>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">calculate salary date: </label>
                      <input type="date" class="form-control" id="exampleInputEmail1" placeholder="Input  money Allowances" name="ngayTinhLuong" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Description: </label>
                      <textarea id="editor1" rows="10" cols="80" name="moTa" class="ckeditor">
                      </textarea>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Creator: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" value="<?php echo $row_acc['ho']; ?> <?php echo $row_acc['ten']; ?>" name="nguoiTao" readonly>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Creation date: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" value="<?php echo date('d-m-Y H:i:s'); ?>" name="ngayTao" readonly>
                    </div>
                    <!-- /.form-group -->
                    <?php 
                      if($_SESSION['level'] == 1)
                        echo "<button type='submit' class='btn btn-primary' name='tinhLuong'><i class='fa fa-money'></i> Calculate salary staff</button>";
                    ?>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </form>
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
}
else
{
  // go to pages login
  header('Location: dang-nhap.php');
}

?>