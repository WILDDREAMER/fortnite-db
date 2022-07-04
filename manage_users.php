<?php

$page_title = "Manage Users";

include('includes/header.php');
include('includes/function.php');
include('language/language.php');

$tableName="tbl_users";   
$targetpage = "manage_users.php"; 
$limit = 15; 

$keyword='';

if(!isset($_GET['keyword'])){
	$query = "SELECT COUNT(*) as num FROM $tableName";
}
else{

	$keyword=addslashes(trim($_GET['keyword']));

	$query = "SELECT COUNT(*) as num FROM $tableName WHERE (`name` LIKE '%$keyword%' OR `email` LIKE '%$keyword%' OR `phone` LIKE '%$keyword%')";

	$targetpage = "manage_users.php?keyword=".$_GET['keyword'];

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
	$sql_query="SELECT * FROM tbl_users ORDER BY tbl_users.`id` DESC LIMIT $start, $limit"; 
}
else{

	$sql_query="SELECT * FROM tbl_users WHERE (`name` LIKE '%$keyword%' OR `email` LIKE '%$keyword%' OR `phone` LIKE '%$keyword%') ORDER BY tbl_users.`id` DESC LIMIT $start, $limit"; 
}

$result=mysqli_query($mysqli,$sql_query) or die(mysqli_error($mysqli));
?>


