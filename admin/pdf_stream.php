<?php

define('INDEX_AUTH', '1');

require '../sysconfig.inc.php';
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';

$file_loc = REPOBS.'lampiran_'.$_GET['view'].'/'.$_GET['file'];
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="'.basename($file_loc).'"');
header('Content-Type: application/pdf');
readfile($file_loc);
exit();
