<?php 

$page_title="User Profile";
$current_page="users";
include('includes/header.php'); 
include('includes/function.php');
include('language/language.php');

$user_id=strip_tags(addslashes(trim($_GET['user_id'])));

if(!isset($_GET['user_id']) OR $user_id==''){
	header("Location: manage_users.php");
}

$user_qry="SELECT * FROM tbl_users WHERE id='$user_id'";
$user_result=mysqli_query($mysqli,$user_qry);

if(mysqli_num_rows($user_result)==0){
	header("Location: manage_users.php");
}

$user_row=mysqli_fetch_assoc($user_result);

$user_img='assets/images/user-icons.jpg';

function getLastActiveLog($user_id){
	global $mysqli;

	$sql="SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
	$res=mysqli_query($mysqli, $sql);

	if(mysqli_num_rows($res) == 0){
		echo 'no available';
	}
	else{

		$row=mysqli_fetch_assoc($res);
		return calculate_time_span($row['date_time'],true);	
	}
}

if(isset($_POST['btn_submit']))
{

	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
	{
		$_SESSION['class']="warn";
		$_SESSION['msg']="invalid_email_format";
	}
	else{

		$email=cleanInput($_POST['email']);

		$sql="SELECT * FROM tbl_users WHERE `email` = '$email' AND `id` <> '".$user_id."'";

		$res=mysqli_query($mysqli, $sql);

		if(mysqli_num_rows($res) == 0){
			$data = array(
				'name'  =>  cleanInput($_POST['name']),
				'email'  =>  cleanInput($_POST['email']),
				'phone'  =>  cleanInput($_POST['phone'])
			);

			if(isset($_POST['password']) && $_POST['password']!="")
			{

				$password=md5(trim($_POST['password']));

				$data = array_merge($data, array("password"=>$password));
			}

			$user_edit=Update('tbl_users', $data, "WHERE id = '".$user_id."'");

			$_SESSION['class']="success";

			$_SESSION['msg']="11";
		}
		else{
			$_SESSION['class']="warn";
			$_SESSION['msg']="email_exist";
		}
	}

	if(isset($_GET['redirect']))
	{
		header("Location:user_profile.php?user_id=".$user_id.'&redirect='.$_GET['redirect']);
	}
	else{
		header("Location:user_profile.php?user_id=".$user_id);
	}

	exit;
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/stylish-tooltip.css">

<div class="row">
	<div class="col-lg-12">
		<?php
		if(isset($_GET['redirect'])){
			echo '<a href="'.$_GET['redirect'].'"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
		}
		else{
			echo '<a href="manage_users.php"><h4 class="pull-left" style="font-size: 20px;color: #e91e63"><i class="fa fa-arrow-left"></i> Back</h4></a>';
		}
		?>
		<div class="page_title_block user_dashboard_item" style="background-color: #333;border-radius:6px;border-bottom:0">
			<div class="user_dashboard_mr_bottom">
				<div class="col-md-12 col-xs-12"> <br>
					<span class="badge badge-success badge-icon">
						<div class="user_profile_img">

							<?php 
							if($user_row['user_type']=='Google'){
								echo '<img src="assets/images/google-logo.png" style="width: 16px;height: 16px;position: absolute;top: 25px;z-index: 1;left: 70px;">';
							}
							else if($user_row['user_type']=='Facebook'){
								echo '<img src="assets/images/facebook-icon.png" style="width: 16px;height: 16px;position: absolute;top: 25px;z-index: 1;left: 70px;">';
							}
							else if($user_row['user_type']=='Apple'){
								echo '<img src="assets/images/apple.png" style="width: 16px;height: 16px;position: absolute;top: 25px;z-index: 1;left: 70px;">';
							}
							?>
							<img type="image" src="<?php echo $user_img;?>" alt="image" style=""/>
						</div>
						<span style="font-size: 14px;"><?php echo $user_row['name'];?>				
					</span>
				</span>  
				<span class="badge badge-success badge-icon">
					<i class="fa fa-envelope fa-2x" aria-hidden="true"></i>
					<span style="font-size: 14px;text-transform: lowercase;"><?php echo $user_row['email'];?></span>
				</span> 
				<span class="badge badge-success badge-icon">
					<strong style="font-size: 14px;">Registered At:</strong>
					<span style="font-size: 14px;"><?php echo date('d-m-Y',$user_row['registered_on']);?></span>
				</span>
				<span class="badge badge-success badge-icon">
					<strong style="font-size: 14px;">Last Activity On:</strong>
					<span style="font-size: 14px;text-transform: lowercase;"><?php echo getLastActiveLog($user_id)?></span>
				</span>
				<br><br/>
			</div>
		</div>
	</div>

	<div class="card card-tab">
		<div class="card-header" style="overflow-x: auto;overflow-y: hidden;">
			<ul class="nav nav-tabs" role="tablist">
				<li role="dashboard" class="active"><a href="#edit_profile" aria-controls="edit_profile" role="tab" data-toggle="tab">Edit Profile</a></li>
				<li role="favourite_post"><a href="#favourite_post" aria-controls="favourite_post" role="tab" data-toggle="tab">Wallpaper Favourite</a></li>
				<li role="favourite_post"><a href="#gif_favourite_post" aria-controls="gif_favourite_post" role="tab" data-toggle="tab">GIF Favourite</a></li>
			</ul>
		</div>
		<div class="card-body no-padding tab-content">
			<div role="tabpanel" class="tab-pane active" id="edit_profile">
				<div class="row">
					<div class="col-md-12">
						<form action="" method="post" class="form form-horizontal" enctype="multipart/form-data">
							<div class="section">
								<div class="section-body">
									<div class="form-group">
										<label class="col-md-3 control-label">Name :-</label>
										<div class="col-md-6">
											<input type="text" name="name" id="name" value="<?=$user_row['name']?>" class="form-control" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">Email :-</label>
										<div class="col-md-6">
											<input type="email" name="email" id="email" value="<?=$user_row['email']?>" class="form-control" <?=($user_row['user_type']!='Normal') ? 'readonly' : ''?> required>
										</div>
									</div>
									<?php 
									if($user_row['user_type']=='Normal' OR $user_row['user_type']=='normal'){
										?>
										<div class="form-group">
											<label class="col-md-3 control-label">Password :-</label>
											<div class="col-md-6">
												<input type="password" name="password" id="password" value="" class="form-control">
											</div>
										</div>
									<?php } ?>
									<div class="form-group">
										<label class="col-md-3 control-label">Phone :-</label>
										<div class="col-md-6">
											<input type="text" name="phone" id="phone" value="<?=$user_row['phone']?>" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-9 col-md-offset-3">
											<button type="submit" name="btn_submit" class="btn btn-primary">Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="favourite_post">
				<div class="row">
					<div class="col-md-12">
						<table class="datatable table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>Sr.</th>
									<th>Image</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<?php

								$sql="SELECT tbl_wallpaper.`id`,tbl_wallpaper.`cat_id`,tbl_wallpaper.`image`, tbl_favorite.`id` AS favourite_id, tbl_favorite.`created_at` AS favourite_date FROM tbl_wallpaper
								LEFT JOIN tbl_favorite ON tbl_wallpaper.`id`=tbl_favorite.`post_id`
								WHERE tbl_favorite.`type`='wallpaper' AND tbl_favorite.`user_id`='$user_id' ORDER BY tbl_favorite.`id` DESC";

								$res=mysqli_query($mysqli, $sql);
								$no=1;
								while ($row=mysqli_fetch_assoc($res)) {
									?>
									<tr>
										<td><?=$no;?></td>
										<td nowrap="">
											<?php

											if(file_exists('categories/'.$row['cat_id'].'/'.$row['image'])){
												?>
												<span class="mytooltip tooltip-effect-3">
													<span class="tooltip-item">
														<img src="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" alt="no image" style="width: 60px;height: auto;border-radius: 5px">
													</span> 
													<span class="tooltip-content clearfix">
														<a href="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" target="_blank"><img src="categories/<?php echo $row['cat_id'];?>/<?php echo $row['image'];?>" alt="no image" /></a>
													</span>
												</span>
											<?php }else{
												?>
												<img src="" alt="no image" style="width: 60px;height: 60px;border-radius: 5px">
												<?php
											} ?>
										</td>
										<td><?=calculate_time_span($row['favourite_date'],true);?></td>
									</tr>
									<?php
									$no++;
								}
								mysqli_free_result($res);

								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="gif_favourite_post">
				<div class="row">
					<div class="col-md-12">
						<table class="datatable table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>Sr.</th>
									<th>Image</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<?php

								$sql="SELECT tbl_wallpaper_gif.`id`,tbl_wallpaper_gif.`image`, tbl_favorite.`id` AS favourite_id, tbl_favorite.`created_at` AS favourite_date FROM tbl_wallpaper_gif
								LEFT JOIN tbl_favorite ON tbl_wallpaper_gif.`id`=tbl_favorite.`post_id`
								WHERE tbl_favorite.`type`='gif' AND tbl_favorite.`user_id`='$user_id' ORDER BY tbl_favorite.`id` DESC";

								$res=mysqli_query($mysqli, $sql);
								$no=1;
								while ($row=mysqli_fetch_assoc($res)) {
									?>
									<tr>
										<td><?=$no;?></td>
										<td nowrap="">
											<?php 
											if(file_exists('images/animation/'.$row['image'])){
												?>
												<span class="mytooltip tooltip-effect-3">
													<span class="tooltip-item">
														<img src="images/animation/<?php echo $row['image'];?>" alt="no image" style="width: 60px;height: auto;border-radius: 5px">
													</span> 
													<span class="tooltip-content clearfix">
														<a href="images/animation/<?php echo $row['image'];?>" target="_blank"><img src="images/animation/<?php echo $row['image'];?>" alt="no image" /></a>
													</span>
												</span>
											<?php }else{
												?>
												<img src="" alt="no image" style="width: 60px;height: 60px;border-radius: 5px">
												<?php
											} ?>
										</td>
										<td><?=calculate_time_span($row['favourite_date'],true);?></td>
									</tr>
									<?php
									$no++;
								}
								mysqli_free_result($res);

								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
</div>

<?php 
include('includes/footer.php');
?>

<script type="text/javascript">

	$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});

	var activeTab = localStorage.getItem('activeTab');
	if(activeTab){
		$('.nav-tabs a[href="' + activeTab + '"]').tab('show');
	}

	/*$('a[data-toggle="tab"]').click(function(e){
		location.reload();
	});*/

</script>