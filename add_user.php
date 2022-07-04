<?php 

$page_title=(!isset($_GET['user_id'])) ? 'Add User' : 'Edit User';

include('includes/header.php');

include('includes/function.php');
include('language/language.php');

if(isset($_POST['submit']) and isset($_GET['add']))
{

  $sql="SELECT * FROM tbl_users WHERE `email` = '".trim($_POST['email'])."'";
  $res=mysqli_query($mysqli, $sql);

  if(mysqli_num_rows($res) == 0){

    $data = array(
      'user_type'=> 'Normal',  
      'name'  =>  cleanInput($_POST['name']),
      'email'  =>  cleanInput($_POST['email']),
      'password'  =>  md5(trim($_POST['password'])),
      'phone'  =>  cleanInput($_POST['phone']),
      'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
    );

    $qry = Insert('tbl_users',$data);

    $_SESSION['class']="success";
    $_SESSION['msg']="10";
  }
  else{
    $_SESSION['class']="warn";
    $_SESSION['msg']="email_exist";
  }

  header("location:manage_users.php");	 
  exit;
}

if(isset($_GET['user_id']))
{
  $user_qry="SELECT * FROM tbl_users where id='".$_GET['user_id']."'";
  $user_result=mysqli_query($mysqli,$user_qry);
  $user_row=mysqli_fetch_assoc($user_result);
}

if(isset($_POST['submit']) and isset($_POST['user_id']))
{

  if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
  {
    $_SESSION['class']="warn";
    $_SESSION['msg']="invalid_email_format";
    if(isset($_GET['redirect'])){
      header("Location:add_user.php?user_id=".$_POST['user_id'].'&redirect='.$_GET['redirect']);
    }
    else{
      header("Location:add_user.php?user_id=".$_POST['user_id']);
    }
    exit;
  }
  else{

    $email=cleanInput($_POST['email']);

    $sql="SELECT * FROM tbl_users WHERE `email` = '$email' AND `id` <> '".$_POST['user_id']."'";

    $res=mysqli_query($mysqli, $sql);

    if(mysqli_num_rows($res) == 0){
      $data = array(
        'name'  =>  cleanInput($_POST['name']),
        'email'  =>  cleanInput($_POST['email']),
        'phone'  =>  cleanInput($_POST['phone']),
      );

      if(isset($_POST['password']) && $_POST['password']!="")
      {
        $password=md5(trim($_POST['password']));
        $data = array_merge($data, array("password"=>$password));
      }

      $user_edit=Update('tbl_users', $data, "WHERE id = '".$_POST['user_id']."'");

      $_SESSION['class']="success";

      $_SESSION['msg']="11";
    }
    else{
      $_SESSION['class']="warn";
      $_SESSION['msg']="email_exist";

      if(isset($_GET['redirect'])){
        header("Location:add_user.php?user_id=".$_POST['user_id'].'&redirect='.$_GET['redirect']);
      }
      else{
        header("Location:add_user.php?user_id=".$_POST['user_id']);
      }
      exit;
    }
  }

  if(isset($_GET['redirect'])){
    header("Location:".$_GET['redirect']);
  }
  else{
    header("Location:add_user.php?user_id=".$_POST['user_id']);
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
      echo '<a href="manage_users.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
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
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data" >
          <input  type="hidden" name="user_id" value="<?=isset($_GET['user_id']) ? $_GET['user_id'] : '';?>"/>
          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="name" id="name" value="<?php if(isset($_GET['user_id'])){echo $user_row['name'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Email :-</label>
                <div class="col-md-6">
                  <input type="email" name="email" id="email" value="<?php if(isset($_GET['user_id'])){echo $user_row['email'];}?>" <?=(isset($_GET['user_id']) AND $user_row['user_type']!='Normal') ? 'readonly' : ''?> class="form-control" <?=(!isset($_GET['user_id'])) ? 'required="required"' : '' ?>>
                </div>
              </div>
              <?php 
              if(!isset($_GET['user_id']) || $user_row['user_type']=='Normal')
              {
                ?>
                <div class="form-group">
                  <label class="col-md-3 control-label">Password :-</label>
                  <div class="col-md-6">
                    <input type="password" name="password" id="password" value="" class="form-control" <?php if(!isset($_GET['user_id'])){?>required<?php }?>>
                  </div>
                </div>
              <?php } ?>
              <div class="form-group">
                <label class="col-md-3 control-label">Phone :-</label>
                <div class="col-md-6">
                  <input type="text" name="phone" id="phone" value="<?php if(isset($_GET['user_id'])){echo $user_row['phone'];}?>" class="form-control">
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

<?php include('includes/footer.php');?>