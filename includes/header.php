<?php

date_default_timezone_set("Asia/Kolkata");
include("includes/connection.php");
include("includes/session_check.php");

$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
$currentFile = $parts[count($parts) - 1];

$requestUrl = $_SERVER["REQUEST_URI"];
$urlparts = Explode('/', $requestUrl);
$redirectUrl = $urlparts[count($urlparts) - 1];

$mysqli->set_charset("utf8mb4");

?>
<!DOCTYPE html>
<html>

<head>
  <meta name="author" content="">
  <meta name="description" content="">
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo (isset($page_title)) ? $page_title.' | '.APP_NAME : APP_NAME; ?></title>
  <link rel="icon" href="images/<?php echo APP_LOGO;?>" sizes="16x16">
  <link rel="stylesheet" type="text/css" href="assets/css/vendor.css">
  <link rel="stylesheet" type="text/css" href="assets/css/flat-admin.css">

  <link rel="stylesheet" type="text/css" href="assets/sweetalert/sweetalert.css">

  <link rel="stylesheet" type="text/css" href="vendor/duDialog-master/duDialog.min.css">

  <link rel="stylesheet" href="vendor/snackbar-master/snackbar.css">

  <script src="assets/ckeditor/ckeditor.js"></script>

  <style type="text/css">
    .btn_edit,
    .btn_delete,
    .btn_cust {
      padding: 5px 10px !important;
    }

    .multi_action .dropdown-menu {
      padding-top: 0px;
      padding-bottom: 0px;
      box-shadow: 0px 6px 12px 1px rgba(4, 4, 4, 0.23);
    }

    .multi_action .dropdown-menu>li>a {
      padding: 8px 20px !important;
    }

    .multi_action .dropdown-menu>li>a {
      border-bottom: 1px solid #eee;
    }

    p.not_data {
      font-size: 16px;
      text-align: center;
      margin-top: 10px;
    }
    .top{
      position: relative !important;
      padding: 0px 0px 20px 0px !important;
    }
    .dataTables_wrapper{
      overflow: initial !important;
    }

    .notifyjs-corner{
      left: 50% !important;
      transform: translateX(-50%) !important;
    }

    .social_img{
      width: 20px !important;
      height: 20px !important;
      position: absolute;
      top: -11px;
      z-index: 1;
      left: 40px;
      margin:5px;
    }

    .select2-container .select2-selection--multiple{
      padding: 4px 4px !important;
      border-radius: 3px;
    }

    .select2-selection--multiple .select2-selection__choice{
      padding: 4px 5px !important;
    }

    @media (min-width:200px) and (max-width:991px) {
      .mytooltip:hover .tooltip-content {
        display: none
      }
    }
  </style>

</head>

