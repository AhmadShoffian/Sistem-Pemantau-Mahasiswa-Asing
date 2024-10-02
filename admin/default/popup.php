<?php
/**
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */


// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
// start the session
require SB.'admin/default/session.inc.php';

$id= $_GET['id'];
$modul = $_GET['modul'];
if($modul == 'mou'){
	$url = '/mou/kegiatan.php';
}
elseif($modul == 'pks'){
	$url = '/per_kerjasama/kegiatan.php';
}

// ajax action
$content = '<script type="text/javascript">'."\n";
$content .= '$(document).ready( function() { $(\'#pageContent\').simbioAJAX(\'../modules'.$url.'?id='.$id.'&type=view&inPopUp=true\'); });';
$content .= '</script>';

// page title
$page_title = 'Kegiatan';

// include the page template
require SB.'/admin/'.$sysconf['admin_template']['dir'].'/notemplate_page_tpl.php';
