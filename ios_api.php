<?php 
include("includes/connection.php");
include("includes/function.php"); 
include("language/app_language.php"); 	
include("smtp_email.php");

$file_path = getBaseUrl();

define("PACKAGE_NAME",$settings_details['package_name']);

define("DEFAULT_PASSWORD",'123');

define("IOS_BUNDLE_IDENTIFIER",$settings_details['ios_bundle_identifier']);

function get_thumb($filename,$thumb_size)
{	
	global $file_path;
	return $thumb_path=$file_path.'thumb.php?src='.$filename.'&size='.$thumb_size;
}

function get_resolution($filename)
{	
	$data = getimagesize($filename);
	$width = $data[0];
	$height = $data[1];

	return $width.'X'.$height;
}

function get_size($filename)
{	 
	$size = filesize($filename);
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$power = $size > 0 ? floor(log($size, 1024)) : 0;
	return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];

}

function get_total_wallpaper($cat_id,$type=null)
{	
	global $mysqli;

	if($type==null || $type==''){
		$qry_wallpaper="SELECT COUNT(*) as num FROM tbl_wallpaper WHERE cat_id='".$cat_id."'";	
	}else{
		$type=trim($type);
		$qry_wallpaper="SELECT COUNT(*) as num FROM tbl_wallpaper WHERE cat_id='".$cat_id."' AND `wallpaper_type`='$type'";
	}

	$total_wallpaper = mysqli_fetch_array(mysqli_query($mysqli,$qry_wallpaper));
	$total_wallpaper = $total_wallpaper['num'];

	return $total_wallpaper;

}

function is_favorite($id,$type='wallpaper',$user_id='')
{	
	global $mysqli;

	$sql="SELECT * FROM tbl_favorite WHERE `post_id`='$id' AND `user_id`='$user_id' AND `type`='$type'";
	$result=mysqli_query($mysqli, $sql);

	if(mysqli_num_rows($result) > 0){
		return 'true';
	}
	else{
		return 'false';
	}
}

