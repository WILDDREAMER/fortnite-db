<?php 

$page_title="Add GIF";
include("includes/header.php");
require("includes/function.php");
require("language/language.php");


if(isset($_POST['submit']))
{

  $count = count($_FILES['wallpaper_image']['name']);
  for($i=0; $i < $count; $i++)
  {

    $file_name= str_replace(" ","-",$_FILES['wallpaper_image']['name'][$i]);
    $albumimgnm=rand(0,99999)."_".$file_name;

    $tpath1='images/animation/'.$albumimgnm;	
    $pic1=$_FILES['wallpaper_image']['tmp_name'][$i];   

    copy($pic1,$tpath1);

    $gif_tags=json_decode($_POST['gif_tags'], true);

    $tags='';

    foreach ($gif_tags as $value) {
      $tags.=$value['value'].', ';
    }

    unset($gif_tags);

    $gif_tags=rtrim($tags, ', ');

    $data = array( 					    
      'image'  =>  $albumimgnm,
      'gif_tags'  =>  $gif_tags
    );		

    $qry = Insert('tbl_wallpaper_gif',$data);	

  }

  $_SESSION['class']="success";
  $_SESSION['msg']="10";
  header( "Location:manage_wallpaper_animation.php");
  exit;
}
?>

<link rel="stylesheet" type="text/css" href="vendor/tagify/tagify.css">

<style type="text/css">
  .tagify{
    height: auto;
  }
</style>

<div class="row">
  <div class="col-md-12">
    <?php
    if(isset($_GET['redirect'])){
      echo '<a href="'.$_GET['redirect'].'" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
    }
    else{
      echo '<a href="manage_wallpaper_animation.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
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

          <div class="section">
            <div class="section-body">

              <div class="form-group">
                <label class="col-md-3 control-label">GIF Image :-</label>
                <div class="col-md-6">
                  <div class="fileupload_block">
                    <input type="file" name="wallpaper_image[]" value="" accept=".gif, .GIF" id="fileupload" multiple required>
                    <div class="fileupload_img"><img type="image" src="assets/images/square.jpg" alt="image"  style="width: 90px;height: 90px"/></div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">GIF Tags :-</label>
                <div class="col-md-6">
                  <input type="text" value="Funny, Dance" name="gif_tags[]" class="form-control tagify-input" required="" style="height: auto !important;"/>
                </div>
              </div>
              <br/>
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

<script type="text/javascript" src="vendor/tagify/tagify.polyfills.min.js"></script>
<script type="text/javascript" src="vendor/tagify/tagify.min.js"></script>

<script type="text/javascript">

  var input = document.querySelector('input[name="gif_tags[]"]');
  new Tagify(input)
</script>       
