<?php 
    
    $page_title="Manage GIF";

    include("includes/header.php");

    require("includes/function.php");
    require("language/language.php");

    $tableName="tbl_wallpaper_gif";   
    $targetpage = "manage_wallpaper_animation.php"; 
    $limit = 12;

    $keyword='';

    if(!isset($_GET['keyword'])){
      $query = "SELECT COUNT(*) as num FROM $tableName";
    }
    else{

      $keyword=addslashes(trim($_GET['keyword']));

      $query="SELECT COUNT(*) as num FROM tbl_wallpaper_gif
        WHERE tbl_wallpaper_gif.`gif_tags` LIKE '%$keyword%' ORDER BY tbl_wallpaper_gif.`id`";

      $targetpage = "manage_wallpaper_animation.php?keyword=".$_GET['keyword'];

    }

    $total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
    $total_pages = $total_pages['num'];

    $stages = 3;
    $page=0;
    if(isset($_GET['page'])){
      $page = mysqli_real_escape_string($mysqli,$_GET['page']);
    }
    if($page){
      $start = ($page - 1) * $limit; 
    }else{
      $start = 0; 
    } 

    if(!isset($_GET['keyword'])){
      $sql_query="SELECT * FROM tbl_wallpaper_gif ORDER BY tbl_wallpaper_gif.`id` DESC LIMIT $start, $limit"; 
    }
    else{

      $sql_query="SELECT * FROM tbl_wallpaper_gif WHERE tbl_wallpaper_gif.`gif_tags` LIKE '%$keyword%' ORDER BY tbl_wallpaper_gif.`id`"; 
    }

    $result=mysqli_query($mysqli,$sql_query) or die(mysqli_error($mysqli));
?>

<div class="row">
  <div class="col-xs-12">
    <div class="card mrg_bottom">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-7 col-xs-12">
          <div class="search_list">
            <div class="search_block">
              <form method="get" id="searchForm" action="">
                <input class="form-control input-sm" placeholder="Search here..." aria-controls="DataTables_Table_0" type="search" name="keyword" value="<?php if(isset($_GET['keyword'])){ echo $_GET['keyword'];} ?>" required="required">
                <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
              </form> 
            </div> 
            <div class="add_btn_primary"> <a href="add_wallpaper_animation.php">Add GIF</a> </div>
          </div>
        </div>
        <div class="col-md-3 col-xs-12 text-right" style="float: right;">
          <div>
              <div class="checkbox" style="width: 95px;margin-top: 5px;margin-left: 10px;right: 100px;position: absolute;">
                <input type="checkbox" id="checkall_input">
                <label for="checkall_input">
                    Select All
                </label>
              </div>
              <div class="dropdown" style="float:right">
                <button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown">Action
                <span class="caret"></span></button>
                <ul class="dropdown-menu" style="right:0;left:auto;">
                  <li><a href="" class="actions" data-action="delete">Delete</a></li>
                </ul>
              </div>
          </div>
        </div>
      </div>

        <div class="clearfix"></div>
          <div class="col-md-12 mrg-top">
            <div class="loader-container text-center">
              <div class="icon">
                  <div class="sk-wave">
                      <div class="sk-rect sk-rect1"></div>
                      <div class="sk-rect sk-rect2"></div>
                      <div class="sk-rect sk-rect3"></div>
                      <div class="sk-rect sk-rect4"></div>
                      <div class="sk-rect sk-rect5"></div>
                    </div>
              </div>
              <div class="title">Loading</div>
            </div>
            <div class="row">
              <?php 
              $i=0;
              while($row=mysqli_fetch_array($result))
              {         
                ?>  
                <div class="col-lg-3 col-sm-6 col-xs-12">
                  <div class="block_wallpaper">
                    <div class="wall_category_block">
                      <div class="checkbox" style="float: right;">
                        <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i; ?>" value="<?php echo $row['id']; ?>" class="post_ids">
                        <label for="checkbox<?php echo $i; ?>">
                        </label>
                      </div>
                    </div>

                    <div class="wall_image_title">
                      <ul>
                        <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?php echo $row['total_views'];?> Views"><i class="fa fa-eye"></i></a></li>                      
                        <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?php echo $row['total_download'];?> Download"><i class="fa fa-download"></i></a></li>
                        <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?php echo $row['rate_avg'];?> Rating"><i class="fa fa-star"></i></a></li>

                        <li><a href="edit_wallpaper_animation.php?wallpaper_id=<?php echo $row['id'];?>" data-toggle="tooltip" data-tooltip="Edit"><i class="fa fa-edit"></i></a></li>

                        <li>
                          <a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" class="btn_delete_a" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a>
                        </li>

                      </ul>
                    </div>
                    <div><img src="images/animation/<?php echo $row['image'];?>" /></div>
                  </div>
                </div>
                <?php

                $i++;
              }
              ?>      

            </div>
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="pagination_item_block">
              <nav>
                <?php include("pagination.php")?>
              </nav>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>

<?php include("includes/footer.php");?>       

<script type="text/javascript">
  $("#searchForm").submit(function(e){
      $(".loader-container").parents("div").addClass("__loading");
  });

  $(".actions").click(function(e){
    e.preventDefault();

    var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });
    var _action=$(this).data("action");

    if(_ids!='')
    {
      confirmDlg = duDialog('Action: '+$(this).text(), 'Do you really want to perform?', {
        init: true,
        dark: false, 
        buttons: duDialog.OK_CANCEL,
        okText: 'Proceed',
        callbacks: {
          okClick: function(e) {
            $(".dlg-actions").find("button").attr("disabled",true);
            $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> Please wait..');
            var _table='tbl_wallpaper_gif';

            $.ajax({
              type:'post',
              url:'processData.php',
              dataType:'json',
              data:{id:_ids,for_action:_action,table:_table,'action':'multi_action'},
              success:function(res){
                $('.notifyjs-corner').empty();
                if(res.status=='1'){
                  location.reload();
                }
              }
            });

          } 
        }
      });
      confirmDlg.show();
    }
    else{
      infoDlg = duDialog('Opps!', 'No data selected', { init: true });
      infoDlg.show();
    }
  });

  $(".btn_delete_a").click(function(e){
    e.preventDefault();

    var _id=$(this).data("id");
    var _table='tbl_wallpaper_gif';

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

  var totalItems=0;

  $("#checkall_input").click(function () {

    totalItems=0;

    $("input[name='post_ids[]']").prop('checked', this.checked);

    $.each($("input[name='post_ids[]']:checked"), function(){
      totalItems=totalItems+1;
    });


    if($("input[name='post_ids[]']").prop("checked") == true){
      $('.notifyjs-corner').empty();
      $.notify(
        'Total '+totalItems+' item checked',
        { position:"top center",className: 'success'}
      );
    }
    else if($("input[name='post_ids[]']").prop("checked") == false){
      totalItems=0;
      $('.notifyjs-corner').empty();
    }
  });

  var noteOption = {
      clickToHide : false,
      autoHide : false,
  }

  $.notify.defaults(noteOption);

  $(".post_ids").click(function(e){

      if($(this).prop("checked") == true){
        totalItems=totalItems+1;
      }
      else if($(this). prop("checked") == false){
        totalItems = totalItems-1;
      }

      if(totalItems==0){
        $('.notifyjs-corner').empty();
        exit();
      }

      $('.notifyjs-corner').empty();

      $.notify(
        'Total '+totalItems+' item checked',
        { position:"top center",className: 'success'}
      );
  });
</script>