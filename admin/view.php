<?php
/**
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com), Hendro Wicaksono (hendrowicaksono@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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

/* File Viewer */

require '../sysconfig.inc.php';
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';

// get file ID
    $fileID = $_GET['fid'];
    $dir = $_GET['view'];
    $file_loc = REPOBS.'lampiran_'.$dir.'/'.$fileID;
    $file_loc_url = SWB.'admin/pdf_stream.php?file='.$fileID;

    if (file_exists($file_loc)) {
        $file_loc_url = SWB.'admin/pdf_stream.php?view='.$dir.'&file='.$fileID;
        require './../js/pdfjs/web/viewer.php';
        exit();
    } else {
        die('<div class="errorBox">File Metadata exists in database BUT '.$file_loc.' does\'t exists in repository!</div>');
    }
