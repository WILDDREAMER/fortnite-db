<?php 

$page_title="Dashboard";

include("includes/header.php");
include("includes/function.php");

$qry_cat="SELECT COUNT(*) as num FROM tbl_category";
$total_category= mysqli_fetch_array(mysqli_query($mysqli,$qry_cat));
$total_category = thousandsNumberFormat($total_category['num']);

$qry_wallpaper="SELECT COUNT(*) as num FROM tbl_wallpaper";
$total_wallpaper = mysqli_fetch_array(mysqli_query($mysqli,$qry_wallpaper));
$total_wallpaper = thousandsNumberFormat($total_wallpaper['num']);

$qry_wallpaper_gif="SELECT COUNT(*) as num FROM tbl_wallpaper_gif";
$total_wallpaper_gif = mysqli_fetch_array(mysqli_query($mysqli,$qry_wallpaper_gif));
$total_wallpaper_gif = thousandsNumberFormat($total_wallpaper_gif['num']);

$qry_wall_dwn="SELECT SUM(total_download) as num FROM tbl_wallpaper";
$total_wall_download= mysqli_fetch_array(mysqli_query($mysqli,$qry_wall_dwn));
$total_wall_download = thousandsNumberFormat($total_wall_download['num']);

$qry_gif_dwn="SELECT SUM(total_download) as num FROM tbl_wallpaper_gif";
$total_gif_download= mysqli_fetch_array(mysqli_query($mysqli,$qry_gif_dwn));
$total_gif_download = thousandsNumberFormat($total_gif_download['num']);

$qry_users="SELECT COUNT(*) as num FROM tbl_users";
$total_users = mysqli_fetch_array(mysqli_query($mysqli,$qry_users));
$total_users = $total_users['num'];

$countStr='';

$no_data_status=false;
$count=$monthCount=0;

for ($mon=1; $mon<=12; $mon++) {

  $monthCount++;

  if(isset($_GET['filterByYear'])){
    $year=$_GET['filterByYear'];
  }
  else{
    $year=date('Y');
  }

  $month = date('M', mktime(0,0,0,$mon, 1, $year));

  $sql_user="SELECT `id` FROM tbl_users WHERE `registered_on` <> 0 AND DATE_FORMAT(FROM_UNIXTIME(`registered_on`), '%c') = '$mon' AND DATE_FORMAT(FROM_UNIXTIME(`registered_on`), '%Y') = '$year'";

  $totalcount=mysqli_num_rows(mysqli_query($mysqli, $sql_user));

  $countStr.="['".$month."', ".$totalcount."], ";

  if($totalcount==0){
    $count++;
  }
}

if($monthCount > $count){
  $no_data_status=false;
}
else{
  $no_data_status=true;
}

$countStr=rtrim($countStr, ", ");

?>

<?php 
$sql_smtp="SELECT * FROM tbl_smtp_settings WHERE id='1'";
$res_smtp=mysqli_query($mysqli,$sql_smtp);
$row_smtp=mysqli_fetch_assoc($res_smtp);

$smtp_warning=true;

if(!empty($row_smtp))
{

  if($row_smtp['smtp_type']=='server'){
    if($row_smtp['smtp_host']!='' AND $row_smtp['smtp_email']!=''){
      $smtp_warning=false;
    }
    else{
      $smtp_warning=true;
    }  
  }
  else if($row_smtp['smtp_type']=='gmail'){
    if($row_smtp['smtp_ghost']!='' AND $row_smtp['smtp_gemail']!=''){
      $smtp_warning=false;
    }
    else{
      $smtp_warning=true;
    }  
  }
}

if($smtp_warning)
{
  ?>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="alert alert-danger alert-dismissible fade in" role="alert">
        <h4 id="oh-snap!-you-got-an-error!"><i class="fa fa-exclamation-triangle"></i> SMTP Setting is not config<a class="anchorjs-link" href="#oh-snap!-you-got-an-error!"><span class="anchorjs-icon"></span></a></h4>
        <p style="margin-bottom: 10px">Config the smtp setting otherwise <strong>forgot password</strong> OR <strong>email</strong> feature will not be work.</p> 
      </div>
    </div>
  </div>
<?php } ?>

