<?php 

$page_title="Manage Wallpaper";

include("includes/header.php");

require("includes/function.php");
require("language/language.php");

$tableName="tbl_wallpaper";   
$targetpage = "manage_wallpaper.php"; 
$limit = 12;

$where='';

$keyword='';

if(isset($_GET['filter_type'])){

  $targetpage='manage_wallpaper.php?';

  foreach ($_GET['filter_type'] as $key => $value){

    $where.="`wallpaper_type`='".$value."' OR ";
    $targetpage .= "filter_type[]=".$_GET['filter_type'][$key].'&'; 

  }

  $where=rtrim($where,'OR ');

  $targetpage=rtrim($targetpage,'&');

  $query = "SELECT COUNT(*) as num FROM $tableName WHERE $where";

  if(isset($_GET['color'])){

    $color=$_GET['color'];

    $targetpage=$targetpage.'&color='.$_GET['color'];

    $query = "SELECT COUNT(*) as num FROM $tableName WHERE ($where) AND FIND_IN_SET($color,`wall_colors`)";
  }
}
else if(isset($_GET['color'])){

  $color=$_GET['color'];

  $targetpage=$targetpage.'?color='.$_GET['color'];

  $query = "SELECT COUNT(*) as num FROM $tableName WHERE FIND_IN_SET($color,`wall_colors`)";
}
else if(isset($_GET['keyword'])){

  $keyword=addslashes(trim($_GET['keyword']));

  $targetpage=$targetpage.'?keyword='.$keyword;

  $query = "SELECT COUNT(*) as num FROM $tableName WHERE `wall_tags` LIKE '%$keyword%'";
}
else{
  $query = "SELECT COUNT(*) as num FROM $tableName WHERE `wall_tags` LIKE '%$keyword%'";
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


$where='';

if(isset($_GET['filter_type'])){

  $targetpage='manage_wallpaper.php?';

  foreach ($_GET['filter_type'] as $key => $value){

    $where.="`wallpaper_type`='".$value."' OR ";
    $targetpage .= "filter_type[]=".$_GET['filter_type'][$key].'&'; 

  }

  $where=rtrim($where,'OR ');

  $targetpage=rtrim($targetpage,'&');

  $wall_qry="SELECT * FROM tbl_wallpaper
  LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`=tbl_category.`cid` 
  WHERE ($where)
  ORDER BY tbl_wallpaper.`id` DESC LIMIT $start, $limit";

  if(isset($_GET['color'])){

    $color=$_GET['color'];

    $wall_qry="SELECT * FROM tbl_wallpaper
    LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`=tbl_category.`cid` 
    WHERE ($where) AND FIND_IN_SET($color,`wall_colors`)
    ORDER BY tbl_wallpaper.`id` DESC LIMIT $start, $limit";

  }
}
else if(isset($_GET['color'])){

  $color=$_GET['color'];

  $wall_qry="SELECT * FROM tbl_wallpaper
  LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`=tbl_category.`cid` 
  WHERE FIND_IN_SET($color,`wall_colors`)
  ORDER BY tbl_wallpaper.`id` DESC LIMIT $start, $limit";

}
else if(isset($_GET['keyword'])){

  $keyword=addslashes(trim($_GET['keyword']));

  $wall_qry="SELECT * FROM tbl_wallpaper
  LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`=tbl_category.`cid`
  WHERE tbl_wallpaper.`wall_tags` LIKE '%$keyword%' 
  ORDER BY tbl_wallpaper.`id` DESC LIMIT $start, $limit";
}
else{

  $wall_qry="SELECT * FROM tbl_wallpaper
  LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`=tbl_category.`cid` 
  ORDER BY tbl_wallpaper.`id` DESC LIMIT $start, $limit";
}

$result=mysqli_query($mysqli,$wall_qry); 

?>

<style type="text/css">

.filter_color{
  -webkit-appearance: none;
  -moz-appearance: none;
  text-indent: 1px;
  text-overflow: '';
  font-family: FontAwesome;

}
.filter_color option span{
  color: red !important;
  background: red
}

ul.filter_color { 
  font-family: 'FontAwesome';
  height: 36px;
  width: 150px;
  z-index: 9999;
  text-align: left;
  cursor: pointer;
}
ul.filter_color li span{
  font-family: "FontAwesome";
}
ul.filter_color li{
  font-family: "Poppins", sans-serif;
  border-radius:6px;
}
ul.filter_color li:first-child{
  border:1px solid #999;
}
ul.filter_color li:last-child{
  border-bottom:1px solid #999;
}
ul.filter_color li { padding: 7px 10px; z-index: 2;border-left: 1px solid #ccc;border-right: 1px solid #ccc;}
ul.filter_color li:not(.init) { border-radius:0px;float: left; border-bottom:1px solid #dfe6e8; width: 150px; display: none; background: #f9f9f9; }
ul.filter_color li:not(.init):hover, ul li.selected:not(.init) { border-radius:0px;background: #e91e63;color:#fff;}

.color-drops{
  width: 20px;
  height: 20px;
  border-radius: 50%;
  float: left;
  margin: 5px -10px 5px 3px;
  text-align:center;
  box-shadow:1px 0px 10px #000;
  transition: all linear .2s;
}
.color-drops:hover{
  transform: scale(1.2);
}


</style>

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
            <div class="add_btn_primary pull-right"> <a href="add_wallpaper.php?redirect=<?=$redirectUrl?>">Add Wallpaper</a> </div>
          </div>
        </div>
        <div class="rows">
          <div class="col-md-9">
            <form id="filterForm" accept="" method="GET">

              <input type="hidden" name="color" value="<?=isset($_GET['color']) ? $_GET['color'] : ''?>">

              <div class="col-md-6 col-xs-12" style="padding-left: 0px">
                <div class="col-md-4 col-xs-6">
                  <div class="checkbox">
                    <input type="checkbox" name="filter_type[]" id="portrait_check" value="Portrait" class="filter" <?php if(isset($_GET['filter_type']) && in_array('Portrait',$_GET['filter_type'])){ echo 'checked';} ?>>
                    <label for="portrait_check">
                      Portrait
                    </label>
                  </div> 
                </div>
                <div class="col-md-4 col-xs-6">
                  <div class="checkbox">
                    <input type="checkbox" name="filter_type[]" id="landscape_check" value="Landscape" class="filter" <?php if(isset($_GET['filter_type']) && in_array('Landscape',$_GET['filter_type'])){ echo 'checked';} ?>>
                    <label for="landscape_check">
                      Landscape
                    </label>
                  </div> 
                </div>
                <div class="col-md-4 col-xs-6">
                  <div class="checkbox">
                    <input type="checkbox" name="filter_type[]" id="square_check" value="Square" class="filter" <?php if(isset($_GET['filter_type']) && in_array('Square',$_GET['filter_type'])){ echo 'checked';} ?>>
                    <label for="square_check">
                      Square
                    </label>
                  </div> 
                </div>
              </div>
              <div class="col-md-4 col-xs-12">
                <div class="col-md-6 col-xs-12">
                  <div class="search_list" style="padding: 0px 0px 5px;float: left;">
                    <ul class="list-unstyled filter_color">
                      <li class="init">Color Filter</li>
                      <?php 
                      $sql="SELECT * FROM tbl_color WHERE color_status='1'";
                      $res=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
                      while($data=mysqli_fetch_assoc($res))
                      {
                        ?>
                        <li data-value="<?=$data['color_id']?>"><span style="color:<?=$data['color_code']?>">&#xf0c8;</span>&nbsp;&nbsp;<?=$data['color_name']?></li>
                      <?php } ?>
                    </ul>
                  </div>
                </div>
              </div>
            </form>
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
                    <li><a href="javascript:void(0)" class="actions" data-action="slider">Set to slider</a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="remove_slider">Remove to slider</a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="delete">Delete</a></li>
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
                      <h2><?php echo $row['category_name'];?></h2>

                      <?php if($row['featured']!=0){?>
                        <a href="javascript:void(0)" class="toggle_btn_a" data-id="<?php echo $row['id'];?>" data-action="remove_slider" data-column="featured" data-toggle="tooltip" data-tooltip="Remove Slider" style="width: 30px;height: 30px">
                          <div style="color:green;"><i class="fa fa-sliders"></i></div>
                        </a>
                      <?php }else{?>
                        <a href="javascript:void(0)" class="toggle_btn_a" data-id="<?php echo $row['id'];?>" data-action="slider" data-column="featured" data-toggle="tooltip" data-tooltip="Set Slider" style="width: 30px;height: 30px"><i class="fa fa-sliders"></i></a>
                      <?php }?>

                      <a href="edit_wallpaper.php?wallpaper_id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="Edit" style="margin-right: 5px;width: 30px;height: 30px"><i class="fa fa-edit"></i></a>

                      <div class="checkbox" style="float: right;">
                        <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i; ?>" value="<?php echo $row['id']; ?>" class="post_ids">
                        <label for="checkbox<?php echo $i; ?>">
                        </label>
                      </div>
                    </div>
                    <div class="wall_image_title">
                      <?php 
                      if(!empty($row['wall_colors'])){
                        ?>
                        <div class="wall_category_block" style="padding-left: 10px">
                          <?php
                          $sql_color="SELECT * FROM tbl_color WHERE color_id IN (".$row['wall_colors'].") ORDER BY color_id ASC";
                          $res_color=mysqli_query($mysqli,$sql_color) or die(mysqli_error($mysqli));

                          if(mysqli_num_rows($res_color) > 0)
                          {
                            while($colors=mysqli_fetch_assoc($res_color)){
                              echo '<div class="color-drops" style="background: '.$colors['color_code'].'" data-toggle="tooltip" data-tooltip="'.$colors['color_name'].'"></div>';
                            }
                          }
                          ?>
                        </div>
                      <?php } ?>
                      <div class="clearfix"></div>
                      <br/>
                      <ul>
                        <?php 
                        if($row['wallpaper_type']=='Landscape'){
                          ?>
                          <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="Landscape"><i class="fa fa-mobile" style="transform:rotate(90deg);"></i></a>
                          </li>
                          <?php
                        }
                        else if($row['wallpaper_type']=='Portrait'){
                          ?>
                          <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="Portrait"><i class="fa fa-mobile"></i></a>
                          </li>
                          <?php
                        }
                        else if($row['wallpaper_type']=='Square'){
                          ?>
                          <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="Square"><i class="fa fa-square-o"></i></a>
                          </li>
                          <?php
                        }
                        ?>

                        <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?php echo thousandsNumberFormat($row['total_views']);?> Views"><i class="fa fa-eye"></i></a></li>            

                        <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?php echo $row['rate_avg'];?> Rating"><i class="fa fa-star"></i></a></li>

                        <li>
                          <a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?php echo thousandsNumberFormat($row['total_download']);?> Download"><i class="fa fa-download"></i></a>
                        </li>

                        <li>
                          <a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" class="btn_delete_a" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a>
                        </li>

                      </ul>
                    </div>
                    <span><img src="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" style="height: 350px"/></span>
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
                <?php include("pagination.php");?>
              </nav>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>

  <?php include("includes/footer.php");?>       

  <script type="text/javascript">

    $("#searchForm").submit(function(e){
      $(".loader-container").parents("div").addClass("__loading");
    });


    $(".filter").on("change", function(e) {
      $("#filterForm *").filter(":input").each(function() {
        if ($(this).val() == '')
          $(this).prop("disabled", true);
      });

      $("#filterForm").submit();
    });

  </script>

  <?php 
  if(isset($_GET['color'])){
    ?>
    <script type="text/javascript">
      var element=$("ul.filter_color li").filter('[data-value="'+<?=$_GET['color']?>+'"]');
      $("ul.filter_color li").filter('[data-value="'+<?=$_GET['color']?>+'"]').addClass('selected');
      $("ul.filter_color").children('.init').html(element.html());
      $("ul.filter_color li:first").after('<li class="clr_filter" data-value="clr">Clear Filter</li>');
    </script>
    <?php
  }
  ?>

  <script type="text/javascript">

    $(".toggle_btn_a").on("click",function(e){
      e.preventDefault();

      var _for=$(this).data("action");
      var _id=$(this).data("id");
      var _column=$(this).data("column");
      var _table='tbl_wallpaper';

      $.ajax({
        type:'post',
        url:'processData.php',
        dataType:'json',
        data:{id:_id,for_action:_for,column:_column,table:_table,'action':'toggle_status'},
        success:function(res){
          if(res.status=='1'){
            location.reload();
          }
        }
      });
    });

    $("ul.filter_color").on("click", ".init", function() {
      $(this).closest("ul").children('li:not(.init)').toggle();
    });

    var allOptions = $("ul.filter_color").children('li:not(.init)');

    $("ul.filter_color").on("click", "li:not(.init)", function() {

      var color=$(this).data("value");

      allOptions.removeClass('selected');
      $("ul.filter_color .clr_filter").remove();
      $(this).addClass('selected');
      $("ul.filter_color").children('.init').html($(this).html());
      $("ul.filter_color").children('.init').html($(this).html());
      $("ul.filter_color li:first").after('<li class="clr_filter" data-value="clr">Clear Filter</li>');
      allOptions.toggle();

      if(color=='clr'){
        $("input[name='color']").attr("disabled","disabled");
        $("input[name='color']").val('');
      }
      else{
        $("input[name='color']").val(color);  
      }

      $("#filterForm").submit();

    });

    $(document).click(function(){
      $(".filter_color li").not(".init").hide();
    });

    $(".filter_color").click(function(event) {
      event.stopPropagation();
    });
  </script>

  <script type="text/javascript">

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
              var _table='tbl_wallpaper';

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
      var _table='tbl_wallpaper';
      
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