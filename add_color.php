<?php 

    $page_title=(isset($_GET['color_id'])) ? 'Edit Color' : 'Add Color';

    include("includes/header.php");
    require("includes/function.php");
    require("language/language.php");

    require_once("thumbnail_images.class.php");

    if(isset($_POST['submit']) and isset($_GET['add']))
    {

       $data = array( 
         'color_name'  =>  trim($_POST['color_name']),
         'color_code'  =>  trim('#'.$_POST['color_code'])
       );		

       $qry = Insert('tbl_color',$data);	

       $color_id=mysqli_insert_id($mysqli);	

       $_SESSION['class']="success";
       $_SESSION['msg']="10";

       header("Location:manage_color.php");
       exit; 

    }

    if(isset($_GET['color_id']))
    {

      $qry="SELECT * FROM tbl_color where color_id='".$_GET['color_id']."'";
      $result=mysqli_query($mysqli,$qry);
      $row=mysqli_fetch_assoc($result);

    }
    if(isset($_POST['submit']) and isset($_POST['color_id']))
    {

      $data = array( 
       'color_name'  =>  trim($_POST['color_name']),
       'color_code'  =>  trim('#'.$_POST['color_code'])
      );	

      $update=Update('tbl_color', $data, "WHERE color_id = '".$_POST['color_id']."'");

      $_SESSION['class']="success";
      $_SESSION['msg']="11";

      if(isset($_GET['redirect'])){
        header("Location:".$_GET['redirect']);
      }
      else{
        header( "Location:add_color.php?color_id=".$_POST['color_id']);
      }
      exit;
    }


?>
<div class="row">
  <div class="col-md-12">
    <?php
      if(isset($_GET['redirect'])){
            echo '<a href="'.$_GET['redirect'].'" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
          }
          else{
            echo '<a href="manage_color.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
          }
    ?>
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
         <div class="card-body mrg_bottom"> 
          <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data">
           <input  type="hidden" name="color_id" value="<?php if(isset($_GET['color_id'])){echo $_GET['color_id'];}?>" />
           
           <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Color Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="color_name" id="color_name" value="<?php if(isset($_GET['color_id'])){echo $row['color_name'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Select Color :-
                </label>
                <div class="col-md-6">
                  <input value="<?php if(isset($_GET['color_id'])){echo str_replace('#','',$row['color_code']);}else{ echo 'e91e63';}?>" name="color_code" class="form-control jscolor {width:243, height:150, position:'right',
                  borderColor:'#000', insetColor:'#FFF', backgroundColor:'#ddd'}">
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary">Save</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php");?>       

<script type="text/javascript" src="assets/js/jscolor.js"></script>