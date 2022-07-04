<?php 

$page_title="Manage Categories";

include("includes/header.php");
require("includes/function.php");
require("language/language.php");

$tableName="tbl_category";   
$targetpage = "manage_category.php"; 
$limit = 12; 

$keyword='';

if(!isset($_GET['keyword'])){
  $query = "SELECT COUNT(*) as num FROM $tableName";
}
else{

  $keyword=addslashes(trim($_GET['keyword']));

  $query = "SELECT COUNT(*) as num FROM $tableName WHERE `category_name` LIKE '%$keyword%'";

  $targetpage = "manage_category.php?keyword=".$_GET['keyword'];

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
  $sql_query="SELECT * FROM tbl_category ORDER BY tbl_category.`cid` DESC LIMIT $start, $limit"; 
}
else{

  $sql_query="SELECT * FROM tbl_category WHERE `category_name` LIKE '%$keyword%' ORDER BY tbl_category.`cid` DESC LIMIT $start, $limit"; 
}

$result=mysqli_query($mysqli,$sql_query) or die(mysqli_error($mysqli));

function get_total_wallpaper($cat_id)
{ 
  global $mysqli;   

  $qry_wallpaper="SELECT COUNT(*) as num FROM tbl_wallpaper WHERE cat_id='".$cat_id."'";

  $total_wallpaper = mysqli_fetch_array(mysqli_query($mysqli,$qry_wallpaper));
  $total_wallpaper = $total_wallpaper['num'];

  return $total_wallpaper;
} 

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
            <div class="add_btn_primary"> <a href="add_category.php?add=yes&redirect=<?=$redirectUrl?>">Add Category</a> </div>
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
              <div class="block_wallpaper add_wall_category">           
                <div class="wall_image_title">
                  <h2><a href="#"><?php echo $row['category_name'];?> <span>(<?php echo get_total_wallpaper($row['cid']);?>)</span></a></h2>
                  <ul>

                    <li><a href="add_category.php?cat_id=<?php echo $row['cid'];?>&redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="Edit"><i class="fa fa-edit"></i></a></li>  

                    <li><a href="javascript:void(0)" data-id="<?php echo $row['cid'];?>" class="btn_delete_a" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a></li>

                    <li>
                      <div class="row toggle_btn">
                        <input type="checkbox" id="enable_disable_check_<?=$i?>" data-id="<?=$row['cid']?>" data-table="tbl_category" data-column="status" class="cbx hidden enable_disable" <?php if($row['status']==1){ echo 'checked';} ?>>
                        <label for="enable_disable_check_<?=$i?>" class="lbl"></label>
                      </div>
                    </li>

                  </ul>
                </div>
                <span><img src="thumb.php?src=images/<?php echo $row['category_image'];?>&size=250x200" /></span>
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
            <?php include("pagination.php"); ?>
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

  $(".btn_delete_a").click(function(e){
    e.preventDefault();

    var _id=$(this).data("id");
    var _table='tbl_category';

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
