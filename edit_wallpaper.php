<?php 
  
    $page_title="Edit Wallpaper";

    include("includes/header.php");
    require("includes/function.php");
    require("language/language.php");
    require_once("thumbnail_images.class.php");

      //Get Category
    $cat_qry="SELECT * FROM tbl_category ORDER BY category_name";
    $cat_result=mysqli_query($mysqli,$cat_qry); 

    $qry="SELECT * FROM tbl_wallpaper where id='".$_GET['wallpaper_id']."'";
    $result=mysqli_query($mysqli,$qry);
    $row=mysqli_fetch_assoc($result);

    $sql="SELECT * FROM tbl_color WHERE color_status='1' ORDER BY color_name";
    $col_result=mysqli_query($mysqli,$sql);

    if(isset($_POST['submit']))
    {
      if($_POST['wall_tags']=='')
      {
        $qry="SELECT * FROM tbl_category where cid='".$_POST['cat_id']."'";
        $result=mysqli_query($mysqli,$qry);
        $row=mysqli_fetch_assoc($result);
        $wall_tags=$row['category_name'];
      }
      else
      {
        $wall_tags=json_decode($_POST['wall_tags'], true);

        $tags='';

        foreach ($wall_tags as $value) {
          $tags.=$value['value'].', ';
        }

        unset($wall_tags);

        $wall_tags=rtrim($tags, ', ');
      }

      if($_FILES['wallpaper_image']['name']!="")
      { 
         $file_name= str_replace(" ","-",$_FILES['wallpaper_image']['name']);

         $albumimgnm=rand(0,99999)."_".$file_name;

         //Main Image
         $tpath1='categories/'.$_POST['cat_id'].'/'.$albumimgnm;       
         $pic1=compress_image($_FILES["wallpaper_image"]["tmp_name"], $tpath1, 70);

         $data = array( 
           'cat_id'  =>  $_POST['cat_id'],
           'wallpaper_type'  =>  $_POST['wallpaper_type'],
           'image'  =>  $albumimgnm,
           'wall_tags'  =>  $wall_tags,
           'wall_colors'  =>  implode(',', $_POST['wallpaper_color'])
         );

         $qry=Update('tbl_wallpaper', $data, "WHERE id = '".$_POST['wallpaper_id']."'");
     }
     else
     {

      if($row['cat_id']!=$_POST['cat_id']){

        $curr_File='categories/'.$row['cat_id'].'/'.$row['image'];
        $moveFile='categories/'.$_POST['cat_id'].'/'.$row['image'];
        if (copy($curr_File,$moveFile)) 
        {
          unlink($curr_File);
        }

        $albumimgnm=$row['image'];

      }
      else{
        $albumimgnm=$row['image'];
      }

      $data = array( 
        'cat_id'  =>  $_POST['cat_id'],
        'wallpaper_type'  =>  $_POST['wallpaper_type'],
        'image'  =>  $albumimgnm,
        'wall_tags'  =>  $wall_tags,
        'wall_colors'  =>  implode(',', $_POST['wallpaper_color'])
      );

      $qry=Update('tbl_wallpaper', $data, "WHERE id = '".$_POST['wallpaper_id']."'");

    }


    $_SESSION['class']="success";
    $_SESSION['msg']="11";

    
    if(isset($_GET['redirect'])){
      header("Location:".$_GET['redirect']);
    }
    else{
      header( "Location:edit_wallpaper.php?wallpaper_id=".$_POST['wallpaper_id']);
    }

    exit;	


    }

?>

<link rel="stylesheet" type="text/css" href="vendor/tagify/tagify.css">