<body>
  <div class="app app-default">
    <aside class="app-sidebar" id="sidebar">
      <div class="sidebar-header">
        <a class="sidebar-brand" href="home.php"><img src="images/<?php echo APP_LOGO; ?>" alt="app logo" /></a>
        <button type="button" class="sidebar-toggle"> <i class="fa fa-times"></i> </button>
      </div>
      <div class="sidebar-menu">
        <ul class="sidebar-nav">
          <li <?php if ($currentFile == "home.php") { ?>class="active" <?php } ?>> <a href="home.php">
            <div class="icon"> <i class="fa fa-dashboard" aria-hidden="true"></i> </div>
            <div class="title">Dashboard</div>
          </a>
        </li>
        <li <?php if ($currentFile == "manage_color.php" or $currentFile == "add_color.php") { ?>class="active" <?php } ?>>
          <a href="manage_color.php">
            <div class="icon"> <i class="fa fa-adjust" aria-hidden="true"></i> </div>
            <div class="title">Color</div>
          </a>
        </li>
        <li <?php if ($currentFile == "manage_category.php" or $currentFile == "add_category.php") { ?>class="active" <?php } ?>>
          <a href="manage_category.php">
            <div class="icon"> <i class="fa fa-sitemap" aria-hidden="true"></i> </div>
            <div class="title">Categories</div>
          </a>
        </li>

        <li <?php if ($currentFile == "manage_wallpaper.php" or $currentFile == "add_wallpaper.php" or $currentFile == "edit_wallpaper.php") { ?>class="active" <?php } ?>>
          <a href="manage_wallpaper.php">
            <div class="icon"> <i class="fa fa-image" aria-hidden="true"></i> </div>
            <div class="title">Wallpaper</div>
          </a>
        </li>

        <li <?php if ($currentFile == "manage_wallpaper_animation.php" or $currentFile == "add_wallpaper_animation.php" or $currentFile == "edit_wallpaper_animation.php") { ?>class="active" <?php } ?>>
          <a href="manage_wallpaper_animation.php">
            <div class="icon"> <i class="fa fa-leaf" aria-hidden="true"></i> </div>
            <div class="title">GIF</div>
          </a>
        </li>
        <li <?php if ($currentFile == "manage_users.php" or $currentFile == "add_user.php" or $currentFile == "user_profile.php") { ?>class="active" <?php } ?>>
          <a href="manage_users.php">
            <div class="icon"> <i class="fa fa-users" aria-hidden="true"></i> </div>
            <div class="title">Users</div>
          </a>
        </li>
        <li <?php if ($currentFile == "send_notification.php") { ?>class="active" <?php } ?>>
          <a href="send_notification.php">
            <div class="icon"> <i class="fa fa-bell" aria-hidden="true"></i> </div>
            <div class="title">Notification</div>
          </a>
        </li>
        <li <?php if ($currentFile == "manage_reports.php") { ?>class="active" <?php } ?>>
          <a href="manage_reports.php">
            <div class="icon"> <i class="fa fa-bug" aria-hidden="true"></i> </div>
            <div class="title">Reports</div>
          </a>
        </li>
        <li <?php if ($currentFile == "smtp_settings.php") { ?>class="active" <?php } ?>>
          <a href="smtp_settings.php">
            <div class="icon"> <i class="fa fa-envelope" aria-hidden="true"></i> </div>
            <div class="title">SMTP Settings</div>
          </a>
        </li>
        <li <?php if ($currentFile == "settings.php") { ?>class="active" <?php } ?>>
          <a href="settings.php">
            <div class="icon"> <i class="fa fa-cog" aria-hidden="true"></i> </div>
            <div class="title">Settings</div>
          </a>
        </li>
        <li <?php if($currentFile=="verification.php"){?>class="active"<?php }?>>
          <a href="verification.php">
            <div class="icon"> <i class="fa fa-check-square-o" aria-hidden="true"></i> </div>
            <div class="title">Verify Purchase</div>
          </a> 
        </li>
        <?php if (file_exists('api.php') OR file_exists('ios_api.php')) { ?>
          <li <?php if ($currentFile == "api_urls.php") { ?>class="active" <?php } ?>>
            <a href="api_urls.php">
              <div class="icon"> <i class="fa fa-exchange" aria-hidden="true"></i> </div>
              <div class="title">API URLS</div>
            </a>
          </li>
        <?php } ?>
      </ul>
    </div>

  </aside>
  <div class="app-container">
    <nav class="navbar navbar-default" id="navbar">
      <div class="container-fluid">
        <div class="navbar-collapse collapse in">
          <ul class="nav navbar-nav navbar-mobile">
            <li>
              <button type="button" class="sidebar-toggle"> <i class="fa fa-bars"></i> </button>
            </li>
            <li class="logo"> <a class="navbar-brand" href="#"><?php echo APP_NAME; ?></a> </li>
            <li>
              <button type="button" class="navbar-toggle">
                <?php if (PROFILE_IMG) { ?>
                  <img class="profile-img" src="images/<?php echo PROFILE_IMG; ?>">
                <?php } else { ?>
                  <img class="profile-img" src="assets/images/profile.png">
                <?php } ?>

              </button>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-left">
            <li class="navbar-title"><?php echo APP_NAME; ?></li>

          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown profile"> <a href="profile.php" class="dropdown-toggle" data-toggle="dropdown"> <?php if (PROFILE_IMG) { ?>
              <img class="profile-img" src="images/<?php echo PROFILE_IMG; ?>">
            <?php } else { ?>
              <img class="profile-img" src="assets/images/profile.png">
            <?php } ?>
            <div class="title">Profile</div>
          </a>
          <div class="dropdown-menu">
            <div class="profile-info">
              <h4 class="username"><?=ucfirst($profile_details['username'])?></h4>
            </div>
            <ul class="action">
              <li><a href="profile.php">Profile</a></li>
              <li><a href="logout.php">Logout</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>