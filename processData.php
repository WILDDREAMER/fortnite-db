<?php 
require("includes/connection.php");
require("includes/function.php");
require("language/language.php");

require("language/app_language.php");

include("smtp_email.php");

$file_path_img = getBaseUrl();

$response=array();

$_SESSION['class'] = "success";

switch ($_POST['action']) {

	case 'toggle_status':
	{
		$table_nm = $_POST['table'];

		$sql_schema="SHOW COLUMNS FROM $table_nm";
		$res_schema=mysqli_query($mysqli, $sql_schema);
		$row_schema=mysqli_fetch_array($res_schema);
		
		$id = $_POST['id'];
		$for_action = $_POST['for_action'];
		$column = $_POST['column'];
		$tbl_id = $row_schema[0];

		$message='';

		if ($for_action == 'enable') {
			$data = array($column  =>  '1');
			$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
			$message=$client_lang['13'];
		}
		else if ($for_action == 'slider') {
			$data = array($column  =>  '1');
			$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
			$message=$client_lang['22'];
			$_SESSION['msg'] = "22";
		}
		else if ($for_action == 'remove_slider') {
			$data = array($column  =>  '0');
			$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
			$message=$client_lang['23'];
			$_SESSION['msg'] = "23";
		}
		else {
			$data = array($column  =>  '0');
			$edit_status = Update($table_nm, $data, "WHERE $tbl_id = '$id'");
			$message=$client_lang['14'];
		}

		$response['status'] = 1;
		$response['msg'] = $message;
		$response['action'] = $for_action;
		echo json_encode($response);
	}
	break;

	case 'multi_action': 
	{
		$action = $_POST['for_action'];
		$ids = implode(",", $_POST['id']);
		$tbl_nm = $_POST['table'];

		if ($ids == '') {
			$ids = $_POST['id'];
		}

		if ($action == 'enable') {
			$sql = "UPDATE $tbl_nm SET `status`='1' WHERE `id` IN ($ids)";
			mysqli_query($mysqli, $sql);
			$_SESSION['msg'] = "13";

		} 
		else if ($action == 'disable') {
			$sql = "UPDATE $tbl_nm SET `status`='0' WHERE `id` IN ($ids)";
			if (mysqli_query($mysqli, $sql)) {
				$_SESSION['msg'] = "14";
			}
		}
		else if ($action == 'slider') {
			$sql = "UPDATE $tbl_nm SET `featured`='1' WHERE `id` IN ($ids)";
			mysqli_query($mysqli, $sql);
			$_SESSION['msg'] = "22";

		} 
		else if ($action == 'remove_slider') {
			$sql = "UPDATE $tbl_nm SET `featured`='0' WHERE `id` IN ($ids)";
			if (mysqli_query($mysqli, $sql)) {
				$_SESSION['msg'] = "23";
			}
		} 
		else if ($action == 'delete')
		{
			if($tbl_nm=='tbl_color'){
				$deleteSql = "DELETE FROM $tbl_nm WHERE `color_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			}
			else if($tbl_nm=='tbl_category'){

				$sql='SELECT * FROM tbl_wallpaper WHERE `cat_id` IN ('.$ids.')';
				
				$result=mysqli_query($mysqli,$sql);

				while ($row=mysqli_fetch_assoc($result)){
					if($row['image']!="")
					{
						unlink('categories/'.$row['cat_id'].'/'.$row['image']);
						unlink('categories/'.$row['cat_id'].'/thumbs/'.$row['image']);
					}
				}

				mysqli_free_result($result);

				$deleteSql = "DELETE FROM tbl_wallpaper WHERE `cat_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$sql='SELECT * FROM tbl_category WHERE `cid` IN ('.$ids.')';
				
				$result=mysqli_query($mysqli,$sql);

				while ($cat_res_row=mysqli_fetch_assoc($result)){
					
					if($cat_res_row['category_image']!="")
					{
						unlink('images/'.$cat_res_row['category_image']);
						unlink('images/thumbs/'.$cat_res_row['category_image']);
					}

					Delete('tbl_category','cid='.$cat_res_row['cid'].'');

					if(!rmdir('categories/'.$cat_res_row['cid']))
					{
						rmdir('categories/'.$cat_res_row['cid'].'/thumbs');
						rmdir('categories/'.$cat_res_row['cid']);       
					}
				}

				mysqli_free_result($result);

			}
			else if($tbl_nm=='tbl_wallpaper'){

				$sql='SELECT * FROM tbl_wallpaper WHERE `id` IN ('.$ids.')';
				
				$result=mysqli_query($mysqli,$sql);

				while ($row=mysqli_fetch_assoc($result)){
					if($row['image']!="")
					{
						unlink('categories/'.$row['cat_id'].'/'.$row['image']);
						unlink('categories/'.$row['cat_id'].'/thumbs/'.$row['image']);
					}
				}

				mysqli_free_result($result);

				$deleteSql = "DELETE FROM tbl_wallpaper WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

			}
			else if($tbl_nm=='tbl_wallpaper_gif'){

				$sql='SELECT * FROM tbl_wallpaper_gif WHERE `id` IN ('.$ids.')';
				
				$result=mysqli_query($mysqli,$sql);

				while ($row=mysqli_fetch_assoc($result)){
					if($row['image']!="")
					{
						unlink('images/animation/'.$row['image']);
					}
				}

				mysqli_free_result($result);

				$deleteSql = "DELETE FROM tbl_wallpaper_gif WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

			}
			else if($tbl_nm=='tbl_user_report'){
				$deleteSql = "DELETE FROM $tbl_nm WHERE `user_report_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			}
			else if($tbl_nm=='tbl_users'){
				$deleteSql = "DELETE FROM tbl_rating WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql = "DELETE FROM tbl_favorite WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql = "DELETE FROM tbl_rating_gif WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql = "DELETE FROM tbl_user_report WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql = "DELETE FROM tbl_active_log WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql = "DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);
			}

			$_SESSION['msg'] = "12";
		}

		$response['status'] = 1;
		echo json_encode($response);
	}
	break;
	
	case 'check_smtp':
	{
		$to = trim($_POST['email']);
		$recipient_name='Check User';
		
		$subject = '[IMPORTANT] '.APP_NAME.' Check SMTP Configuration';

		$message='<div style="background-color: #f9f9f9;" align="center"><br />
		<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
		<tbody>
		<tr>
		<td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path_img.'/images/'.APP_LOGO.'" alt="header" /></td>
		</tr>
		<tr>
		<td width="600" valign="top" bgcolor="#FFFFFF"><br>
		<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
		<tbody>
		<tr>
		<td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
		<tbody>
		<tr>
		<td>
		<p style="color: #262626; font-size: 24px; margin-top:0px;">Hi, '.$_SESSION['admin_name'].'</p>
		<p style="color: #262626; font-size: 18px; margin-top:0px;">This is the demo mail to check SMTP Configuration. </p>
		<p style="color:#262626; font-size:17px; line-height:32px;font-weight:500;margin-bottom:30px;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>

		</td>
		</tr>
		</tbody>
		</table></td>
		</tr>

		</tbody>
		</table></td>
		</tr>
		<tr>
		<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
		</tr>
		</tbody>
		</table>
		</div>';

		send_email($to,$recipient_name,$subject,$message, true);
		break;
	}

	default:
	
	break;
}

?>