<!doctype html>
<html>
<head>
  <title><?php echo $page_title; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" />
  <meta http-equiv="Expires" content="Sat, 26 Jul 1997 05:00:00 GMT" />
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo SWB; ?>admin/admin_template/adminlte/style.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo SWB.'template/core.style.css'; ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo SWB.'admin/'.$sysconf['admin_template']['css']; ?>" />
  <link rel="stylesheet" href="<?php echo SWB; ?>admin/admin_template/adminlte/assets/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/datepicker.min.css">
  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/font-awesome.min.css">
  <?php if (isset($css)) { echo $css; } ?>
  <script type="text/javascript" src="<?php echo JWB; ?>jquery.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>updater.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>form.js"></script>
  <script type="text/javascript" src="<?php echo JWB; ?>gui.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8/dist/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@8/dist/sweetalert2.min.css" id="theme-styles">
  <?php if (isset($js)) { echo $js; } ?>
</head>
<body style ="background: #FFFFFF;">
<div id="pageContent">
<?php echo $content; ?>
</div>

<!-- block if we inside iframe -->
<script type="text/javascript">
// if we are inside iframe
jQuery(document).ready( function() {

<?php if (isset($_GET['block'])) { ?>

var parentWin = self.parent;
if (parentWin && parentWin.jQuery('.editFormLink').length > 0) {
  var enabler = parentWin.jQuery('.editFormLink');
  jQuery(document.body).append('<div id="blocker" style="position: fixed; width: 100%; height: 100%; top: 0; left: 0; background: #ccc; opacity: 0.3">&nbsp;</div>');
  enabler.click(function(evt) { evt.preventDefault(); self.parent.jQuery('form').enableForm();
	self.jQuery('#blocker').remove();
	self.parent.jQuery('.makeHidden').removeClass('makeHidden'); });
}

<?php } ?>

});
    $( "#dataList" ).addClass('table');
    $( ".menuBox").addClass("box box-solid");
    $( ".per_title").addClass("box-header with-border");
    $( ".sub_section").addClass("box-body");
    $( ".btn").addClass("btn-flat");
    $( ".button").addClass("btn-flat");
    $( "#doSearch").addClass("btn btn-default btn-flat");
    $( ".editLink").addClass('fa fa-search');
    $( ".editFormLink").addClass('pull-right');
    $( "input[type=text]").addClass("form-control");
       // $( "select").addClass("form-control");
    $( ".btn-group").addClass("pull-right");

</script>
<script src="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/js/datepicker.min.js"></script>
</body>
</html>
