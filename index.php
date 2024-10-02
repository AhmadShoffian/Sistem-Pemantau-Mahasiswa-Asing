<?php
/**
 * SENAYAN application bootstrap files
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 * Some modifications & patches by Hendro Wicaksono (hendrowicaksono@yahoo.com)
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

// required file
require 'sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('opac');

// start session
session_start();
if ($sysconf['template']['base'] == 'html') {
  require SIMBIO.'simbio_GUI/template_parser/simbio_template_parser.inc.php';
}

//header("location:index.php?p=login");
// page title
$page_title = $sysconf['library_subname'].' | '.$sysconf['library_name'];

// default library info
$info = ('Web Online Public Access Catalog - Use the search options to find documents quickly');
// total opac result page
$total_pages = 1;
// default header info
$header_info = '';
// HTML metadata
$metadata = '';
// searched words for javascript highlight
$searched_words_js_array = '';


// start the output buffering for main content
ob_start();
include LIB.'contents/login.inc.php';
// main content grab
$main_content = ob_get_clean();

// template output
require $sysconf['template']['dir'].'/template/index_template.inc.php';