<?php

// Need to modified script to adaptive new theme
include 'function.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Sistem Monitoring Mahasiswa Asing</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="<?php echo SWB; ?>template/core.style.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo JWB; ?>colorbox/colorbox.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo JWB; ?>chosen/chosen.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo JWB; ?>jquery.imgareaselect/css/imgareaselect-default.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo $sysconf['admin_template']['css']; ?>" rel="stylesheet" type="text/css" />

  <script type="text/javascript" src="<?php echo JWB; ?>jquery.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>updater.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>gui.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>form.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>calendar.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>keyboard.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>chosen/chosen.jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>chosen/ajax-chosen.min.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>tooltipsy.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>colorbox/jquery.colorbox-min.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>jquery.imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>webcam.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>scanner.js"></script>
  <script src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/datepicker.min.js"></script>
  <script type="text/javascript" src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/Chart.min.js"></script>
  <script type="text/javascript" src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/utils.js"></script>
  <script type="text/javascript" src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/hack.js"></script>
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/all-skins.min.css">
    <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<style type="text/css">
  h1,h2,h3,h4,h5,h6, .logo {
    font-family: 'Poppins', sans-serif !important;
    font-weight: bolder !important;
  }
  .content-wrapper{
    background-color: #eaeff0 !important;
  }
  .editLink_tabel{
    background-color: #fff !important;
  }
  .btn{
    margin: 12px 0px 12px 8px;
  }
  table, .datagrid-action-bar{
    margin-bottom: 0px !important;
    background-color: #fff;
  }
  .with-border{
    border-bottom: 0px !important;
  }
  .box-header{
    margin-left: 15px;
  }
    .headerField{
      font-weight: 700;
    font-size: 15pt;
    border-bottom: solid 4px #808080b8;
    border-top: solid 4px #808080b8;
    width: max-content;
    padding-bottom: 6px;
    padding-top: 6px;
    color: #46496cbd;
  }
  .form-control, .input-group-addon, textarea, .chzn-single  {
    border-top: none !important;
    border-right: none !important;
    border-left: none !important;
    background-image: none !important;
    font-weight: 500 !important;
  }
  #datalist{
    font-weight: 100 !important;
  }
  .editFormLink{
    margin-right: 10px;
  }
      .paging-area{
      padding: 15px;
      background: #fff;
    }
    .pagingList > a, .pagingList > b {
    border: solid 1px;
    padding: 4px 10px 4px 10px;
    border-radius: 4px;
}

.btn.btn-flat{
  border-radius: 10px;
}
.box-body{
  margin-left: 15px;
}
</style>

</head>
<body class="hold-transition skin-white sidebar-mini" style="font-family: 'Poppins', sans-serif;">
<div class="wrapper">

  <header class="main-header">
    <a href="" class="logo">
      <span class="logo-mini"><b>Si-PANTAU</b> WNA</span>
      <span class="logo-lg"><b>Si-PANTAU</b> WNA</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><?php echo $_SESSION['realname']?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../../../../orgasing/template/template/img/avatar.jpg" class="img-circle" alt="User Image">

                <p>
                  <?php echo $_SESSION['realname']?>
                  
                </p>
              </li>

              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left" id="sidepan">
                  <a href="<?php echo MWB;?>system/app_user.php?changecurrent=true&action=detail" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>

    </nav>
  </header>

  <aside class="main-sidebar">
    <section class="sidebar">
          <?php main_menu(); ?>
    </section>
    <!-- /.sidebar -->
  </aside>

  <div class="content-wrapper">
    <section class="content">
      <div class="row">
      <div class="col-md-12">
        
          <?php
            if(isset($_GET['mod']) && ($_GET['mod'] == 'system')) {
              include "modules/system/index.php";
              echo "<script>$('#mainForm').attr('action','".AWB."modules/system/index.php');</script>";
            } else {
              echo '<div id="mainContent" style="padding:20px;">';
              echo $main_content;
              echo '</div>';
            }
          ?>
        </div>
      </div>
    </section>
  </div>


  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      sistem pemantauan warga negara asing
    </div>
    <strong>Copyright © 2023 Institut Seni Indonesia -- Yogyakarta.</strong> All rights
    reserved.
  </footer>

</div>

<script src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/bootstrap.min.js"></script>
<script src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/fastclick.js"></script>
<script src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/adminlte.min.js"></script>

<!--<script src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/demo.js"></script> -->


<!-- fake submit iframe for search form, DONT REMOVE THIS! -->
  <iframe name="blindSubmit" style="visibility: hidden; width: 0; height: 0;"></iframe>
  <!-- fake submit iframe -->
  <script>
    var toggleMainMenu = function() {
      $('.per_title').bind('click',function(){
        $('.s-content').toggleClass('active');
        $('.s-sidebar').toggleClass('active');
        $('.s-user-frame').toggleClass('active');
        $('.s-menu').toggleClass('active');
      });
    }

    //trigger untuk menyembunyikan sidebar saat ini
    $('.s-current-child').click(function(){
      $('.s-current').trigger('click');
    });

    //buat jangkar bantuan dengan meu saat ini
    $('.s-current-child').click(function(){
      $('.left, .right, .loader').removeClass('active');
      $('.s-help > i').removeClass('fa-times').addClass('fa-question-circle');
      $('.s-help-content').html();
      $('.s-help').removeClass('active');
      var get_url       = $(this).attr('href');
      var path_array    = get_url.split('/');
      var clean_path    = path_array[path_array.length-1].split('.');
      var new_pathname  = '<?php echo AWB?>help.php?url='+path_array[path_array.length-2]+'/'+clean_path[0]+'.md';
      $('.s-help').attr('href', new_pathname);
    });

    //generate help file
    $('.s-help').click(function(e){
      e.preventDefault();
      if($(this).attr('href') != '#') {
        // load active style
        $('.left, .right, .loader').toggleClass('active');
        $(this).toggleClass('active');
        $.ajax({
          type: 'GET',
          url: $(this).attr('href')
        }).done(function( data ) {
          $('.s-help-content').html(data);
          $('.s-help > i').toggleClass('fa-question-circle fa-times');
        });
      }else{
        alert('Help content will show according to available menu.')
      }
    });

    $('.s-user-photo').bind('click', function(e) {
      e.preventDefault();
      $('a.submenu-user-profile').trigger('click');
    });

    // toggle main menu event register
    toggleMainMenu();
    $('body').on('simbioAJAXloaded', function(evt) {
      toggleMainMenu();
    })

    $('#mainMenu a.opac').bind('click', function(evt) {
      evt.preventDefault();
      top.jQuery.colorbox({iframe:true,
        href: $(this).attr('href'),
          width: function() { return parseInt($(window).width())-50; },
          height: function() { return parseInt($(window).height())-50; },
          title: function() { return 'Online Public Access Catalog'; } }
        );
    });

    // menyembunyikan menu jika klik konten utama
    $('.s-content').click(function(){
      $('#mainMenu input[type=radio]').each(function(){
        $(this).removeAttr('checked');
      });
    })


  </script>
</body>
</html>