<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12"> 
    <a href="manage_category.php" class="card card-banner card-green-light">
      <div class="card-body"> <i class="icon fa fa-sitemap fa-4x"></i>
        <div class="content">
          <div class="title">Categories</div>
          <div class="value"><span class="sign"></span><?php echo $total_category;?></div>
        </div>
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
    <a href="manage_wallpaper.php" class="card card-banner card-yellow-light">
      <div class="card-body"> <i class="icon fa fa-image fa-4x"></i>
        <div class="content">
          <div class="title">Wallpaper</div>
          <div class="value"><span class="sign"></span><?php echo $total_wallpaper;?></div>
        </div>
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
    <a href="manage_wallpaper_animation.php" class="card card-banner card-blue-light">
      <div class="card-body"> <i class="icon fa fa-leaf fa-4x"></i>
        <div class="content">
          <div class="title">GIF</div>
          <div class="value"><span class="sign"></span><?php echo $total_wallpaper_gif;?></div>
        </div>
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mr_bot60"> 
    <a href="manage_wallpaper.php?is_download" class="card card-banner card-pink-light">
      <div class="card-body"> <i class="icon fa fa-download fa-4x"></i>
        <div class="content">
          <div class="title">Wallpaper Download</div>
          <div class="value"><span class="sign"></span><?php echo $total_wall_download;?></div>
        </div>
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mr_bot60"> 
    <a href="manage_wallpaper_animation.php?is_download" class="card card-banner card-orange-light">
      <div class="card-body"> <i class="icon fa fa-download fa-4x"></i>
        <div class="content">
          <div class="title">GIF Download</div>
          <div class="value"><span class="sign"></span><?php echo $total_gif_download;?></div>
        </div>
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mr_bot60"> 
    <a href="manage_users.php" class="card card-banner card-aliceblue-light">
      <div class="card-body"> <i class="icon fa fa-users fa-4x"></i>
        <div class="content">
          <div class="title">Users</div>
          <div class="value"><span class="sign"></span><?php echo $total_users;?></div>
        </div>
      </div>
    </a> 
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 2px;">
      <div class="col-lg-10">
        <h3>Users Analysis</h3>
        <p>New registrations</p>
      </div>
      <div class="col-lg-2" style="padding-top: 20px">
        <form method="get" id="graphFilter">
          <select class="form-control select2" name="filterByYear" style="box-shadow: none;height: auto;border-radius: 0px;font-size: 16px;">
            <?php 
            $currentYear=date('Y');
            $minYear=2020;

            for ($i=$currentYear; $i >= $minYear ; $i--) { 
              ?>
              <option value="<?=$i?>" <?=(isset($_GET['filterByYear']) && $_GET['filterByYear']==$i) ? 'selected' : ''?>><?=$i?></option>
              <?php
            }
            ?>
          </select>
        </form>
      </div>
      <div class="col-lg-12">
        <?php 
        if($no_data_status){
          ?>
          <h3 class="text-muted text-center" style="padding-bottom: 2em">No data found !</h3>
          <?php
        }
        else{
          ?>
          <div id="registerChart">
            <p style="text-align: center;"><i class="fa fa-spinner fa-spin" style="font-size:3em;color:#aaa;margin-bottom:50px" aria-hidden="true"></i></p>
          </div>
          <?php    
        }
        ?>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php");?> 

<?php 
if(!$no_data_status){
  ?>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'line']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Month');
      data.addColumn('number', 'Users');

      data.addRows([<?=$countStr?>]);

      var options = {
        curveType: 'function',
        fontSize: 15,
        hAxis: {
          title: "Months of <?=(isset($_GET['filterByYear'])) ? $_GET['filterByYear'] : date('Y')?>",
          titleTextStyle: {
            color: '#000',
            bold:'true',
            italic: false
          },
        },
        vAxis: {
          title: "Nos of Users",
          titleTextStyle: {
            color: '#000',
            bold:'true',
            italic: false,
          },
          gridlines: { count: 5},
          format: '#',
          viewWindowMode: "explicit", viewWindow:{ min: 0 },
        },
        height: 400,
        chartArea:{
          left:100,top:20,width:'100%',height:'auto'
        },
        legend: {
          position: 'none'
        },
        lineWidth:4,
        animation: {
          startup: true,
          duration: 1200,
          easing: 'out',
        },
        pointSize: 5,
        pointShape: "circle",

      };
      var chart = new google.visualization.LineChart(document.getElementById('registerChart'));

      chart.draw(data, options);
    }

    $(document).ready(function () {
      $(window).resize(function(){
        drawChart();
      });
    });
  </script>

<?php } ?>

<script type="text/javascript">

  // filter of graph
  $("select[name='filterByYear']").on("change",function(e){
    $("#graphFilter").submit();
  });

</script>      
