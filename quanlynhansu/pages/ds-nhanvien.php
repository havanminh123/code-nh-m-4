<?php 

// Create session
session_start();

if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
  // Include file
  include('../layouts/header.php');
  include('../layouts/topbar.php');
  include('../layouts/sidebar.php');

  if(isset($_POST['edit']))
  {
    $id = $_POST['idStaff'];
    echo "<script>location.href='sua-nhan-vien.php?p=staff&a=list-staff&id=".$id."'</script>";
  }

  if(isset($_POST['view']))
  {
    $id = $_POST['idStaff'];
    echo "<script>location.href='thong-tin-nhan-vien.php?p=staff&a=list-staff&id=".$id."'</script>";
  }

  // Show data
  $showData = "SELECT id, ma_nv, hinh_anh, ten_nv, gioi_tinh, ngay_tao, ngay_sinh, noi_sinh, so_cmnd, trang_thai FROM nhanvien ORDER BY id DESC";
  $result = mysqli_query($conn, $showData);
  $arrShow = array();
  while ($row = mysqli_fetch_array($result)) {
    $arrShow[] = $row;
  }

  // Delete record
  if(isset($_POST['delete']))
  {
    $id = $_POST['idStaff'];
    $target_dir = "../uploads/staffs/";

    // Get image
    $image = "SELECT hinh_anh FROM nhanvien WHERE id = $id";
    $resultImage = mysqli_query($conn, $image);
    $rowImage = mysqli_fetch_array($resultImage);
    $removeImage = $target_dir . $rowImage['hinh_anh'];

    $delete = "DELETE FROM nhanvien WHERE id = $id";
    $resultDel = mysqli_query($conn, $delete);
    if($resultDel)
    {
      $showMess = true;
      if($rowImage['hinh_anh'] != "demo-3x4.jpg")
      {
        unlink($removeImage);
      }

      $success['success'] = 'Xóa nhân viên thành công.';
      echo '<script>setTimeout("window.location=\'danh-sach-nhan-vien.php?p=staff&a=list-staff\'",1000);</script>';  
    }
  }

?>
  
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Thông báo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="idStaff">
            Bạn có thực sự muốn xóa nhân viên này?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
            <button type="submit" class="btn btn-danger" name="delete">Xóa</button>
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
        Nhân viên
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
        <li><a href="danh-sach-nhan-vien.php?p=staff&a=list-staff">Nhân viên</a></li>
        <li class="active">Danh sách nhân viên</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- Small box for adding staff -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3>Thêm NV</h3>
              <p>Thêm nhân viên</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-plus"></i>
            </div>
            <a href="them-nhan-vien.php?p=staff&a=add-staff" class="small-box-footer">
              Nhấn vào thêm nhân viên mới <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Danh sách nhân viên</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>STT</th>
                      <th>Mã nhân viên</th>
                      <th>Ảnh</th>
                      <th>Tên nhân viên</th>
                      <th>Giới tính</th>
                      <th>Ngày sinh</th>
                      <th>Nơi sinh</th>
                      <th>Số CMND</th>
                      <th>Tình trạng</th>
                      <th>Xem</th> 
                      <th>Sửa</th>
                      <th>Xóa</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $count = 1;
                    foreach ($arrShow as $arrS) 
                    {
                  ?>
                      <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $arrS['ma_nv']; ?></td>
                        <td><img src="../uploads/staffs/<?php echo $arrS['hinh_anh']; ?>" width="80"></td>
                        <td><?php echo $arrS['ten_nv']; ?></td>
                        <td><?php echo $arrS['gioi_tinh'] == 1 ? "Nam" : "Nữ"; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($arrS['ngay_sinh'])); ?></td>
                        <td><?php echo $arrS['noi_sinh']; ?></td>
                        <td><?php echo $arrS['so_cmnd']; ?></td>
                        <td><?php echo $arrS['trang_thai'] == 1 ? '<span class="badge bg-blue">Đang làm việc</span>' : '<span class="badge bg-red">Đã nghỉ việc</span>'; ?></td>
                        <td>
                          <form method='POST'>
                            <input type='hidden' value='<?php echo $arrS['id']; ?>' name='idStaff'/>
                            <button type='submit' class='btn btn-info' name='view'><i class='fa fa-eye'></i></button>
                          </form>
                        </td>
                        <td>
                          <form method='POST'>
                            <input type='hidden' value='<?php echo $arrS['id']; ?>' name='idStaff'/>
                            <button type='submit' class='btn btn-warning' name='edit'><i class='fa fa-edit'></i></button>
                          </form>
                        </td>
                        <td>
                          <button type='button' class='btn btn-danger' data-toggle='modal' data-target='#exampleModal' data-whatever='<?php echo $arrS['id']; ?>'><i class='fa fa-trash'></i></button>
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
  // Include footer
  include('../layouts/footer.php');
}
else
{
  // Go to login page
  header('Location: dang-nhap.php');
}

?>