function generateRandomPassword($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

if($settings_details['envato_buyer_name']=='' OR $settings_details['envato_ios_purchase_code']=='' OR $settings_details['envato_ios_purchased_status']==0){ 

	$set['HD_WALLPAPER'][] =array('MSG' => 'Purchase code verification failed!','success'=>-1);

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}

$get_method = checkSignSalt($_POST['data']);

if($get_method['method_name']=="get_home")	
{
	$home_limit = HOME_LIMIT;

	$user_id=cleanInput($get_method['user_id']);

	$jsonObj= array();
	$data_arr= array();

	if($get_method['type']!=''){

		$type=trim($get_method['type']);

		$sql="SELECT * FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_wallpaper.`featured`='1' AND tbl_category.`status`='1' AND tbl_wallpaper.`wallpaper_type`='$type'
		ORDER BY tbl_wallpaper.`id` DESC LIMIT $home_limit";

		$result = mysqli_query($mysqli,$sql);

		while($data = mysqli_fetch_assoc($result))
		{
			$data_arr['id'] = $data['id'];
			$data_arr['cat_id'] = $data['cat_id'];
			$data_arr['wallpaper_type'] = $data['wallpaper_type'];
			$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
			$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
			$data_arr['total_views'] = $data['total_views'];
			$data_arr['total_rate'] = $data['total_rate'];
			$data_arr['rate_avg'] = $data['rate_avg'];

			$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

			$data_arr['wall_tags'] = $data['wall_tags'];	        
			$data_arr['wall_colors'] = $data['wall_colors'];

			$data_arr['cid'] = $data['cid'];
			$data_arr['category_name'] = $data['category_name'];
			$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
			$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

			array_push($jsonObj,$data_arr);
			
		}

		$row['featured_wallpaper']=$jsonObj;

		mysqli_free_result($result);

		$jsonObj=array();
		$data_arr=array();
		
		$cid=API_CAT_ORDER_BY;

		$sql="SELECT * FROM tbl_category WHERE status='1' ORDER BY tbl_category.".$cid."";
		$result = mysqli_query($mysqli,$sql);

		while($data = mysqli_fetch_assoc($result))
		{

			$data_arr['cid'] = $data['cid'];
			$data_arr['category_name'] = $data['category_name'];
			$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
			$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');
			$data_arr['category_total_wall'] = get_total_wallpaper($data['cid'],$type);

			array_push($jsonObj,$data_arr);
		}

		$row['wallpaper_category']=$jsonObj;

		mysqli_free_result($result);

		$jsonObj=array();
		$data_arr=array();	

		$sql="SELECT * FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_wallpaper.`wallpaper_type`='$type' AND tbl_category.`status`='1' 
		ORDER BY tbl_wallpaper.`id` DESC LIMIT $home_limit";


		$result = mysqli_query($mysqli,$sql);

		while($data = mysqli_fetch_assoc($result))
		{
			$data_arr['id'] = $data['id'];
			$data_arr['cat_id'] = $data['cat_id'];
			$data_arr['wallpaper_type'] = $data['wallpaper_type'];
			$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
			$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
			$data_arr['total_views'] = $data['total_views'];
			$data_arr['total_rate'] = $data['total_rate'];
			$data_arr['rate_avg'] = $data['rate_avg'];

			$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

			$data_arr['wall_tags'] = $data['wall_tags'];
			$data_arr['wall_colors'] = $data['wall_colors'];

			$data_arr['cid'] = $data['cid'];
			$data_arr['category_name'] = $data['category_name'];
			$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
			$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');


			array_push($jsonObj,$data_arr);
			
		}

		$row['latest_wallpaper']=$jsonObj;

		mysqli_free_result($result);

		$jsonObj=array();
		$data_arr=array();

		$sql="SELECT * FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_wallpaper.`wallpaper_type`='$type' AND tbl_category.`status`='1'
		ORDER BY tbl_wallpaper.`total_views` DESC LIMIT $home_limit";

		$result = mysqli_query($mysqli,$sql);

		while($data = mysqli_fetch_assoc($result))
		{
			$data_arr['id'] = $data['id'];
			$data_arr['cat_id'] = $data['cat_id'];
			$data_arr['wallpaper_type'] = $data['wallpaper_type'];
			$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
			$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
			$data_arr['total_views'] = $data['total_views'];
			$data_arr['total_rate'] = $data['total_rate'];
			$data_arr['rate_avg'] = $data['rate_avg'];

			$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

			$data_arr['wall_tags'] = $data['wall_tags'];	        
			$data_arr['wall_colors'] = $data['wall_colors'];	        

			$data_arr['cid'] = $data['cid'];
			$data_arr['category_name'] = $data['category_name'];
			$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
			$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

			array_push($jsonObj,$data_arr);
			
		}

		$row['popular_wallpaper']=$jsonObj;

		mysqli_free_result($result);

		$jsonObj=array();
		$data_arr=array();
		
		$sql="SELECT * FROM tbl_color WHERE color_status='1' ORDER BY color_id DESC";
		$result = mysqli_query($mysqli,$sql);

		while($data = mysqli_fetch_assoc($result))
		{

			$data_arr['color_id'] = $data['color_id'];
			$data_arr['color_name'] = $data['color_name'];
			$data_arr['color_code'] = $data['color_code'];

			array_push($jsonObj,$data_arr);
			
		}

		$row['wallpaper_colors']=$jsonObj;

		mysqli_free_result($result);

	}

	$ids=$get_method['id'];

	$jsonObj= array();
	$data_arr= array();

	if($ids!=''){
		$sql="SELECT * FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
		WHERE tbl_wallpaper.`id` IN ($ids) AND tbl_category.`status`='1' LIMIT $home_limit";

		$result = mysqli_query($mysqli,$sql) or die (mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($result))
		{	
			$data_arr['num'] = $total_pages['num'];
			$data_arr['id'] = $data['id'];
			$data_arr['cat_id'] = $data['cat_id'];
			$data_arr['wallpaper_type'] = $data['wallpaper_type'];
			$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
			$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
			$data_arr['total_views'] = $data['total_views']; 
			$data_arr['total_rate'] = $data['total_rate'];
			$data_arr['rate_avg'] = $data['rate_avg'];

			$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

			$data_arr['wall_tags'] = $data['wall_tags'];
			$data_arr['wall_colors'] = $data['wall_colors'];

			$data_arr['cid'] = $data['cid'];
			$data_arr['category_name'] = $data['category_name'];
			$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
			$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

			array_push($jsonObj,$data_arr);
		}

	}

	$row['recent_wallpapers']=$jsonObj;


	$set['HD_WALLPAPER'] = $row;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if ($get_method['method_name']=="get_latest") {

	$page_limit=API_LATEST_LIMIT;

	$jsonObj=array();
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);

	$limit=($get_method['page']-1) * $page_limit;
	$colors_arr=explode(',', $get_method['color_id']);

	if($get_method['type']!='')
	{

		$type=cleanInput($get_method['type']);

		$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
		WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_category.`status`='1'
		ORDER BY tbl_wallpaper.`id`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`wallpaper_type`='$type' AND tbl_category.`status`='1' AND ($column)
			ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";

		}else{

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";
		}
	}
	else
	{
		$query_rec="SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_category.`status`='1' 
		ORDER BY tbl_wallpaper.`id`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE
			($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_category.`status`='1' 
			ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";
		}
	}

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{	
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];
		$data_arr['cat_id'] = $data['cat_id'];
		$data_arr['wallpaper_type'] = $data['wallpaper_type'];
		$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
		$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

		$data_arr['wall_tags'] = $data['wall_tags'];
		$data_arr['wall_colors'] = $data['wall_colors'];

		$data_arr['cid'] = $data['cid'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
		$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');
		
		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_category") 
{

	$jsonObj=array();
	$data_arr=array();

	$cat_order=API_CAT_ORDER_BY;

	$type=$get_method['type'];
	
	$sql="SELECT * FROM tbl_category WHERE tbl_category.`status`='1' ORDER BY ".$cat_order."";

	$result = mysqli_query($mysqli,$sql)or die(mysqli_error($mysqli));

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['cid'] = $data['cid'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
		$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');
		$data_arr['category_total_wall'] = get_total_wallpaper($data['cid'],$type);

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_wallpaper")
{
	$jsonObj=array();
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);

	$post_order_by=API_CAT_POST_ORDER_BY;

	$page_limit=10;

	$cat_id=$get_method['cat_id'];	
	$limit=($get_method['page']-1) * $page_limit;
	$colors_arr=explode(',', $get_method['color_id']);

	if($get_method['type']!='')
	{
		$type=cleanInput($get_method['type']);

		$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
		WHERE tbl_wallpaper.`cat_id`='$cat_id' AND tbl_category.`status`='1' AND tbl_wallpaper.`wallpaper_type` = '$type' ORDER BY tbl_wallpaper.`id`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`cat_id`='$cat_id' AND tbl_category.`status`='1' AND tbl_wallpaper.`wallpaper_type` = '$type'
			AND ($column)
			ORDER BY tbl_wallpaper.`id` $post_order_by LIMIT $limit, $page_limit";

		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`cat_id`='$cat_id' AND tbl_category.`status`='1' AND tbl_wallpaper.`wallpaper_type` = '$type'
			ORDER BY tbl_wallpaper.`id` $post_order_by LIMIT $limit, $page_limit";
		}

	}
	else
	{
		$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
		WHERE tbl_wallpaper.`cat_id`='$cat_id' AND tbl_category.`status`='1' ORDER BY tbl_wallpaper.`id` DESC";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){

			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`cat_id`='$cat_id' AND ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`id` $post_order_by LIMIT $limit, $page_limit";

		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`cat_id`='$cat_id' AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`id` $post_order_by LIMIT $limit, $page_limit";
		}
	}

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];
		$data_arr['cat_id'] = $data['cat_id'];
		$data_arr['wallpaper_type'] = $data['wallpaper_type'];
		$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
		$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
		$data_arr['total_views'] = $data['total_views'];
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

		$data_arr['wall_tags'] = $data['wall_tags'];
		$data_arr['wall_colors'] = $data['wall_colors'];

		$data_arr['cid'] = $data['cid'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
		$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');
		
		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_single_wallpaper")
{
	
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$wallpaper_id=cleanInput($get_method['wallpaper_id']);

	$sql="SELECT * FROM tbl_wallpaper 
	LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
	WHERE tbl_wallpaper.`id` = '$wallpaper_id' AND tbl_category.`status`='1'";

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{

		$data_arr['id'] = $data['id'];
		$data_arr['cat_id'] = $data['cat_id'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['wallpaper_type'] = $data['wallpaper_type'];
		$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
		$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
		$data_arr['total_views'] = $data['total_views'];
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

		$data_arr['total_download'] = $data['total_download'];
		$data_arr['wall_tags'] = $data['wall_tags'];
		$data_arr['wall_colors'] = $data['wall_colors'];

		$data_arr['resolution'] = get_resolution($file_path.'categories/'.$data['cat_id'].'/'.$data['image']);
		$data_arr['size'] = get_size('categories/'.$data['cat_id'].'/'.$data['image']);

		array_push($jsonObj,$data_arr);

	}

	$view_qry=mysqli_query($mysqli,"UPDATE tbl_wallpaper SET `total_views` = `total_views` + 1 WHERE id = '$wallpaper_id'");

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_wallpaper_most_viewed") 
{
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$page_limit=10;
	
	$limit=($get_method['page']-1) * $page_limit;

	$colors_arr=explode(',', $get_method['color_id']);

	if($get_method['type']!='')
	{
		$type=cleanInput($get_method['type']);

		$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_category.`status`='1' 
		ORDER BY tbl_wallpaper.`total_views` DESC";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));	

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`total_views` DESC LIMIT $limit, $page_limit";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_category.`status`='1' 
			ORDER BY tbl_wallpaper.`total_views` DESC LIMIT $limit, $page_limit";
		}

	}
	else
	{
		$query_rec="SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_category.`status`='1' 
		ORDER BY tbl_wallpaper.`total_views`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`total_views` DESC LIMIT $limit, $page_limit";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`total_views` DESC LIMIT $limit, $page_limit";
		}
	}

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];
		$data_arr['cat_id'] = $data['cat_id'];
		$data_arr['wallpaper_type'] = $data['wallpaper_type'];
		$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
		$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

		$data_arr['wall_tags'] = $data['wall_tags'];
		$data_arr['wall_colors'] = $data['wall_colors'];

		$data_arr['cid'] = $data['cid'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
		$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');
		
		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_wallpaper_most_rated")
{
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$page_limit=10;
	
	$limit=($get_method['page']-1) * $page_limit;

	$colors_arr=explode(',', $get_method['color_id']);

	if($get_method['type']!='')
	{
		$type=$get_method['type'];

		$query_rec="SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
		WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_category.`status`='1' 
		ORDER BY tbl_wallpaper.`total_rate` DESC";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_wallpaper.`wallpaper_type` = '$type'
			AND ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`total_rate` DESC LIMIT $limit, $page_limit";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_category.`status`='1' 
			ORDER BY tbl_wallpaper.`total_rate` DESC LIMIT $limit, $page_limit";
		}

	}
	else
	{
		$query_rec="SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
		WHERE tbl_category.`status`='1'
		ORDER BY tbl_wallpaper.`total_rate`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`total_rate` DESC LIMIT $limit, $page_limit";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`total_rate` DESC LIMIT $limit, $page_limit";
		}	
	}

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];
		$data_arr['cat_id'] = $data['cat_id'];
		$data_arr['wallpaper_type'] = $data['wallpaper_type'];
		$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
		$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

		$data_arr['wall_tags'] = $data['wall_tags'];
		$data_arr['wall_colors'] = $data['wall_colors'];
		$data_arr['cid'] = $data['cid'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
		$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_latest_gif") 
{
	$jsonObj=array();
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);
	
	$page_limit=API_LATEST_LIMIT;

	$limit=($get_method['page']-1) * $page_limit;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper_gif
	ORDER BY tbl_wallpaper_gif.`id`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

	$sql="SELECT * FROM tbl_wallpaper_gif
	ORDER BY tbl_wallpaper_gif.`id` DESC LIMIT $limit, $page_limit";

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{	
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];			 
		$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
		$data_arr['gif_tags'] = $data['gif_tags'];
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if($get_method['method_name']=="get_check_favorite")
{
	$jsonObj= array();
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);

	$page_limit=API_LATEST_LIMIT;

	$limit=($get_method['page']-1) * $page_limit;

	$ids=$get_method['id'];	

	switch ($get_method['type']) {
		case 'wallpaper':
		{
			$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`id` IN ($ids) AND tbl_category.`status`='1'   
			ORDER BY tbl_wallpaper.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`id` IN ($ids) AND tbl_category.`status`='1'
			LIMIT $limit, $page_limit";

			$result = mysqli_query($mysqli,$sql) or die (mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($result))
			{	
				$data_arr['num'] = $total_pages['num'];
				$data_arr['id'] = $data['id'];
				$data_arr['cat_id'] = $data['cat_id'];
				$data_arr['wallpaper_type'] = $data['wallpaper_type'];
				$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
				$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
				$data_arr['total_views'] = $data['total_views']; 
				$data_arr['total_rate'] = $data['total_rate'];
				$data_arr['rate_avg'] = $data['rate_avg'];

				$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

				$data_arr['wall_tags'] = $data['wall_tags'];
				$data_arr['wall_colors'] = $data['wall_colors'];

				$data_arr['cid'] = $data['cid'];
				$data_arr['category_name'] = $data['category_name'];
				$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
				$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

				array_push($jsonObj,$data_arr);
			}
		}
		break;

		case 'gif':
		{
			$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper_gif
			WHERE tbl_wallpaper_gif.`id` IN ($ids)
			ORDER BY tbl_wallpaper_gif.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$sql="SELECT * FROM tbl_wallpaper_gif WHERE tbl_wallpaper_gif.`id` IN ($ids)
			LIMIT $limit,$page_limit";
			
			$result = mysqli_query($mysqli,$sql) or die (mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($result))
			{	
				$data_arr['num'] = $total_pages['num'];
				$data_arr['id'] = $data['id'];			 
				$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
				$data_arr['gif_tags'] = $data['gif_tags'];
				$data_arr['total_views'] = $data['total_views']; 
				$data_arr['total_rate'] = $data['total_rate'];
				$data_arr['rate_avg'] = $data['rate_avg'];

				$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

				array_push($jsonObj,$data_arr);
			}
		}
		break;

		default:
		{
		}
		break;
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}

else if($get_method['method_name']=="get_recent_post")
{
	$jsonObj= array();
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);

	$page_limit=API_LATEST_LIMIT;

	$limit=($get_method['page']-1) * $page_limit;

	$ids=$get_method['id'];	

	switch ($get_method['type']) {
		case 'wallpaper':
		{
			$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`id` IN ($ids) AND tbl_category.`status`='1'   
			ORDER BY tbl_wallpaper.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE tbl_wallpaper.`id` IN ($ids) AND tbl_category.`status`='1'
			LIMIT $limit, $page_limit";

			$result = mysqli_query($mysqli,$sql) or die (mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($result))
			{	
				$data_arr['num'] = $total_pages['num'];
				$data_arr['id'] = $data['id'];
				$data_arr['cat_id'] = $data['cat_id'];
				$data_arr['wallpaper_type'] = $data['wallpaper_type'];
				$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
				$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
				$data_arr['total_views'] = $data['total_views']; 
				$data_arr['total_rate'] = $data['total_rate'];
				$data_arr['rate_avg'] = $data['rate_avg'];

				$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

				$data_arr['wall_tags'] = $data['wall_tags'];
				$data_arr['wall_colors'] = $data['wall_colors'];

				$data_arr['cid'] = $data['cid'];
				$data_arr['category_name'] = $data['category_name'];
				$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
				$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

				array_push($jsonObj,$data_arr);
			}
		}
		break;

		case 'gif':
		{
			$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper_gif
			WHERE tbl_wallpaper_gif.`id` IN ($ids)
			ORDER BY tbl_wallpaper_gif.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$sql="SELECT * FROM tbl_wallpaper_gif WHERE tbl_wallpaper_gif.`id` IN ($ids)
			LIMIT $limit,$page_limit";
			
			$result = mysqli_query($mysqli,$sql) or die (mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($result))
			{	
				$data_arr['num'] = $total_pages['num'];
				$data_arr['id'] = $data['id'];			 
				$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
				$data_arr['gif_tags'] = $data['gif_tags'];
				$data_arr['total_views'] = $data['total_views']; 
				$data_arr['total_rate'] = $data['total_rate'];
				$data_arr['rate_avg'] = $data['rate_avg'];

				$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

				array_push($jsonObj,$data_arr);
			}
		}
		break;

		default:
		{
		}
		break;
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}

else if ($get_method['method_name']=="get_gif_list") 
{
	
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$gif_order=API_GIF_POST_ORDER_BY;

	$sql="SELECT * FROM tbl_wallpaper_gif ORDER BY `id` $gif_order";
	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['id'] = $data['id'];			 
		$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
		$data_arr['gif_tags'] = $data['gif_tags'];
		$data_arr['total_views'] = $data['total_views'];
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;
	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_single_gif") 
{
	
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$gif_id=cleanInput($get_method['gif_id']);

	$sql="SELECT * FROM tbl_wallpaper_gif WHERE `id`='$gif_id'";
	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{ 
		$data_arr['id'] = $data['id'];			 
		$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
		$data_arr['gif_tags'] = $data['gif_tags'];
		$data_arr['total_views'] = $data['total_views'];
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

		$data_arr['total_download'] = $data['total_download'];
		$data_arr['resolution'] = get_resolution($file_path.'images/animation/'.$data['image']);
		$data_arr['size'] = get_size('images/animation/'.$data['image']);

		array_push($jsonObj,$data_arr);
	}

	$view_qry=mysqli_query($mysqli,"UPDATE tbl_wallpaper_gif SET `total_views` = `total_views` + 1 WHERE `id` = '$gif_id'");

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_gif_wallpaper_most_viewed") 
{
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$page_limit=10;

	$limit=($get_method['page']-1) * $page_limit;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper_gif
	ORDER BY tbl_wallpaper_gif.`total_views`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

	$sql="SELECT * FROM tbl_wallpaper_gif
	ORDER BY tbl_wallpaper_gif.`total_views` DESC LIMIT $limit, $page_limit";

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];			 
		$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
		$data_arr['gif_tags'] = $data['gif_tags'];
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;
	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="get_gif_wallpaper_most_rated") 
{
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);

	$page_limit=10;

	$limit=($get_method['page']-1) * $page_limit;

	$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper_gif
	ORDER BY tbl_wallpaper_gif.`total_rate`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

	$sql="SELECT * FROM tbl_wallpaper_gif ORDER BY tbl_wallpaper_gif.`total_rate` DESC LIMIT $limit, $page_limit";

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];			 
		$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
		$data_arr['gif_tags'] = $data['gif_tags'];
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
	
}
else if ($get_method['method_name']=="search_wallpaper") 
{
	
	$jsonObj= array();	
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);

	$colors_arr=explode(',', $get_method['color_id']);

	$keyword=cleanInput($get_method['search_text']);

	if($get_method['type']!='')
	{
		$type=cleanInput($get_method['type']);

		$query_rec="SELECT COUNT(*) as num FROM tbl_wallpaper
		LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
		WHERE tbl_wallpaper.`wallpaper_type`='$type' AND tbl_wallpaper.`wall_tags` LIKE '%$keyword%' AND tbl_category.`status`='1'
		ORDER BY tbl_wallpaper.`wall_tags`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		if($colors_arr[0]!=''){

			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
			WHERE 
			tbl_wallpaper.`wallpaper_type`='$type' AND tbl_wallpaper.`wall_tags` LIKE '%$keyword%' 
			AND ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`wall_tags`";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE 
			tbl_wallpaper.`wallpaper_type`='$type' AND tbl_wallpaper.`wall_tags` LIKE '%$keyword%' AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`wall_tags`";
		}

	}
	else
	{
		if($colors_arr[0]!=''){
			$column='';
			foreach ($colors_arr as $key => $value) {
				$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
			}

			$column=rtrim($column,'OR ');

			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE 
			tbl_wallpaper.`wall_tags` LIKE '%$keyword%' AND ($column) AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`wall_tags`";
		}else{
			$sql="SELECT * FROM tbl_wallpaper
			LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
			WHERE 
			tbl_wallpaper.`wall_tags` LIKE '%$keyword%' AND tbl_category.`status`='1'
			ORDER BY tbl_wallpaper.`wall_tags`";
		}
	}

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$data_arr['num'] = $total_pages['num'];
		$data_arr['id'] = $data['id'];
		$data_arr['cat_id'] = $data['cat_id'];
		$data_arr['wallpaper_type'] = $data['wallpaper_type'];
		$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
		$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
		$data_arr['total_views'] = $data['total_views']; 
		$data_arr['total_rate'] = $data['total_rate'];
		$data_arr['rate_avg'] = $data['rate_avg'];

		$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

		$data_arr['wall_tags'] = $data['wall_tags'];
		$data_arr['wall_colors'] = $data['wall_colors'];
		$data_arr['cid'] = $data['cid'];
		$data_arr['category_name'] = $data['category_name'];
		$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
		$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

		array_push($jsonObj,$data_arr);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();	

}
else if ($get_method['method_name']=="search_gif") 
{

	$jsonObj=array();
	$data_arr=array();

	$user_id=cleanInput($get_method['user_id']);

	$keyword=cleanInput($get_method['gif_search_text']);

	$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper_gif WHERE `gif_tags` LIKE '%$keyword%' ORDER BY tbl_wallpaper_gif.`gif_tags`";

	$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

	$sql="SELECT * FROM tbl_wallpaper_gif WHERE `gif_tags` LIKE '%$keyword%' ORDER BY tbl_wallpaper_gif.`gif_tags`";

	$result = mysqli_query($mysqli,$sql);

	while($data = mysqli_fetch_assoc($result))
	{
		$row['num'] = $total_pages['num'];
		$row['id'] = $data['id'];			 
		$row['gif_image'] = $file_path.'images/animation/'.$data['image'];
		$row['gif_tags'] = $data['gif_tags'];
		$row['total_views'] = $data['total_views'];
		$row['total_rate'] = $data['total_rate'];
		$row['rate_avg'] = $data['rate_avg'];

		$row['is_favorite']=is_favorite($data['id'],'gif',$user_id);

		array_push($jsonObj,$row);
	}

	$set['HD_WALLPAPER'] = $jsonObj;

	mysqli_free_result($result);

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();	

}
else if ($get_method['method_name']=="wallpaper_rate") 
{

	$jsonObj= array();	

	$ip = cleanInput($get_method['device_id']);
	$post_id = cleanInput($get_method['post_id']);
	$user_id = cleanInput($get_method['device_id']);
	$therate = cleanInput($get_method['rate']);

	$result = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE `post_id` = '$post_id' AND `user_id` = '$user_id'"); 

	if(mysqli_num_rows($result) == 0){

		mysqli_query($mysqli, "INSERT INTO tbl_rating(`post_id`, `user_id`, `rate`, `ip`) VALUES ('$post_id', '$user_id', '$therate', '$ip')");

		$query = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE `post_id` = '$post_id'");

		while($data = mysqli_fetch_assoc($query)){
			$rate_db[] = $data;
			$sum_rates[] = $data['rate'];
		}

		if(@count($rate_db)){
			$rate_times = count($rate_db);
			$sum_rates = array_sum($sum_rates);
			$rate_value = $sum_rates/$rate_times;
			$rate_bg = (($rate_value)/5)*100;
		}else{
			$rate_times = 0;
			$rate_value = 0;
			$rate_bg = 0;
		}

		$rate_avg=round($rate_value); 

		$sql="UPDATE tbl_wallpaper SET `total_rate`=`total_rate` + 1, `rate_avg` = '$rate_avg' WHERE `id`='$post_id'";

		mysqli_query($mysqli,$sql);

		$total_rat_sql="SELECT * FROM tbl_wallpaper WHERE `id`='$post_id'";
		$total_rat_res=mysqli_query($mysqli,$total_rat_sql);
		$total_rat_row=mysqli_fetch_assoc($total_rat_res);

		$jsonObj = array( 'total_rate' => $total_rat_row['total_rate'],'rate_avg' =>$total_rat_row['rate_avg'],'MSG'=>$app_lang['rate_success']); 

	}
	else{
		$jsonObj = array( 'MSG' => $app_lang['rate_already']);
	}

	$set['HD_WALLPAPER'][] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if ($get_method['method_name']=="get_wallpaper_rate") 
{
	$jsonObj= array();	

	$post_id = cleanInput($get_method['post_id']);
	$user_id = cleanInput($get_method['device_id']);

	$result = mysqli_query($mysqli,"select * from tbl_rating where post_id  = '$post_id' && user_id = '$user_id'"); 

	if(mysqli_num_rows($result) > 0){
		$data = mysqli_fetch_assoc($result);

		$jsonObj = array( 'total_rate' => $data['rate']);
	}
	else{
		$jsonObj = array( 'total_rate' => 0);
	}

	$set['HD_WALLPAPER'][] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if ($get_method['method_name']=="gif_rate") 
{

	$jsonObj= array();	

	$ip = cleanInput($get_method['device_id']);
	$post_id = cleanInput($get_method['post_id']);
	$user_id = cleanInput($get_method['device_id']);
	$therate = cleanInput($get_method['rate']);

	$result = mysqli_query($mysqli,"SELECT * FROM tbl_rating_gif WHERE `post_id` = '$post_id' AND `user_id` = '$user_id'"); 

	if(mysqli_num_rows($result) == 0){

		mysqli_query($mysqli, "INSERT INTO tbl_rating_gif(`post_id`, `user_id`, `rate`, `ip`) VALUES ('$post_id', '$user_id', '$therate', '$ip')"); 

		$query = mysqli_query($mysqli,"SELECT * FROM tbl_rating_gif WHERE `post_id` = '$post_id'");

		while($data = mysqli_fetch_assoc($query)){
			$rate_db[] = $data;
			$sum_rates[] = $data['rate'];
		}

		if(@count($rate_db)){
			$rate_times = count($rate_db);
			$sum_rates = array_sum($sum_rates);
			$rate_value = $sum_rates/$rate_times;
			$rate_bg = (($rate_value)/5)*100;
		}else{
			$rate_times = 0;
			$rate_value = 0;
			$rate_bg = 0;
		}

		$rate_avg=round($rate_value); 

		$sql="UPDATE tbl_wallpaper_gif SET `total_rate` = `total_rate` + 1, `rate_avg` = '$rate_avg' where id='$post_id'";

		mysqli_query($mysqli,$sql);

		$total_rat_sql="SELECT * FROM tbl_wallpaper_gif WHERE id='$post_id'";
		$total_rat_res=mysqli_query($mysqli,$total_rat_sql);
		$total_rat_row=mysqli_fetch_assoc($total_rat_res);

		$jsonObj = array( 'total_rate' => $total_rat_row['total_rate'],'rate_avg' =>$total_rat_row['rate_avg'],'MSG'=>$app_lang['rate_success']);

	}
	else{
		$jsonObj = array( 'MSG' => $app_lang['rate_already']);
	}

	$set['HD_WALLPAPER'][] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();

}
else if ($get_method['method_name']=="get_gif_rate") 
{
	$jsonObj= array();	

	$post_id = cleanInput($get_method['post_id']);
	$user_id = cleanInput($get_method['device_id']);

	$result = mysqli_query($mysqli,"SELECT * FROM tbl_rating_gif WHERE `post_id` = '$post_id' AND `user_id` = '$user_id'"); 

	if(mysqli_num_rows($result) > 0){

		$data = mysqli_fetch_assoc($result);
		$jsonObj = array( 'total_rate' => $data['rate']);	
	}
	else{
		$jsonObj = array( 'total_rate' => 0);
	}

	$set['HD_WALLPAPER'][] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if ($get_method['method_name']=="download_wallpaper") {

	$jsonObj= array();	

	$id=cleanInput($get_method['wallpaper_id']);

	mysqli_query($mysqli,"UPDATE tbl_wallpaper SET `total_download` = `total_download` + 1 WHERE `id` = '$id'");

	$sql="SELECT * FROM tbl_wallpaper WHERE id='$id'";
	$result=mysqli_query($mysqli,$sql);
	$data=mysqli_fetch_assoc($result);

	$jsonObj = array( 'total_download' => $data['total_download']);

	$set['HD_WALLPAPER'][] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if ($get_method['method_name']=="download_gif") {

	$jsonObj= array();	

	$id=cleanInput($get_method['gif_id']);

	mysqli_query($mysqli,"UPDATE tbl_wallpaper_gif SET `total_download` = `total_download` + 1 WHERE `id` = '$id'");

	$sql="SELECT * FROM tbl_wallpaper_gif WHERE id='$id'";
	$result=mysqli_query($mysqli,$sql);
	$data=mysqli_fetch_assoc($result);

	$jsonObj = array( 'total_download' => $data['total_download']);

	$set['HD_WALLPAPER'][] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_method['method_name']=="get_app_details")
{

	$jsonObj= array();	

	$query="SELECT * FROM tbl_settings WHERE id='1'";
	$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

	$data = mysqli_fetch_assoc($sql);

	$type=explode(',', $data['item_type']);

	$row['ios_bundle_identifier'] = $data['ios_bundle_identifier'];

	$row['package_name'] = $data['package_name'];
	$row['app_name'] = $data['app_name'];
	$row['app_logo'] = $data['app_logo'];
	$row['app_version'] = $data['app_version'];
	$row['app_author'] = $data['app_author'];
	$row['app_contact'] = $data['app_contact'];
	$row['app_email'] = $data['app_email'];
	$row['app_website'] = $data['app_website'];
	$row['app_description'] = $data['app_description'];
	$row['app_developed_by'] = $data['app_developed_by'];

	$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);

	$row['publisher_id'] = $data['publisher_id'];
	$row['interstital_ad'] = $data['interstital_ad'];
	$row['interstital_ad_id'] = $data['interstital_ad_id'];
	$row['interstital_ad_click'] = $data['interstital_ad_click'];
	$row['banner_ad'] = $data['banner_ad'];
	$row['banner_ad_id'] = $data['banner_ad_id'];

	$row['facebook_interstital_ad'] = $data['facebook_interstital_ad'];
	$row['facebook_interstital_ad_id'] = $data['facebook_interstital_ad_id'];
	$row['facebook_interstital_ad_click'] = $data['facebook_interstital_ad_click'];		
	$row['facebook_banner_ad'] = $data['facebook_banner_ad'];
	$row['facebook_banner_ad_id'] = $data['facebook_banner_ad_id'];

	$row['facebook_native_ad'] = $data['facebook_native_ad'];
	$row['facebook_native_ad_id'] = $data['facebook_native_ad_id'];
	$row['facebook_native_ad_click'] = $data['facebook_native_ad_click'];		
	$row['admob_nathive_ad'] = $data['admob_nathive_ad'];
	$row['admob_native_ad_id'] = $data['admob_native_ad_id'];
	$row['admob_native_ad_click'] = $data['admob_native_ad_click'];

	$row['publisher_id_ios'] = $data['publisher_id_ios'];
	$row['interstital_ad_ios'] = $data['interstital_ad_ios'];
	$row['interstital_ad_id_ios'] = $data['interstital_ad_id_ios'];
	$row['interstital_ad_click_ios'] = $data['interstital_ad_click_ios'];
	$row['banner_ad_ios'] = $data['banner_ad_ios'];
	$row['banner_ad_id_ios'] = $data['banner_ad_id_ios'];

	$row['ios_facebook_interstital_ad'] = $data['ios_facebook_interstital_ad'];
	$row['ios_facebook_interstital_ad_id'] = $data['ios_facebook_interstital_ad_id'];
	$row['ios_facebook_interstital_ad_click'] = $data['ios_facebook_interstital_ad_click'];		
	$row['ios_facebook_banner_ad'] = $data['ios_facebook_banner_ad'];
	$row['ios_facebook_banner_ad_id'] = $data['ios_facebook_banner_ad_id'];

	$row['gif_on_off'] = $data['gif_on_off'];

	if(in_array('Portrait',$type) || empty($type)){ 
		$row['portrait'] = 'true';
	}else{
		$row['portrait'] = 'false';
	}

	if(in_array('Landscape',$type)){ 
		$row['landscape'] = 'true';
	}else{
		$row['landscape'] = 'false';
	}

	if(in_array('Square',$type)){ 
		$row['square'] = 'true';
	}else{
		$row['square'] = 'false';
	}

	$row['app_update_status'] = $data['app_update_status'];
	$row['app_new_version'] = $data['app_new_version'];
	$row['app_update_desc'] = stripslashes($data['app_update_desc']);
	$row['app_redirect_url'] = $data['app_redirect_url'];
	$row['cancel_update_status'] = $data['cancel_update_status'];

	$row['app_update_status_ios'] = $data['app_update_status_ios'];
	$row['app_new_version_ios'] = $data['app_new_version_ios'];
	$row['app_update_desc_ios'] = stripslashes($data['app_update_desc_ios']);
	$row['app_redirect_url_ios'] = $data['app_redirect_url_ios'];
	$row['cancel_update_status_ios'] = $data['cancel_update_status_ios'];

	array_push($jsonObj, $row);

	$set['HD_WALLPAPER'] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();	
}

else if($get_method['method_name']=="user_login")
{
	$email= trim($get_method['email']);
	$password = trim($get_method['password']);

	$auth_id = trim($get_method['auth_id']);

	$user_type = trim($get_method['type']);

	if($user_type=='normal' OR $user_type=='Normal'){

		$qry = "SELECT * FROM tbl_users WHERE email = '$email' AND (`user_type`='Normal' OR `user_type`='normal') AND `id` <> 0"; 
		$result = mysqli_query($mysqli,$qry);
		$num_rows = mysqli_num_rows($result);

		if($num_rows > 0){
			$row = mysqli_fetch_assoc($result);

			if($row['status']==1){
				if($row['password']==md5($password)){

					$user_id=$row['id'];

					save_activity_log($user_id);

					$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => '', 'success'=>'1');
				}
				else{
					$set['HD_WALLPAPER'][]=array('MSG' =>$app_lang['invalid_password'],'success'=>'0');
				}
			}
			else{
				$set['HD_WALLPAPER'][]=array('MSG' =>$app_lang['account_deactive'],'success'=>'0');
			}

		}
		else{
			$set['HD_WALLPAPER'][]=array('MSG' =>$app_lang['email_not_found'],'success'=>'0');	
		}
	}
	else if($user_type=='google' OR $user_type=='Google'){

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND (`user_type`='Google' OR `user_type`='google')";

		$res=mysqli_query($mysqli, $sql);

		if(mysqli_num_rows($res) > 0){

			$row = mysqli_fetch_assoc($res);

			if($row['status']==0){
				$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['account_deactive'],'success'=>'0');
			}	
			else
			{
				$user_id=$row['id'];

				save_activity_log($user_id);

				$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success'=>'1');

				$data = array(
					'auth_id'  =>  $auth_id
				);  

				$updatePlayerID=Update('tbl_users', $data, "WHERE `id` = '".$row['id']."'");
			}

		}
		else{
			$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['email_not_found'],'success'=>'0');
		}
	}
	else if($user_type=='apple' OR $user_type=='Apple'){

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND (`user_type`='Apple' OR `user_type`='apple')";

		$res=mysqli_query($mysqli, $sql);

		if(mysqli_num_rows($res) > 0){

			$row = mysqli_fetch_assoc($res);

			if($row['status']==0){
				$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['account_deactive'],'success'=>'0');
			}	
			else
			{
				$user_id=$row['id'];

				save_activity_log($user_id);

				$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success'=>'1');

				$data = array(
					'auth_id'  =>  $auth_id
				);  

				$updatePlayerID=Update('tbl_users', $data, "WHERE `id` = '".$row['id']."'");
			}

		}
		else{
			$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['email_not_found'],'success'=>'0');
		}
	}
	else if($user_type=='facebook' OR $user_type=='Facebook'){

		$sql = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND (`user_type`='Facebook' OR `user_type`='facebook')";

		$res=mysqli_query($mysqli, $sql);

		if(mysqli_num_rows($res) > 0){
			$row = mysqli_fetch_assoc($res);

			if($row['status']==0){
				$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['account_deactive'],'success'=>'0');
			}	
			else
			{
				$user_id=$row['id'];

				save_activity_log($user_id);

				$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success'=>'1');

				$data = array(
					'auth_id'  =>  $auth_id
				);  

				$updatePlayerID=Update('tbl_users', $data, "WHERE `id` = '".$row['id']."'");
			}

		}
		else{
			$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['email_not_found'],'success'=>'0');
		}

	}
	else{
		$set['HD_WALLPAPER'][]=array('success'=>'0', 'MSG' =>$app_lang['invalid_user_type']);
	}

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}

