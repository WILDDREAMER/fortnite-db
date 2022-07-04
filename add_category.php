<?php 
    
    $page_title=(isset($_GET['cat_id'])) ? 'Edit Category' : 'Add Category';

    include("includes/header.php");
    require("includes/function.php");
    require("language/language.php");

    require_once("thumbnail_images.class.php");

    if(isset($_POST['submit']) and isset($_GET['add']))
    {
        $ext = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);

        $category_image=rand(0,99999)."_category.".$ext;

        $tpath1='images/'.$category_image;   
        
        if($ext!='png')  {
          $pic1=compress_image($_FILES["category_image"]["tmp_name"], $tpath1, 80);
        }
        else{
          $tmp = $_FILES['category_image']['tmp_name'];
          move_uploaded_file($tmp, $tpath1);
        }

        $thumbpath='images/thumbs/'.$category_image;   
        $thumb_pic1=create_thumb_image($tpath1,$thumbpath,'300','300'); 

        $data = array( 
            'category_name'  =>  cleanInput($_POST['category_name']),
            'category_image'  =>  $category_image
        );    

        $qry = Insert('tbl_category',$data);  

        $cat_id=mysqli_insert_id($mysqli);  

        if(!is_dir('categories/'.$cat_id))
        {
          mkdir('categories/'.$cat_id, 0777); 
        }

        $_SESSION['msg']="10";
        $_SESSION['class']='success';
        header( "Location:manage_category.php");
        exit;
    }

    if(isset($_GET['cat_id']))
    {
        $qry="SELECT * FROM tbl_category where cid='".$_GET['cat_id']."'";
        $result=mysqli_query($mysqli,$qry);
        $row=mysqli_fetch_assoc($result);
    }
    if(isset($_POST['submit']) and isset($_POST['cat_id']))
    {

        if($_FILES['category_image']['name']!="")
        {
            if($row['category_image']!="")
            {
                unlink('images/thumbs/'.$row['category_image']);
                unlink('images/'.$row['category_image']);
            }

            $ext = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);

            $category_image=rand(0,99999)."_category.".$ext;

            $tpath1='images/'.$category_image;   

            if($ext!='png')  {
              $pic1=compress_image($_FILES["category_image"]["tmp_name"], $tpath1, 80);
            }
            else{
              $tmp = $_FILES['category_image']['tmp_name'];
              move_uploaded_file($tmp, $tpath1);
            }

            $thumbpath='images/thumbs/'.$category_image;   
            $thumb_pic1=create_thumb_image($tpath1,$thumbpath,'300','300');

        }
        else{
            $category_image=$row['category_image'];
        }
          
        $data = array( 
            'category_name'  =>  cleanInput($_POST['category_name']),
            'category_image'  =>  $category_image
        );

        $category_edit=Update('tbl_category', $data, "WHERE cid = '".$_POST['cat_id']."'");

        $cat_id=$_POST['cat_id']; 

        if(!is_dir('categories/'.$cat_id))
        {
            mkdir('categories/'.$cat_id, 0777);
        }

        $_SESSION['msg']="11";
        $_SESSION['class']='success'; 
        
        if(isset($_GET['redirect'])){
          header("Location:".$_GET['redirect']);
        }
        else{
          header( "Location:add_category.php?cat_id=".$_POST['cat_id']);
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
            echo '<a href="manage_category.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
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
        <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
          <input  type="hidden" name="cat_id" value="<?=(isset($_GET['cat_id'])) ? $_GET['cat_id'] : ''?>" />

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Category Name :-
                
                </label>
                <div class="col-md-6">
                  <input type="text" name="category_name" id="category_name" value="<?php if(isset($_GET['cat_id'])){echo $row['category_name'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Select Image :-
                  <p class="control-label-help">(Recommended resolution: 300x300, 400x400 OR Square Image)</p>
                </label>
                <div class="col-md-6">
                  <div class="fileupload_block">
                    <input type="file" name="category_image" value="fileupload" accept=".png, .jpg, .JPG .PNG" onchange="fileValidation()" id="fileupload">
                    <?php if(isset($_GET['cat_id'])) {?>
                      <div class="fileupload_img" id="uploadPreview"><img type="image" src="images/<?php echo $row['category_image'];?>" alt="image" style="width: 120px;height: 120px;"/></div>
                    <?php }else{?>
                      <div class="fileupload_img" id="uploadPreview"><img type="image" src="assets/images/square.jpg" alt="image" style="width: 120px;height: 120px" /></div>
                      <?php } ?>
                       
                  </div>
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

<script type="text/javascript">
  function fileValidation(){
    var fileInput = document.getElementById('fileupload');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.png|.PNG|.jpg|.JPG)$/i;
    if(!allowedExtensions.exec(filePath)){
        alert('Please upload file having extension .png, .jpg, .PNG, .JPG only.');
        fileInput.value = '';
        return false;
    }else{
        //image preview
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadPreview').innerHTML = '<img src="'+e.target.result+'" style="width:120px;height:120px"/>';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
  }
</script>      

