<?php 
    
    $page_title="Send Notification";

    include("includes/header.php");

    require("includes/function.php");
    require("language/language.php");

    function get_cat_name($cat_id)
    { 
      global $mysqli;

      $cat_qry="SELECT * FROM tbl_category WHERE cid='".$cat_id."'";
      $cat_result=mysqli_query($mysqli,$cat_qry); 
      $cat_row=mysqli_fetch_assoc($cat_result); 

      return $cat_row['category_name'];

    }

    $cat_qry="SELECT * FROM tbl_category ORDER BY category_name";
    $cat_result=mysqli_query($mysqli,$cat_qry); 

    if(isset($_POST['submit']))
    {

        if($_POST['external_link']!="")
        {
          $external_link = trim($_POST['external_link']);
        }
        else
        {
          $external_link = false;
        } 

        if($_POST['cat_id']!=0)
        {
          $cat_name=get_cat_name($_POST['cat_id']);
        }
        else
        {
          $cat_name='';
        }

        $content = array("en" => cleanInput($_POST['notification_msg']));
        $notification_title = cleanInput($_POST['notification_title']);

        if($_FILES['big_picture']['name']!="")
        {   

              $big_picture=rand(0,99999)."_".$_FILES['big_picture']['name'];
              $tpath2='images/'.$big_picture;
              move_uploaded_file($_FILES["big_picture"]["tmp_name"], $tpath2);

              $file_path = getBaseUrl().'images/'.$big_picture;

              $fields = array(
                'app_id' => ONESIGNAL_APP_ID,
                'included_segments' => array('All'),                                            
                'data' => array("foo" => "bar","cat_id"=>$_POST['cat_id'],"cat_name"=>$cat_name,"external_link"=>$external_link),
                'headings'=> array("en" => $notification_title),
                'contents' => $content,
                'big_picture' =>$file_path,
                'ios_attachments' => array(
                 'id' => $file_path,
               ),                    
              );
        }
        else
        {
              $fields = array(
                'app_id' => ONESIGNAL_APP_ID,
                'included_segments' => array('All'),                                      
                'data' => array("foo" => "bar","cat_id"=>$_POST['cat_id'],"cat_name"=>$cat_name,"external_link"=>$external_link),
                'headings'=> array("en" => $notification_title),
                'contents' => $content
              );
        }

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
         'Authorization: Basic '.ONESIGNAL_REST_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        $_SESSION['class'] = "success";
        $_SESSION['msg']="16";
        header( "Location:send_notification.php");
        exit;

    }
    else if(isset($_POST['notification_submit'])) {

        $data = array(
          'onesignal_app_id' => trim($_POST['onesignal_app_id']),
          'onesignal_rest_key' => trim($_POST['onesignal_rest_key']),
        );

        $settings_edit = Update('tbl_settings', $data, "WHERE id = '1'");

        $_SESSION['class'] = "success";
        $_SESSION['msg'] = "11";
        header("Location:send_notification.php");
        exit;
    }

?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?= $page_title ?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px">

        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#notification_settings" name="Notification Settings" aria-controls="notification_settings" role="tab" data-toggle="tab"><i class="fa fa-wrench"></i> Notification Settings</a></li>
          <li role="presentation"><a href="#send_notification" aria-controls="send_notification" name="Send notification" role="tab" data-toggle="tab"><i class="fa fa-send"></i> Send Notification</a></li>

        </ul>

        <div class="tab-content">

          <div role="tabpanel" class="tab-pane active" id="notification_settings">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
                  <form action="" name="settings_api" method="post" class="form form-horizontal" enctype="multipart/form-data" id="api_form">
                    <div class="section">
                      <div class="section-body">
                        <div class="form-group">
                          <label class="col-md-3 control-label">OneSignal App ID :-</label>
                          <div class="col-md-6">
                            <input type="text" name="onesignal_app_id" id="onesignal_app_id" value="<?php echo $settings_details['onesignal_app_id']; ?>" class="form-control">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">OneSignal Rest Key :-</label>
                          <div class="col-md-6">
                            <input type="text" name="onesignal_rest_key" id="onesignal_rest_key" value="<?php echo $settings_details['onesignal_rest_key']; ?>" class="form-control">
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="col-md-9 col-md-offset-3">
                            <button type="submit" name="notification_submit" class="btn btn-primary">Save</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="send_notification">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
                    <form action="" name="" method="post" class="form form-horizontal" enctype="multipart/form-data">
                      <div class="section">
                        <div class="section-body">
                          <div class="form-group">
                            <label class="col-md-3 control-label">Title :-</label>
                            <div class="col-md-6">
                              <input type="text" name="notification_title" id="notification_title" class="form-control" value="" placeholder="" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-3 control-label">Message :-</label>
                            <div class="col-md-6">
                              <textarea name="notification_msg" id="notification_msg" class="form-control" required></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-3 control-label">Image :-<br/>(Optional)<p class="control-label-help">(Recommended resolution: 600x293 or 650x317 or 700x342 or 750x366)</p></label>

                            <div class="col-md-6">
                              <div class="fileupload_block">
                                <input type="file" name="big_picture" value="fileupload" accept=".png, .jpg, .JPG .PNG" onchange="fileValidation()" id="fileupload">
                                  <div class="fileupload_img" id="uploadPreview"><img type="image" src="assets/images/square.jpg" alt="image" style="width: 120px !important;height: 90px !important;" /></div>
                              </div>
                            </div>
                         </div>
                         <div class="col-md-9 mrg_bottom link_block">
                          <div class="form-group">
                            <label class="col-md-4 control-label">Category :-<br/>(Optional)
                              <p class="control-label-help">To directly open wallpapers of selected category when click on notification</p></label>
                              <div class="col-md-8">
                                <select name="cat_id" id="cat_id" class="select2" required>
                                  <option value="0">--Select Category--</option>
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
                            <div class="or_link_item">
                              <h2>OR</h2>
                            </div>
                            <div class="form-group">
                              <label class="col-md-4 control-label">External Link :-<br/>(Optional)</label>
                              <div class="col-md-8">
                                <input type="text" name="external_link" id="external_link" class="form-control" value="" placeholder="http://www.viaviweb.com">
                              </div>
                            </div>   
                          </div>   
                          <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                              <button type="submit" name="submit" class="btn btn-primary">Send</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                </div>
              </div>
            </div>
          </div>

          <div class="clearfix"></div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php");?>

<script type="text/javascript">

  $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
    localStorage.setItem('activeTab', $(e.target).attr('href'));
    document.title = $(this).text()+" | <?=APP_NAME?>";
  });

  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

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
                document.getElementById('uploadPreview').innerHTML = '<img src="'+e.target.result+'" style="width:120px;height:90px"/>';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
  }
</script>       