else if($get_method['method_name']=="user_register")
{

	$user_type=trim($get_method['type']);

	$email=addslashes(trim($get_method['email']));
	$auth_id=addslashes(trim($get_method['auth_id']));

	$to = $get_method['email'];
	$recipient_name=$get_method['name'];

	$subject = str_replace('###', APP_NAME, $app_lang['register_mail_lbl']);

	if($user_type=='Google' || $user_type=='google'){

		$sql="SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Google'";
		$res=mysqli_query($mysqli,$sql);
		$num_rows = mysqli_num_rows($res);
		$row = mysqli_fetch_assoc($res);

		if($num_rows == 0)
		{
			$data = array(
				'user_type'=>'Google',
				'name'  => addslashes(trim($get_method['name'])),				    
				'email'  =>  addslashes(trim($get_method['email'])),
				'password'  =>  md5(DEFAULT_PASSWORD),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')), 
				'status'  =>  '1'
			);		

			$qry = Insert('tbl_users',$data);

			$user_id=mysqli_insert_id($mysqli);

			save_activity_log($user_id);

			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['google_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			$set['HD_WALLPAPER'][]=array('user_id' => strval($user_id),'name'=>$get_method['name'],'email'=>$get_method['email'], 'success'=>'1', 'MSG' =>'', 'auth_id' => $auth_id);
		}
		else{
			$data = array(
				'auth_id'  =>  $auth_id,
			); 

			$update=Update('tbl_users', $data, "WHERE id = '".$row['id']."'");

			$user_id=$row['id'];

			save_activity_log($user_id);

			if($row['status']==0)
			{
				$set['HD_WALLPAPER'][]=array('MSG' =>$app_lang['account_deactive'],'success'=>'0');
			}	
			else
			{
				$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success'=>'1');
			}
		}

	}
	else if($user_type=='Apple' || $user_type=='apple'){

		$sql="SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Apple'";
		$res=mysqli_query($mysqli,$sql);
		$num_rows = mysqli_num_rows($res);
		$row = mysqli_fetch_assoc($res);

		if($num_rows == 0)
		{
			$data = array(
				'user_type'=>'Apple',
				'name'  => addslashes(trim($get_method['name'])),				    
				'email'  =>  addslashes(trim($get_method['email'])),
				'password'  =>  md5(DEFAULT_PASSWORD),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')), 
				'status'  =>  '1'
			);		

			$qry = Insert('tbl_users',$data);

			$user_id=mysqli_insert_id($mysqli);

			save_activity_log($user_id);

			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['apple_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			$set['HD_WALLPAPER'][]=array('user_id' => strval($user_id),'name'=>$get_method['name'],'email'=>$get_method['email'], 'success'=>'1', 'MSG' =>'', 'auth_id' => $auth_id);
		}
		else{
			$data = array(
				'auth_id'  =>  $auth_id,
			); 

			$update=Update('tbl_users', $data, "WHERE id = '".$row['id']."'");

			$user_id=$row['id'];

			save_activity_log($user_id);

			if($row['status']==0)
			{
				$set['HD_WALLPAPER'][]=array('MSG' =>$app_lang['account_deactive'],'success'=>'0');
			}	
			else
			{

				$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success'=>'1');
			}
		}

	}
	else if($user_type=='Facebook' || $user_type=='facebook'){

		$sql="SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Facebook'";
		$res=mysqli_query($mysqli,$sql);
		$num_rows = mysqli_num_rows($res);
		$row = mysqli_fetch_assoc($res);

		if($num_rows == 0)
		{
			$data = array(
				'user_type'=>'Facebook',
				'name'  => addslashes(trim($get_method['name'])),				    
				'email'  =>  addslashes(trim($get_method['email'])),
				'password'  =>  md5(DEFAULT_PASSWORD),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')), 
				'status'  =>  '1'
			);		

			$qry = Insert('tbl_users',$data);

			$user_id=mysqli_insert_id($mysqli);

			save_activity_log($user_id);

			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['facebook_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			$set['HD_WALLPAPER'][]=array('user_id' => strval($user_id),'name'=>$get_method['name'],'email'=>$get_method['email'], 'success'=>'1', 'MSG' =>'', 'auth_id' => $auth_id);
		}
		else{
			$data = array(
				'auth_id'  =>  $auth_id,
			); 

			$update=Update('tbl_users', $data, "WHERE id = '".$row['id']."'");

			$user_id=$row['id'];

			save_activity_log($user_id);

			if($row['status']==0)
			{
				$set['HD_WALLPAPER'][]=array('MSG' =>$app_lang['account_deactive'],'success'=>'0');
			}	
			else
			{
				$set['HD_WALLPAPER'][]=array('user_id' => $row['id'], 'name'=>$row['name'], 'email'=>$row['email'], 'MSG' => $app_lang['login_success'], 'auth_id' => $auth_id, 'success'=>'1');
			}
		}

	}
	else{

		$sql = "SELECT * FROM tbl_users WHERE email = '$email' AND `user_type`='Normal'";
		$result = mysqli_query($mysqli, $sql);
		$row = mysqli_fetch_assoc($result);

		if (!filter_var($get_method['email'], FILTER_VALIDATE_EMAIL)) 
		{
			$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['invalid_email_format'],'success'=>'0');
		}
		else if($row['email']!="")
		{
			$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['email_exist'],'success'=>'0');
		}
		else
		{	
			$data = array(
				'user_type'=>'Normal',											 
				'name'  => addslashes(trim($get_method['name'])),				    
				'email'  =>  addslashes(trim($get_method['email'])),
				'password'  =>  md5(trim($get_method['password'])),
				'phone'  =>  addslashes(trim($get_method['phone'])),
				'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
				'status'  =>  '1'
			);		

			$qry = Insert('tbl_users',$data);

			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['normal_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['register_success'],'success'=>'1');
		}

	}

	send_email($to,$recipient_name,$subject,$message);

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}

else if($get_method['method_name']=="user_profile")
{
	$jsonObj= array();	

	$user_id=$get_method['user_id'];

	$qry = "SELECT * FROM tbl_users WHERE id = '$user_id'"; 

	$result = mysqli_query($mysqli,$qry);

	$row = mysqli_fetch_assoc($result);	

	$data['success']="1";
	$data['user_id'] = $row['id'];
	$data['name'] = $row['name'];
	$data['email'] = $row['email'];
	$data['phone'] = $row['phone'];

	array_push($jsonObj,$data);

	$set['HD_WALLPAPER'] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();
}
else if($get_method['method_name']=="edit_profile")
{
	$jsonObj= array();	

	$qry = "SELECT * FROM tbl_users WHERE id = '".$get_method['user_id']."'"; 
	$result = mysqli_query($mysqli,$qry);
	$row = mysqli_fetch_assoc($result);

	if (!filter_var($get_method['email'], FILTER_VALIDATE_EMAIL)) 
	{
		$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['invalid_email_format'],'success'=>'0');

		header( 'Content-Type: application/json; charset=utf-8' );
		$json = json_encode($set);
		echo $json;
		exit;
	}
	else if($row['email']==$get_method['email'] AND $row['id']!=$get_method['user_id'])
	{
		$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['email_exist'],'success'=>'0');

		header( 'Content-Type: application/json; charset=utf-8' );
		$json = json_encode($set);
		echo $json;
		exit;
	}
	else if($get_method['password']!="")
	{
		$data = array(
			'name'  =>  $get_method['name'],
			'email'  =>  $get_method['email'],
			'password'  =>  md5($get_method['password']),
			'phone'  =>  $get_method['phone'] 
		);
	}
	else
	{
		$data = array(
			'name'  =>  $get_method['name'],
			'email'  =>  $get_method['email'],			 
			'phone'  =>  $get_method['phone'] 
		);
	}

	$user_edit=Update('tbl_users', $data, "WHERE id = '".$get_method['user_id']."'");

	$set['HD_WALLPAPER'][]=array('MSG'=>$app_lang['update_success'],'success'=>'1');

	header( 'Content-Type: application/json; charset=utf-8' );
	$json = json_encode($set);
	echo $json;
	exit;
}
else if($get_method['method_name']=="forgot_pass")
{	 

	$email=addslashes(trim($get_method['email']));

	$qry = "SELECT * FROM tbl_users WHERE email = '$email' AND `user_type`='Normal' AND `id` <> 0"; 
	$result = mysqli_query($mysqli,$qry);
	$row = mysqli_fetch_assoc($result);

	if($row['email']!="")
	{
		$password=generateRandomPassword(7);

		$new_password=md5($password);

		$to = $row['email'];
		$recipient_name=$row['name'];
		$subject = str_replace('###', APP_NAME, $app_lang['forgot_password_sub_lbl']);

		$message='<div style="background-color: #f9f9f9;" align="center"><br />
		<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
		<tbody>
		<tr>
		<td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" style="width:100px;height:auto"/></td>
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
		<p style="color: #262626; font-size: 24px; margin-top:0px;"><strong>'.$app_lang['dear_lbl'].' '.$row['name'].'</strong></p>
		<p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-top:5px;"><br>'.$app_lang['your_password_lbl'].': <span style="font-weight:400;">'.$password.'</span></p>
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

		send_email($to,$recipient_name,$subject,$message);

		$sql="UPDATE tbl_users SET `password`='$new_password' WHERE `id`='".$row['id']."'";
		mysqli_query($mysqli,$sql);

		$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['password_sent_mail'],'success'=>'1');
	}
	else
	{  	 
		$set['HD_WALLPAPER'][]=array('MSG' => $app_lang['email_not_found'],'success'=>'0');		
	}

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();

}
else if($get_method['method_name']=="user_report")
{
	$jsonObj= array();	
	$user_id=cleanInput($get_method['user_id']);
	$item_id=cleanInput($get_method['item_id']);
	$user_txt=cleanInput($get_method['user_txt']);
	$report_for=cleanInput($get_method['report_for']);

	$data = array(
		'report_for' => $report_for,				    
		'user_id'  => $user_id,				    
		'parent_id'  =>  $item_id,
		'user_message'  =>  $user_txt
	);		


	$qry = Insert('tbl_user_report',$data);									 

	$info['success']="1";	
	$info['MSG']=$app_lang['report_success'];

	array_push($jsonObj,$info);

	$set['HD_WALLPAPER'] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();

}
else if($get_method['method_name']=="favorite_post")
{
	$jsonObj= array();	
	$user_id=cleanInput($get_method['user_id']);
	$post_id=cleanInput($get_method['post_id']);
	$fav_type=cleanInput($get_method['fav_type']);

	$sql="SELECT * FROM tbl_favorite WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `type`='$fav_type'";
	$res=mysqli_query($mysqli, $sql);

	if(mysqli_num_rows($res) == 0){

		$data = array(
			'post_id' => $post_id,				    
			'user_id'  => $user_id,				    
			'type'  =>  $fav_type,
			'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
		);		


		$qry = Insert('tbl_favorite',$data);									 

		$info['success']="1";	
		$info['MSG']=$app_lang['favourite_success'];
	}
	else{
		$deleteSql="DELETE FROM tbl_favorite WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `type`='$fav_type'";
		
		if(mysqli_query($mysqli, $deleteSql)){
			$info['success']="1";	
			$info['MSG']=$app_lang['favourite_remove_success'];
		}
		else{
			$info['success']="0";	
			$info['MSG']=$app_lang['favourite_remove_error'];
		}
	}

	array_push($jsonObj,$info);

	$set['HD_WALLPAPER'] = $jsonObj;

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();
}
else if($get_method['method_name']=="get_favorite_post")
{
	$jsonObj= array();
	$data_arr= array();

	$user_id=cleanInput($get_method['user_id']);
	$fav_type=cleanInput($get_method['fav_type']);
	$type=cleanInput($get_method['type']);

	$page_limit=API_LATEST_LIMIT;

	$limit=($get_method['page']-1) * $page_limit;

	$colors_arr=explode(',', $get_method['color_id']);

	switch ($fav_type) {
		case 'wallpaper':
		{
			if($get_method['type']!='')
			{
				$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
				LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
				LEFT JOIN tbl_favorite ON tbl_wallpaper.`id` = tbl_favorite.`post_id`
				WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_favorite.`type`='wallpaper' AND tbl_favorite.`user_id`='$user_id' AND tbl_category.`status`='1'
				ORDER BY tbl_wallpaper.`id`";

				$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

				if($colors_arr[0]!=''){
					$column='';
					foreach ($colors_arr as $key => $value) {
						$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
					}

					$column=rtrim($column,'OR ');

					$sql="SELECT tbl_wallpaper.*, tbl_category.`cid`, tbl_category.`category_name`, tbl_category.`category_image` FROM tbl_wallpaper
					LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
					LEFT JOIN tbl_favorite ON tbl_wallpaper.`id` = tbl_favorite.`post_id`
					WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_favorite.`type`='wallpaper' AND tbl_category.`status`='1' AND tbl_favorite.`user_id`='$user_id' AND ($column)
					ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";

				}else{

					$sql="SELECT tbl_wallpaper.*, tbl_category.`cid`, tbl_category.`category_name`, tbl_category.`category_image` FROM tbl_wallpaper
					LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
					LEFT JOIN tbl_favorite ON tbl_wallpaper.`id` = tbl_favorite.`post_id` 
					WHERE tbl_wallpaper.`wallpaper_type` = '$type' AND tbl_favorite.`user_id`='$user_id' AND tbl_favorite.`type`='wallpaper' AND tbl_category.`status`='1'
					ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";
				}
			}
			else
			{
				$query_rec = "SELECT COUNT(*) as num FROM tbl_wallpaper
				LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid` 
				LEFT JOIN tbl_favorite ON tbl_wallpaper.`id` = tbl_favorite.`post_id`
				WHERE tbl_favorite.`type`='wallpaper' AND tbl_favorite.`user_id`='$user_id' AND tbl_category.`status`='1'
				ORDER BY tbl_wallpaper.`id`";

				$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

				if($colors_arr[0]!=''){
					$column='';
					foreach ($colors_arr as $key => $value) {
						$column.='FIND_IN_SET('.$value.', tbl_wallpaper.`wall_colors`) OR ';
					}

					$column=rtrim($column,'OR ');

					$sql="SELECT tbl_wallpaper.*, tbl_category.`cid`, tbl_category.`category_name`, tbl_category.`category_image` FROM tbl_wallpaper
					LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
					LEFT JOIN tbl_favorite ON tbl_wallpaper.`id` = tbl_favorite.`post_id`
					WHERE
					($column) AND tbl_category.`status`='1' AND tbl_favorite.`type`='wallpaper' AND tbl_favorite.`user_id`='$user_id'
					ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit,$page_limit";
				}else{
					$sql="SELECT tbl_wallpaper.*, tbl_category.`cid`, tbl_category.`category_name`, tbl_category.`category_image` FROM tbl_wallpaper
					LEFT JOIN tbl_category ON tbl_wallpaper.`cat_id`= tbl_category.`cid`
					LEFT JOIN tbl_favorite ON tbl_wallpaper.`id` = tbl_favorite.`post_id`
					WHERE 
					tbl_category.`status`='1' AND tbl_favorite.`type`='wallpaper' AND tbl_favorite.`user_id`='$user_id'
					ORDER BY tbl_wallpaper.`id` DESC LIMIT $limit, $page_limit";
				}
			}

			$result = mysqli_query($mysqli,$sql);

			while($data = mysqli_fetch_assoc($result))
			{	
				$data_arr['num'] = $total_pages['num'];
				$data_arr['id'] = $data['id'];
				$data_arr['cat_id'] = $data['cat_id'];
				$data_arr['wallpaper_type'] = $data['wallpaper_type'];
				$data_arr['wallpaper_image'] = $file_path.'categories/'.$data['cat_id'].'/'.$data['image'];
				$data_arr['wallpaper_image_thumb'] = get_thumb('categories/'.$data['cat_id'].'/'.$data['image'],'300x300');
				$data_arr['total_views'] = $data['total_views']; 
				$data_arr['total_rate'] = $data['total_rate'];
				$data_arr['rate_avg'] = $data['rate_avg'];

				$data_arr['is_favorite']=is_favorite($data['id'],'wallpaper',$user_id);

				$data_arr['wall_tags'] = $data['wall_tags'];
				$data_arr['wall_colors'] = $data['wall_colors'];

				$data_arr['cid'] = $data['cid'];
				$data_arr['category_name'] = $data['category_name'];
				$data_arr['category_image'] = $file_path.'images/'.$data['category_image'];
				$data_arr['category_image_thumb'] = get_thumb('images/'.$data['category_image'],'300x300');

				array_push($jsonObj,$data_arr);
			}
		}
		break;

		case 'gif':
		{
			$query_rec="SELECT COUNT(*) as num FROM tbl_wallpaper_gif 
			LEFT JOIN tbl_favorite ON tbl_wallpaper_gif.`id` = tbl_favorite.`post_id`
			WHERE tbl_favorite.`type`='gif' AND tbl_favorite.`user_id`='$user_id'
			ORDER BY tbl_wallpaper_gif.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$sql="SELECT tbl_wallpaper_gif.* FROM tbl_wallpaper_gif
			LEFT JOIN tbl_favorite ON tbl_wallpaper_gif.`id` = tbl_favorite.`post_id`
			WHERE tbl_favorite.`type`='gif' AND tbl_favorite.`user_id`='$user_id'
			ORDER BY tbl_wallpaper_gif.`id` DESC LIMIT $limit,$page_limit";

			$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($result))
			{	
				$data_arr['num'] = $total_pages['num'];
				$data_arr['id'] = $data['id'];			 
				$data_arr['gif_image'] = $file_path.'images/animation/'.$data['image'];
				$data_arr['gif_tags'] = $data['gif_tags'];
				$data_arr['total_views'] = $data['total_views']; 
				$data_arr['total_rate'] = $data['total_rate'];
				$data_arr['rate_avg'] = $data['rate_avg'];

				$data_arr['is_favorite']=is_favorite($data['id'],'gif',$user_id);

				array_push($jsonObj,$data_arr);
			}
		}
		break;
		
		default:
		{
		}
		break;
	}

	$set['HD_WALLPAPER'] = $jsonObj;
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();
}
else
{
	$get_method = checkSignSalt($_POST['data']);
}
?>