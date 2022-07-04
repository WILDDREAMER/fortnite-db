<?php 
  
  $page_title="Manage User's Reports";
    
  include("includes/header.php");

	require("includes/function.php");
	require("language/language.php");

?>

<link rel="stylesheet" type="text/css" href="assets/css/stylish-tooltip.css">

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#wallpaper_report" aria-controls="wallpaper_report" role="tab" data-toggle="tab">Wallpaper Report</a></li>
            <li role="presentation"><a href="#gif_report" aria-controls="gif_report" role="tab" data-toggle="tab">GIF Report</a></li>
        </ul>
      
       <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="wallpaper_report">
            <div class="section">
              <div class="section-body">
                <div class="col-md-12 mrg-top">
                  <table class="datatable table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Sr.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Wallpaper</th>
                        <th>Report</th> 
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php 
                      $sql="SELECT report.*, wall.`image`, wall.`cat_id`, user.`name`, user.`email` FROM tbl_wallpaper wall, tbl_user_report report, tbl_users user WHERE wall.`id`=report.`parent_id` AND user.`id`=report.`user_id` AND report.`report_for`='wallpaper' AND report.`user_report_status`='1' ORDER BY report.`user_report_id` DESC";

                      $res=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

                      $no=1;
                      while($row=mysqli_fetch_assoc($res))
                      {
                          // print_r($row);
                    ?>
                        <tr>
                          <td><?=$no++;?></td>
                          <td style="word-wrap: break-word;"><?=$row['name']?></td>
                          <td style="word-wrap: break-word;"><?=$row['email']?></td>
                          <td>
                            <span class="mytooltip tooltip-effect-3">
                              <span class="tooltip-item">
                                <img src="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" style="width: 50px;height: 60px">
                              </span> 
                              <span class="tooltip-content clearfix">
                                <a href="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" target="_blank"><img src="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" /></a>
                              </span>
                            </span>
                              <!--  -->
                          </td>
                          <td style="word-wrap: break-word;"><?=$row['user_message']?></td>
                          <td>
                            <a href="javascript:void(0)" data-id="<?php echo $row['user_report_id']; ?>" data-toggle="tooltip" data-tooltip="Delete" class="btn btn-danger btn_delete">
                              <i class="fa fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                    <?php 
                      }
                    ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="gif_report">
            <div class="section">
              <div class="section-body">
                <div class="col-md-12 mrg-top">
                  <table class="datatable table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Sr.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>GIF</th>
                        <th>Report</th> 
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php 
                      $sql="SELECT report.*, gif.`image`, user.`name`, user.`email` FROM tbl_wallpaper_gif gif, tbl_user_report report, tbl_users user WHERE gif.`id`=report.`parent_id` AND user.`id`=report.`user_id` AND report.`report_for`='gif' AND report.`user_report_status`='1' ORDER BY report.`user_report_id` DESC";
                      $res=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

                      $no=1;
                      while($row=mysqli_fetch_assoc($res))
                      {
                    ?>
                        <tr>
                          <td><?=$no++;?></td>
                          <td style="word-wrap: break-word;"><?=$row['name']?></td>
                          <td style="word-wrap: break-word;"><?=$row['email']?></td>
                          <td>
                            <span class="mytooltip tooltip-effect-3">
                              <span class="tooltip-item">
                                <img src="images/animation/<?php echo $row['image'];?>" style="width: 50px;height: 60px">
                              </span> 
                              <span class="tooltip-content clearfix">
                                <a href="images/animation/<?php echo $row['image'];?>" target="_blank"><img src="images/animation/<?php echo $row['image'];?>" /></a>
                              </span>
                            </span>
                              <!--  -->
                          </td>
                          <td style="word-wrap: break-word;"><?=$row['user_message']?></td>
                          <td>
                            <a href="javascript:void(0)" data-id="<?php echo $row['user_report_id']; ?>" data-toggle="tooltip" data-tooltip="Delete" class="btn btn-danger btn_delete">
                              <i class="fa fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                    <?php 
                        }
                    ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>   

      </div>
    </div>
  </div>
</div>
 
        
<?php include("includes/footer.php");?>   

<script type="text/javascript">
  $('a[data-toggle="tooltip"]').tooltip({
    animated: 'fade',
    placement: 'bottom',
    html: true
  });

  $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
    localStorage.setItem('activeTab', $(e.target).attr('href'));
  });

  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

  $(".btn_delete").click(function(e){
    e.preventDefault();

    var _id=$(this).data("id");
    var _table='tbl_user_report';

    confirmDlg = duDialog('Are you sure?', 'All data will be removed which belong to this!', {
      init: true,
      dark: false, 
      buttons: duDialog.OK_CANCEL,
      okText: 'Proceed',
      callbacks: {
        okClick: function(e) {
          $(".dlg-actions").find("button").attr("disabled",true);
          $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> Please wait..');
          $.ajax({
            type:'post',
            url:'processData.php',
            dataType:'json',
            data: {'id[]': _id, for_action: 'delete', table: _table, 'action': 'multi_action'},
            success:function(res){
              location.reload();
            }
          });
        } 
      }
    });
    confirmDlg.show();
  });
</script>    
