<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<title><?php echo $page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--<link rel="stylesheet" type="text/css" href="<?php echo SWB.'template/printed.style.css'; ?>" /> -->
	<link rel="stylesheet" type="text/css" href="<?php echo SWB.'admin/'.$sysconf['admin_template']['css']; ?>" />
	  <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/AdminLTE.min.css">
	      <link rel="stylesheet" href="<?php echo AWB; ?>admin_template/<?php echo $sysconf['admin_template']['theme']?>/assets/css/font-awesome.min.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

	<?php if (isset($css)) { echo $css; } ?>
	<style type="text/css">
		body { 
			background: #FFFFFF; 
			font-family: 'Poppins', sans-serif !important;
		}
		.printPageInfo{
			padding: 10px;
			background: #cbd9d999;
			color: #2e642c;
		}
		.btn{
  border-radius: 10px;
}
	</style>
	<?php if (isset($js)) { echo $js; } ?>
</head>
<body>
	<div id="pageContent">
		<?php echo $content; ?>
	</div>
</body>
</html>
