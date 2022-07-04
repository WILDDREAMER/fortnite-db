<?php 

$page_title="Add Wallpaper";

include("includes/header.php");
require("includes/function.php");
require("language/language.php");

$cat_qry="SELECT * FROM tbl_category WHERE status='1' ORDER BY category_name";
$cat_result=mysqli_query($mysqli,$cat_qry); 

$sql="SELECT * FROM tbl_color WHERE color_status='1' ORDER BY color_name";
$col_result=mysqli_query($mysqli,$sql); 

if(isset($_POST['submit']))
{ 
  $count = count($_FILES['wallpaper_image']['name']);

  for($i=0;$i<$count;$i++)
  { 
    $file_name= str_replace(" ","-",$_FILES['wallpaper_image']['name'][$i]);
    $albumimgnm=rand(0,99999)."_".$file_name;

             //Main Image
    $tpath1='categories/'.$_POST['cat_id'].'/'.$albumimgnm;      
    $pic1=compress_image($_FILES["wallpaper_image"]["tmp_name"][$i], $tpath1, 70); 

    $date=date('Y-m-j');          
    if($_POST['wall_tags']=='')
    {
      $qry="SELECT * FROM tbl_category WHERE cid='".$_POST['cat_id']."'";
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


    $data = array( 
      'cat_id'  =>  $_POST['cat_id'],
      'wallpaper_type'  =>  $_POST['wallpaper_type'],
      'image_date'  =>  $date,
      'image'  =>  $albumimgnm,
      'wall_tags'  =>  $wall_tags,
      'wall_colors'  =>  implode(',', $_POST['wallpaper_color'])
    );    

    $qry = Insert('tbl_wallpaper',$data); 

  } 

  $_SESSION['class']="success";
  $_SESSION['msg']="10";
  header( "Location:manage_wallpaper.php");
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

          <div class="section">
            <div class="section-body">
             <div class="form-group">
              <label class="col-md-3 control-label">Category :-</label>
              <div class="col-md-6">
                <select name="cat_id" id="cat_id" class="select2" required>
                  <option value="">--Select Category--</option>
                  <?php
                  while($cat_row=mysqli_fetch_array($cat_result))
                  {
                   ?>                      
                   <option value="<?php echo $cat_row['cid'];?>"><?php echo $cat_row['category_name'];?></option>                          
                   <?php
                 }
                 ?>
               </select>
             </div>
           </div>
           <div class="form-group">
            <label class="col-md-3 control-label">Wallpaper Type :-
            </label>
            <div class="col-md-6">
              <select name="wallpaper_type" id="wallpaper_type" class="select2" required>
                <option value="Portrait" data-img="assets/images/portrait.jpg">Portrait</option>
                <option value="Landscape" data-img="assets/images/landscape.jpg">Landscape</option>
                <option value="Square" data-img="assets/images/square.jpg">Square</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-3 control-label">Wallpaper Images :-
              <p class="control-label-help" id="portrait_lable_info">(Recommended resolution: 600x900,680x1024,640x960,720x1280 OR width less then height)</p>
              <p class="control-label-help" id="landscape_lable_info" style="display: none;">(Recommended resolution: 900x600,1280x720 OR width more then height)</p>
              <p class="control-label-help" id="square_lable_info" style="display: none;">(Recommended resolution: 500x500,700x700 OR width and height equal)</p>

              <p class="control-label-help">(You can upload multiples wallpapers)</p>

            </label>
            <div class="col-md-6">

              <div class="fileupload_block files" id="fileupload_block">
                <div class="col-md-5" style="padding-left: 0px;display:inline-block">
                  <input type="file" name="wallpaper_image[]" value="" accept=".png, .jpg, .jpeg, .svg" id="fileupload" multiple>
                </div>
                <div class="fileupload_img" id="uploadPreview"><img type="image" src="assets/images/portrait.jpg" alt="image" style="width: 90px;height: auto;" /></div>
              </div>

            </div>
          </div>
          <div class="form-group">
            <label class="col-md-3 control-label">Wallpaper Tags :-</label>
            <div class="col-md-6">
              <input type="text" value="Nature,Beauty" name="wall_tags" class="form-control tagify-input" required="" style="height: auto !important;" />
            </div>
          </div>
          <br/>
          <div class="form-group">
            <label class="col-md-3 control-label">Wallpaper Colors :-</label>
            <div class="col-md-6">
              <select name="wallpaper_color[]" class="select2" multiple="multiple" required="" style="padding: 10px 15px !important;">
                <option value="">--Select Colors--</option>
                <?php
                while($colors=mysqli_fetch_array($col_result))
                {
                  ?>                       
                  <option value="<?php echo $colors['color_id'];?>"><?php echo $colors['color_name'];?></option>                           
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

  /*var tagify = new Tagify($(".tagify-input"));*/

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
  });

</script>
