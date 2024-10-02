<?php

/* Staffs/Application Users Management section */

// kunci untuk mengautentikasi
define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';
// konfigurasi sistem utama
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');

// memulai sesi
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';
require SIMBIO.'simbio_FILE/simbio_file_upload.inc.php';

// pemeriksaan hak istimewa
$can_read = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');

function getUserType($obj_db, $array_data, $col) {
  global $sysconf;
  if (isset($sysconf['system_user_type'][$array_data[$col]])) {
    return $sysconf['system_user_type'][$array_data[$col]];
  }
}

// periksa apakah kami ingin mengubah profil pengguna saat ini
$changecurrent = false;
if (isset($_GET['changecurrent'])) {
    $changecurrent = true;
}

if (!$changecurrent) {
    // hanya administrator yang memiliki hak istimewa untuk menambahkan/mengedit pengguna
    if ($_SESSION['uid'] != 1) {
        die('<div class="errorBox">'.('You don\'t have enough privileges to view this section').'</div>');
    }
}

/* REMOVE IMAGE */
if (isset($_POST['removeImage']) && isset($_POST['uimg']) && isset($_POST['img'])) {
  $_delete = $dbs->query(sprintf('UPDATE user SET user_image=NULL WHERE user_id=%d', $_POST['uimg']));
  if ($_delete) {
    @unlink(sprintf(IMGBS.'persons/%s',$_POST['img']));
    exit('<script type="text/javascript">alert(\''.str_replace('{imageFilename}', $_POST['img'], ('{imageFilename} successfully removed!')).'\'); $(\'#userImage, #imageFilename\').remove();</script>');
  }
  exit();
}
/* RECORD OPERATION */
if (isset($_POST['saveData'])) {
    $userName = trim(strip_tags($_POST['userName']));
    $realName = trim(strip_tags($_POST['realName']));
    $passwd1 = trim($_POST['passwd1']);
    $passwd2 = trim($_POST['passwd2']);
    // check form validity
    if (empty($userName) OR empty($realName)) {
        utility::jsAlert(('User Name or Real Name can\'t be empty'));
        exit();
    } else if (($userName == 'admin' OR $realName == 'Administrator') AND $_SESSION['uid'] != 1) {
        utility::jsAlert(('Login username or Real Name is probihited!'));
        exit();
    } else if (($passwd1 AND $passwd2) AND ($passwd1 !== $passwd2)) {
        utility::jsAlert(('Password confirmation does not match. See if your Caps Lock key is on!'));
        exit();
    } else if ($_POST['csrf_token'] != $_SESSION['csrf_token']['mainForm']) {
        utility::jsAlert(('Invalid token!'));
        exit();
    }else {
        $data['username'] = $dbs->escape_string(trim($userName));
        $data['realname'] = $dbs->escape_string(trim($realName));
        $data['user_type'] = (integer)$_POST['userType'];
        $data['email'] = $dbs->escape_string(trim($_POST['eMail']));
        $social_media = array();
        foreach ($_POST['social'] as $id => $social) {
          $social_val = $dbs->escape_string(trim($social));
          if ($social_val != '') {
            $social_media[$id] = $social_val;
          }
        }
        if ($social_media) {
          $data['social_media'] = $dbs->escape_string(serialize($social_media));
        }
        if (isset($_POST['noChangeGroup'])) {
            // mem-parsing data grup
            $groups = '';
            if (isset($_POST['groups']) AND !empty($_POST['groups'])) {
                $groups = serialize($_POST['groups']);
            } else {
                $groups = 'literal{NULL}';
            }
            $data['groups'] = trim($groups);
        }
        if (($passwd1 AND $passwd2) AND ($passwd1 === $passwd2)) {
            $data['passwd'] = password_hash($passwd2, PASSWORD_BCRYPT);
            // $data['passwd'] = 'literal{MD5(\''.$passwd2.'\')}';
        }
        $data['input_date'] = date('Y-m-d');
        $data['last_update'] = date('Y-m-d');

        if (!empty($_FILES['image']) AND $_FILES['image']['size']) {
          // membuat objek unggahan
          $upload = new simbio_file_upload();
          $upload->setAllowableFormat($sysconf['allowed_images']);
          $upload->setMaxSize($sysconf['max_image_upload']*1024); // approx. 100 kb
          $upload->setUploadDir(IMGBS.'persons');
          // beri nama baru untuk file unggahan
          $new_filename = 'user_'.str_replace(array(',', '.', ' ', '-'), '_', strtolower($data['username']));
          $upload_status = $upload->doUpload('image', $new_filename);
          if ($upload_status == UPLOAD_SUCCESS) {
            $data['user_image'] = $dbs->escape_string($upload->new_filename);
          }
        } else if (!empty($_POST['base64picstring'])) {
			    list($filedata, $filedom) = explode('#image/type#', $_POST['base64picstring']);
          $filedata = base64_decode($filedata);
          $fileinfo = getimagesizefromstring($filedata);
          $valid = strlen($filedata)/1024 < $sysconf['max_image_upload'];
          $valid = (!$fileinfo || $valid === false) ? false : in_array($fileinfo['mime'], array($sysconf['allowed_images_mimetype']));
			    $new_filename = 'user_'.str_replace(array(',', '.', ' ', '-'), '_', strtolower($data['username'])).'.'.strtolower($filedom);

			    if ($valid AND file_put_contents(IMGBS.'persons/'.$new_filename, $filedata)) {
				    $data['user_image'] = $dbs->escape_string($new_filename);
				    if (!defined('UPLOAD_SUCCESS')) define('UPLOAD_SUCCESS', 1);
				    $upload_status = UPLOAD_SUCCESS;
			    }
		    }

        // buat objek sql op
        $sql_op = new simbio_dbop($dbs);
        if (isset($_POST['updateRecordID'])) {
            /* UPDATE RECORD MODE */
            // hapus tanggal masukan
            unset($data['input_date']);
            // memfilter ID catatan pembaruan
            $updateRecordID = (integer)$_POST['updateRecordID'];
            // memperbarui data
            $update = $sql_op->update('user', $data, 'user_id='.$updateRecordID);
            if ($update) {
                // write log
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'].' update user data ('.$data['realname'].') with username ('.$data['username'].')');
                utility::jsAlert(('User Data Successfully Updated'));
                // upload status alert
                if (isset($upload_status)) {
                    if ($upload_status == UPLOAD_SUCCESS) {
                        // write log
                        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system/user', $_SESSION['realname'].' upload image file '.$upload->new_filename);
                        utility::jsAlert(('Image Uploaded Successfully'));
                    } else {
                        // write log
                        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system/user', 'ERROR : '.$_SESSION['realname'].' FAILED TO upload image file '.$upload->new_filename.', with error ('.$upload->error.')');
                        utility::jsAlert(('Image FAILED to upload'));
                    }
                }
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
            } else { utility::jsAlert(('User Data FAILED to Updated. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
            exit();
        } else {
            /* INSERT RECORD MODE */
            // insert the data
            if ($sql_op->insert('user', $data)) {
                // write log
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'].' add new user ('.$data['realname'].') with username ('.$data['username'].')');
                utility::jsAlert(('New User Data Successfully Saved'));
                // upload status alert
                if (isset($upload_status)) {
                    if ($upload_status == UPLOAD_SUCCESS) {
                        // write log
                        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system/user', $_SESSION['realname'].' upload image file '.$upload->new_filename);
                        utility::jsAlert(('Image Uploaded Successfully'));
                    } else {
                        // write log
                        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system/user', 'ERROR : '.$_SESSION['realname'].' FAILED TO upload image file '.$upload->new_filename.', with error ('.$upload->error.')');
                        utility::jsAlert(('Image FAILED to upload'));
                    }
                }
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\');</script>';
            } else { utility::jsAlert(('User Data FAILED to Save. Please Contact System Administrator')."\n".$sql_op->error); }
            exit();
        }
    }
    exit();
} else if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
    if (!($can_read AND $can_write)) {
        die();
    }
    /* DATA DELETION PROCESS */
    $sql_op = new simbio_dbop($dbs);
    $failed_array = array();
    $error_num = 0;
    if (!is_array($_POST['itemID'])) {
        // make an array
        $_POST['itemID'] = array((integer)$_POST['itemID']);
    }
    // loop array
    foreach ($_POST['itemID'] as $itemID) {
        $itemID = (integer)$itemID;
        // get user data
        $user_q = $dbs->query('SELECT username, realname FROM user WHERE user_id='.$itemID);
        $user_d = $user_q->fetch_row();
        if (!$sql_op->delete('user', "user_id='$itemID'")) {
            $error_num++;
        } else {
            // write log
            utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'].' DELETE user ('.$user_d[1].') with username ('.$user_d[0].')');
        }
    }

    // error alerting
    if ($error_num == 0) {
        utility::jsAlert(('All Data Successfully Deleted'));
        echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    } else {
        utility::jsAlert(('Some or All Data NOT deleted successfully!\nPlease contact system administrator'));
        echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    }
    exit();
}
/* RECORD OPERATION END */

if (!$changecurrent) {
/* search form */
?>



<fieldset class="menuBox">
<div class="menuBoxInner userIcon">
	<div class="per_title">
	    <h2><?php echo ('System Users'); ?></h2>
  </div>
	<div class="sub_section">
	  <div class="btn-group">
      <a href="<?php echo MWB; ?>/system/app_user.php" class="btn btn-default"><i class="glyphicon glyphicon-user"></i>&nbsp;<?php echo ('User List'); ?></a>
      <a href="<?php echo MWB; ?>system/app_user.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo ('Add New User'); ?></a>
	  </div>
    <form name="search" action="<?php echo MWB; ?>system/app_user.php" id="search" method="get" style="display: inline;"><?php echo ('Search'); ?> :
    <input type="text" name="keywords" size="30" />
    <input type="submit" id="doSearch" value="<?php echo ('Search'); ?>" class="btn btn-default" />
    </form>
  </div>
</div>
</fieldset>
<?php
/* search form end */
}

/* main content */
if (isset($_POST['detail']) OR (isset($_GET['action']) AND $_GET['action'] == 'detail')) {
    if (!($can_read AND $can_write) AND !$changecurrent) {
        die('<div class="errorBox">'.('You don\'t have enough privileges to view this section').'</div>');
    }
    /* RECORD FORM */
    // try query
    $itemID = (integer)isset($_POST['itemID'])?$_POST['itemID']:0;
    if ($changecurrent) {
        $itemID = (integer)$_SESSION['uid'];
    }
    $rec_q = $dbs->query('SELECT * FROM user WHERE user_id='.$itemID);
    $rec_d = $rec_q->fetch_assoc();

    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 'post');
    $form->submit_button_attr = 'name="saveData" value="'.('Save').'" class="btn btn-default"';

    // form table attributes
    $form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
    $form->table_content_attr = 'class="alterCell2"';

    // set tanda mode edit
    if ($rec_q->num_rows > 0) {
        $form->edit_mode = true;
        // record ID for delete process
        if (!$changecurrent) {
            // form record id
            $form->record_id = $itemID;
        } else {
            $form->addHidden('updateRecordID', $itemID);
            $form->back_button = false;
        }
        // form record title
        $form->record_title = $rec_d['realname'];
        // submit button attribute
        $form->submit_button_attr = 'name="saveData" value="'.('Update').'" class="btn btn-default"';
    }

    /* Form Element(s) */
    // user name
    $form->addTextField('text', 'userName', ('Login Username').'*', $rec_d['username'], 'style="width: 50%;"');
    // user real name
    $form->addTextField('text', 'realName', ('Real Name').'*', $rec_d['realname'], 'style="width: 50%;"');
    // user e-mail
    $form->addTextField('text', 'eMail', ('E-Mail'), $rec_d['email'], 'style="width: 50%;"');

    // user group
    // hanya ditampilkan oleh pengguna yang memegang hak istimewa modul sistem
    if (!$changecurrent AND $can_read AND $can_write) {
        // add hidden element as a flag that we dont change group data
        $form->addHidden('noChangeGroup', '1');
        // user group
        $group_query = $dbs->query('SELECT group_id, group_name FROM
            user_group WHERE group_id != 1');
        // initiliaze group options
        $group_options = array();
        while ($group_data = $group_query->fetch_row()) {
            $group_options[] = array($group_data[0], $group_data[1]);
        }
        $form->addCheckBox('groups', ('Group(s)'), $group_options, unserialize($rec_d['groups']));
    }
    // user password
    $form->addTextField('password', 'passwd1', ('New Password').'*', '', ' class="form-control" style="width: 50%;"');
    // user password confirm
    $form->addTextField('password', 'passwd2', ('Confirm New Password').'*', '', '  class="form-control" style="width: 50%;"');

    // edit mode messagge
    if ($form->edit_mode) {
        echo '<fieldset class="menuBox">';
        echo '<div class="infoBox alert bg-gray"><div class="box-body">'.('You are going to edit user profile'),' : <b>'.$rec_d['realname'].'</b> <br />'.('Last Update').'&nbsp;'.$rec_d['last_update'].'
          <div>'.('Leave Password field blank if you don\'t want to change the password').'</div></div>';
        if ($rec_d['user_image']) {
          if (file_exists(IMGBS.'persons/'.$rec_d['user_image'])) {
            echo '<div id="userImage" style="float: right;"><img src="'.SWB.'lib/minigalnano/createthumb.php?filename=../../images/persons/'.urlencode($rec_d['user_image']).'&amp;width=180&amp;timestamp='.date('his').'" style="border: 1px solid #999999" /></div>';
          }
        }
        echo '</div></fieldset>';
    }
    // print out the form object
    echo $form->printOut();
} else {
    // hanya administrator yang memiliki hak istimewa untuk melihat daftar pengguna
    if (!($can_read AND $can_write) OR $_SESSION['uid'] != 1) {
        die('<div class="errorBox">'.('You don\'t have enough privileges to view this section').'</div>');
    }

    /* USER LIST */
    // table spec
    $table_spec = 'user AS u';

    // create datagrid
    $datagrid = new simbio_datagrid();
    if ($can_read AND $can_write) {
        $datagrid->setSQLColumn('u.user_id',
            'u.realname AS \''.('Real Name').'\'',
            'u.username AS \''.('Login Username').'\'',
            'u.user_type AS \''.('User Type').'\'',
            'u.last_login AS \''.('Last Login').'\'',
            'u.last_update AS \''.('Last Update').'\'');
        $col = 3;
    } else {
        $datagrid->setSQLColumn('u.realname AS \''.('Real Name').'\'',
            'u.username AS \''.('Real Name').'\'',
            'u.user_type AS \''.('User Type').'\'',
            'u.last_login AS \''.('Last Login').'\'',
            'u.last_update AS \''.('Last Update').'\'');
        $col = 2;
    }
    $datagrid->modifyColumnContent($col, 'callback{getUserType}');
    $datagrid->setSQLorder('username ASC');

    // is there any search
    $criteria = 'u.user_id != 1 ';
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
       $keywords = $dbs->escape_string($_GET['keywords']);
       $criteria .= " AND (u.username LIKE '%$keywords%' OR u.realname LIKE '%$keywords%')";
    }
    $datagrid->setSQLCriteria($criteria);

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // set delete proccess URL
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];

    // atur tabel dan atribut tabel
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, ($can_read AND $can_write));
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, ('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox alert alert-success">'.$msg.' : "'.$_GET['keywords'].'"</div>';
    }

    echo $datagrid_result;
}
/* main content end */
?>
