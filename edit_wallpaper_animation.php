<?php 

$page_title="Edit GIF";
include("includes/header.php");
require("includes/function.php");
require("language/language.php");

$qry="SELECT * FROM tbl_wallpaper_gif where id='".$_GET['wallpaper_id']."'";
$result=mysqli_query($mysqli,$qry);
$row=mysqli_fetch_assoc($result);

if(isset($_POST['submit']))
{

  $gif_tags=json_decode($_POST['gif_tags'], true);

  $tags='';

  foreach ($gif_tags as $value) {
    $tags.=$value['value'].', ';
  }

  unset($gif_tags);

  $gif_tags=rtrim($tags, ', ');

  if($_FILES['wallpaper_image']['name']!="")
  { 

    $file_name= str_replace(" ","-",$_FILES['wallpaper_image']['name']);
    $albumimgnm=rand(0,99999)."_".$file_name;

    $tpath1='images/animation/'.$albumimgnm;   
    $pic1=$_FILES['wallpaper_image']['tmp_name'];   

    copy($pic1,$tpath1);

    $data = array( 
      'image'  =>  $albumimgnm,
      'gif_tags'  =>  $gif_tags
    );		


    $qry=Update('tbl_wallpaper_gif', $data, "WHERE id = '".$_POST['wallpaper_id']."'");
  }
  else
  {

    $data = array(              
      'gif_tags'  =>  $gif_tags
    );  

    $qry=Update('tbl_wallpaper_gif', $data, "WHERE id = '".$_POST['wallpaper_id']."'");
  }

  $_SESSION['msg']="11";
  $_SESSION['class']="success";
  header( "Location:edit_wallpaper_animation.php?wallpaper_id=".$_POST['wallpaper_id']);
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
        <form action="" name="" method="post" class="form form-horizontal" enctype="multipart/form-data">
          <input  type="hidden" name="wallpaper_id" value="<?php echo $_GET['wallpaper_id'];?>" />

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Wallpaper Image :-</label>
                <div class="col-md-6">
                  <div class="fileupload_block">
                    <input type="file" name="wallpaper_image" id="fileupload" accept=".gif, .GIF" onchange="fileValidation()">
                    <?php if($row['image']!="") {?>
                      <div class="fileupload_img"><img type="image" src="images/animation/<?php echo $row['image'];?>" alt="image" style="width: 90px;height: 90px"/></div>
                    <?php }else{ ?>
                      <div class="fileupload_img"><img type="image" src="assets/images/square.jpg" alt="image" style="width: 90px;height: 90px"/></div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">GIF Tags :-</label>
                <div class="col-md-6">
                  <input type="text" name="gif_tags" id="gif_tags" class="form-control tagify-input" required="" style="height: auto !important;" value="<?php echo $row['gif_tags'];?>">
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

  var input = document.querySelector('input[name="gif_tags"]');

  new Tagify(input)

  function fileValidation(){
    var fileInput = document.getElementById('fileupload');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.gif|.GIF)$/i;
    if(!allowedExtensions.exec(filePath)){
      infoDlg = duDialog('Opps!', 'Please upload file having extension .gif, .GIF only.');
      fileInput.value = '';
      return false;
    }else{
      if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(".fileupload_img").find("img").attr("src",e.target.result);
        };
        reader.readAsDataURL(fileInput.files[0]);
      }
    }
  }
</script>