<div class="row">
	<div class="col-xs-12">
		<div class="card mrg_bottom">
			<div class="page_title_block">
				<div class="col-md-5 col-xs-12">
					<div class="page_title"><?= $page_title ?></div>
				</div>
				<div class="col-md-7 col-xs-12">
					<div class="search_list">
						<div class="search_block">
							<form method="get" action="">
								<input class="form-control input-sm" placeholder="Search here..." aria-controls="DataTables_Table_0" type="search" name="keyword" value="<?php if(isset($_GET['keyword'])){ echo $_GET['keyword'];} ?>" required="required">
								<button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
							</form>
						</div>
						<div class="add_btn_primary"> <a href="add_user.php?add">Add User</a> </div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="col-md-4 col-xs-12 text-right" style="float: right;">
						<div class="checkbox" style="width: 95px;margin-top: 5px;margin-left: 10px;right: 100px;position: absolute;">
							<input type="checkbox" id="checkall_input">
							<label for="checkall_input">
								Select All
							</label>
						</div>
						<div class="dropdown" style="float:right">
							<button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown">Action
								<span class="caret"></span></button>
								<ul class="dropdown-menu" style="right:0;left:auto;">
									<li><a href="javascript:void(0)" class="actions" data-action="enable">Enable</a></li>
									<li><a href="javascript:void(0)" class="actions" data-action="disable">Disable</a></li>
									<li><a href="javascript:void(0)" class="actions" data-action="delete">Delete !</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-12 mrg-top">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Phone</th>
								<th class="text-center">Status</th>
								<th class="cat_action_list">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							if(mysqli_num_rows($result) > 0)
							{
								while ($users_row = mysqli_fetch_array($result)) {
									?>
									<tr>
										<td width="50">
											<div class="checkbox" style="float: right;margin: 0px">
												<input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i; ?>" value="<?php echo $users_row['id']; ?>" class="post_ids" style="margin: 0px;">
												<label for="checkbox<?php echo $i;?>"></label>
											</div>
										</td>
										<td nowrap="">
											<a href="user_profile.php?user_id=<?=$users_row['id']?>&redirect=<?=$redirectUrl?>">
												<div class="row" style="vertical-align: middle;">
													<div class="col-md-3 col-xs-12">
														<?php 
														if($users_row['user_type']=='Google'){
															echo '<img src="assets/images/google-logo.png" class="social_img">';
														}
														else if($users_row['user_type']=='Facebook'){
															echo '<img src="assets/images/facebook-icon.png" class="social_img">';
														}
														else if($users_row['user_type']=='Apple'){
															echo '<img src="assets/images/apple.png" class="social_img">';
														}
														?>
														<img type="image" src="assets/images/user-icons.jpg" alt="image" style="width: 40px;height: 40px;border-radius: 4px"/>
													</div>
													<div class="col-md-9 col-xs-12" style="padding: 8px 15px">
														<?php echo $users_row['name'];?>
													</div>
												</div>
											</a>
										</td>
										<td style="word-break: break-all;"><?php echo $users_row['email']; ?></td>
										<td style="word-break: break-all;"><?php echo $users_row['phone']; ?></td>

										<td>
											<div class="row toggle_btn">
												<input type="checkbox" id="enable_disable_check_<?=$i?>" data-id="<?=$users_row['id']?>" data-table="tbl_users" data-column="status" class="cbx hidden enable_disable" <?php if($users_row['status']==1){ echo 'checked';} ?>>
												<label for="enable_disable_check_<?=$i?>" class="lbl"></label>
											</div>
										</td>
										<td nowrap="">

											<a href="user_profile.php?user_id=<?php echo $users_row['id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-success btn_cust" data-toggle="tooltip" data-tooltip="User Profile"><i class="fa fa-history"></i></a>

											<a href="add_user.php?user_id=<?php echo $users_row['id']; ?>&redirect=<?=$redirectUrl?>"class="btn btn-primary btn_delete" data-toggle="tooltip" data-tooltip="Edit"><i class="fa fa-edit"></i></a>

											<a href="javascript:void(0)" data-id="<?php echo $users_row['id']; ?>" data-toggle="tooltip" data-tooltip="Delete" class="btn btn-danger btn_delete btn_delete_a">
												<i class="fa fa-trash"></i>
											</a>
										</td>
									</tr>
									<?php
									$i++;
								}
							}
							else{
								?>
								<tr>
									<td colspan="7">
										<p class="not_data"><strong>Sorry</strong> no data found!</p>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="col-md-12 col-xs-12">
					<div class="pagination_item_block">
						<nav>
							<?php include("pagination.php")?>
						</nav>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>

	<?php include('includes/footer.php'); ?>

	<script type="text/javascript">

		$(".actions").click(function(e){
			e.preventDefault();

			var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });
			var _action=$(this).data("action");

			if(_ids!='')
			{
				confirmDlg = duDialog('Action: '+$(this).text(), 'Do you really want to perform?', {
					init: true,
					dark: false, 
					buttons: duDialog.OK_CANCEL,
					okText: 'Proceed',
					callbacks: {
						okClick: function(e) {
							$(".dlg-actions").find("button").attr("disabled",true);
							$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> Please wait..');
							var _table='tbl_users';

							$.ajax({
								type:'post',
								url:'processData.php',
								dataType:'json',
								data:{id:_ids,for_action:_action,table:_table,'action':'multi_action'},
								success:function(res){
									$('.notifyjs-corner').empty();
									if(res.status=='1'){
										location.reload();
									}
								}
							});

						} 
					}
				});
				confirmDlg.show();
			}
			else{
				infoDlg = duDialog('Opps!', 'No data selected', { init: true });
				infoDlg.show();
			}
		});

		$(".btn_delete_a").click(function(e){
			e.preventDefault();

			var _id=$(this).data("id");
			var _table='tbl_users';

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

		var totalItems = 0;

		$("#checkall_input").click(function() {

			totalItems = 0;

			$('input:checkbox').not(this).prop('checked', this.checked);
			$.each($("input[name='post_ids[]']:checked"), function() {
				totalItems = totalItems + 1;
			});

			if ($('input:checkbox').prop("checked") == true) {
				$('.notifyjs-corner').empty();
				$.notify(
					'Total ' + totalItems + ' item checked', {
						position: "top center",
						className: 'success',
						clickToHide: false,
						autoHide: false
					}
					);
			} else if ($('input:checkbox').prop("checked") == false) {
				totalItems = 0;
				$('.notifyjs-corner').empty();
			}
		});

		$(".post_ids").click(function(e) {

			if ($(this).prop("checked") == true) {
				totalItems = totalItems + 1;
			} else if ($(this).prop("checked") == false) {
				totalItems = totalItems - 1;
			}

			if (totalItems == 0) {
				$('.notifyjs-corner').empty();
				exit();
			}

			$('.notifyjs-corner').empty();

			$.notify(
				'Total ' + totalItems + ' item checked', {
					position: "top center",
					className: 'success',
					clickToHide: false,
					autoHide: false
				}
				);
		});
	</script>