<style type="text/css">
  .select2-container .select2-selection--multiple{
    padding: 5px 5px !important;
  }
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
      echo '<a href="manage_wallpaper.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
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
            
            <input  type="hidden" name="wallpaper_id" value="<?php echo $_GET['wallpaper_id'];?>" />
            <div class="section">
              <div class="section-body">
               <div class="form-group">
                <label class="col-md-3 control-label">Category :-</label>
                <div class="col-md-6">
                  <select name="cat_id" id="cat_id" class="select2">
                    <option value="">--Select Category--</option>
                    <?php
                    while($cat_row=mysqli_fetch_array($cat_result))
                    {
                     ?>          						 
                     <option value="<?php echo $cat_row['cid'];?>" <?php if($cat_row['cid']==$row['cat_id']){?>selected<?php }?>><?php echo $cat_row['category_name'];?></option>	          							 
                     <?php
                   }
                   ?>
                 </select>
               </div>
             </div>
             <div class="form-group">
              <label class="col-md-3 control-label">Wallpaper Type :-
                <!-- <p class="control-label-help">(Optional)</p> -->
              </label>
              <div class="col-md-6">
                <select name="wallpaper_type" id="wallpaper_type" class="select2" required>
                  <!-- <option value="none">--Select Type--</option> -->
                  <option value="Portrait" <?php if($row['wallpaper_type']=="Portrait"){?>selected<?php }?>>Portrait</option>
                  <option value="Landscape" <?php if($row['wallpaper_type']=="Landscape"){?>selected<?php }?>>Landscape</option>
                  <option value="Square" <?php if($row['wallpaper_type']=="Square"){?>selected<?php }?>>Square</option>

                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label">Wallpaper Image :-

                <p class="control-label-help" id="portrait_lable_info" <?php if($row['wallpaper_type']=="Portrait"){?>style="display: block;" <?php }else{?>style="display: none;"<?php }?>>(Recommended resolution: 600x900,680x1024,640x960,720x1280 OR width less then height)</p>

                <p class="control-label-help" id="landscape_lable_info" <?php if($row['wallpaper_type']=="Landscape"){?>style="display: block;" <?php }else{?>style="display: none;"<?php }?>>(Recommended resolution: 900x600,1280x720 OR width more then height)</p>

                <p class="control-label-help" id="square_lable_info" <?php if($row['wallpaper_type']=="Square"){?>style="display: block;" <?php }else{?>style="display: none;"<?php }?>>(Recommended resolution: 500x500,700x700 OR width and height equal)</p>

              </label>
              <div class="col-md-6">
                <div class="fileupload_block">
                  <input type="file" name="wallpaper_image" id="fileupload" accept=".png, .jpg, .jpeg" onchange="fileValidation()">
                  <?php if($row['image']!="") {?>
                    <div class="fileupload_img" id="uploadPreview">
                      <img src="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" alt="image"  style="width: 100px;height: 150px;"/></div>
                  <?php }else{ ?>
                  <div class="fileupload_img" id="uploadPreview">
                    <img type="image" src="assets/images/portrait.jpg" alt="image" style="width: 100px;height: 150px;" /></div>
                  <?php } ?>

                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label">Wallpaper Tags :-</label>
              <div class="col-md-6">
                <input type="text" value="<?php echo trim($row['wall_tags']);?>" name="wall_tags" class="form-control tagify-input" required="" style="height: auto !important;" />
              </div>
            </div>
            <br/>
            <div class="form-group">
              <label class="col-md-3 control-label">Wallpaper Colors :-</label>
              <div class="col-md-6">
                <select name="wallpaper_color[]" class="select2" multiple="multiple" required="" style="padding: 10px 15px !important;">
                  <option value="">--Select Colors--</option>
                  <?php
                  $db_colors=explode(',', $row['wall_colors']);
                  while($colors=mysqli_fetch_array($col_result))
                  {

                    ?>                       
                    <option value="<?php echo $colors['color_id'];?>" <?php if(in_array($colors['color_id'],$db_colors)){ echo 'selected'; } ?>><?php echo $colors['color_name'];?></option>                           
                    <?php
                  }
                  ?>
                </select>
              </div>
            </div>

            <hr/>
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

  var input = document.querySelector('input[name="wall_tags"]');

  new Tagify(input)

  var type='Portrait';

  $(function(){

    $("#wallpaper_type").change(function(){

        type=$("#wallpaper_type").val();

        var img_path = $(this).find(":selected").data("img");

        $(".fileupload_img").find("img").attr("src",img_path);

        if(type=="Portrait")
        {
          $(".fileupload_img").find("img").css({"width": "100px", "height": "150px"});
          $("#portrait_lable_info").show();
          $("#landscape_lable_info").hide();
          $("#square_lable_info").hide();
        }
        else if(type=="Landscape")
        {                 
          $(".fileupload_img").find("img").css({"width": "150px", "height": "100px"});

          $("#portrait_lable_info").hide();
          $("#landscape_lable_info").show();
          $("#square_lable_info").hide();
        }
        else if(type=="Square")
        { 
          $(".fileupload_img").find("img").css({"width": "100px", "height": "100px"});  
          $("#portrait_lable_info").hide();
          $("#landscape_lable_info").hide();
          $("#square_lable_info").show();
        }
        else
        {
          $("#portrait_lable_info").hide();
          $("#landscape_lable_info").hide();
          $("#square_lable_info").hide();
        }
    });

    type=$("#wallpaper_type").val();

    if(type=="Portrait")
    {
      $(".fileupload_img").find("img").css({"width": "90px", "height": "auto"});
      $("#portrait_lable_info").show();
      $("#landscape_lable_info").hide();
      $("#square_lable_info").hide();
    }
    else if(type=="Landscape")
    {                 
      $(".fileupload_img").find("img").css({"width": "150px", "height": "auto"});

      $("#portrait_lable_info").hide();
      $("#landscape_lable_info").show();
      $("#square_lable_info").hide();
    }
    else if(type=="Square")
    { 
      $(".fileupload_img").find("img").css({"width": "100px", "height": "auto"});  
      $("#portrait_lable_info").hide();
      $("#landscape_lable_info").hide();
      $("#square_lable_info").show();
    }
    else
    {
      $("#portrait_lable_info").hide();
      $("#landscape_lable_info").hide();
      $("#square_lable_info").hide();
    }

  });

  function fileValidation(){
    var fileInput = document.getElementById('fileupload');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.png|.PNG|.jpg|.JPG)$/i;
    if(!allowedExtensions.exec(filePath)){
        swal({title: 'Invalid!',text: 'Please upload file having extension .png, .jpg, .jpeg .PNG, .JPG, .JPEG only.', type: 'warning'});
        fileInput.value = '';
        return false;
    }else{
        //image preview
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $("#uploadPreview").find("img").attr("src",e.target.result);
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
  }

</script>       
