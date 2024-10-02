<?php

/* lokasi Type Management section */

// knci untuk mengautentikasi
define('INDEX_AUTH', '1');
// kunci untuk mendapatkan akses database penuh
define('DB_ACCESS', 'fa');

// konfigurasi sistem utama
require '../../../sysconfig.inc.php';
// Pembatasan akses berbasis IP
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-masterfile');
// memulai session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';

// pemeriksaan hak istimewa
$can_read = utility::havePrivilege('master_file', 'r');
$can_write = utility::havePrivilege('master_file', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.('You don\'t have enough privileges to view this section').'</div>');
}

/* RECORD OPERATION */
if (isset($_POST['saveData'])) {
    $citizenName = trim(strip_tags($_POST['nama_negara']));
    // check form validity
    if (empty($citizenName)) {
        utility::jsAlert(('Kewarganegaraan tidak boleh kosong'));
        exit();
    } else {
        $data['nama_negara'] = $dbs->escape_string($citizenName);
        $data['input_date'] = date('Y-m-d');
        $data['last_update'] = date('Y-m-d');

        // create sql op object
        $sql_op = new simbio_dbop($dbs);
        if (isset($_POST['updateRecordID'])) {
            /* UPDATE RECORD MODE */
            // remove input date
            unset($data['input_date']);
            // filter update record ID
            $updateRecordID = (integer)$_POST['updateRecordID'];
            // update the data
            $update = $sql_op->update('mst_negara', $data, 'id_negara='.$updateRecordID);
            if ($update) {
                utility::jsAlert(('Kewarganegaraan berhasil diperbaharui'));
                echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(parent.jQuery.ajaxHistory[0].url);</script>';
            } else { utility::jsAlert(('lokasi Data FAILED to Updated. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
            exit();
        } else {
            /* INSERT RECORD MODE */
            // insert the data
            $insert = $sql_op->insert('mst_negara', $data);
            if ($insert) {
                utility::jsAlert(('Kewarganegaraan berhasil disimpan'));
                echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\');</script>';
            } else { utility::jsAlert(('lokasi Data FAILED to Save. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
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
        $item_q = $dbs->query('SELECT mc.nama_negara, COUNT(mc.id_negara) FROM orang_asing AS f
            LEFT JOIN mst_negara mc ON f.id_negara=mc.id_negara
            WHERE mc.id_negara='.$itemID.' GROUP BY f.id_negara');
        $item_d = $item_q->fetch_row();


        if ($item_d[1] < 1) {
            if (!$sql_op->delete('mst_negara', "id_negara=$itemID")) {
                $error_num++;
            }
        } else {
            $msg = str_replace('{item_name}', $item_d[0], ('lokasi ({item_name}) masih digunakan')); //mfc
            $msg = str_replace('{number_items}', $item_d[1], $msg);
            $still_have_item[] = $msg;
            $error_num++;
        }
    }

    if ($still_have_item) {
        $undeleted_coll_types = '';
        foreach ($still_have_item as $coll_type) {
            $undeleted_coll_types .= $coll_type."\n";
        }
        utility::jsAlert(('Below data can not be deleted:').$undeleted_coll_types);
        echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    }
    // error alerting
    if ($error_num == 0) {
        utility::jsAlert(('All Data Successfully Deleted'));
        echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    } else {
        utility::jsAlert(('Some or All Data NOT deleted successfully!\nPlease contact system administrator'));
        echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'?'.$_POST['lastQueryStr'].'\');</script>';
    }
    exit();
}
/* RECORD OPERATION END */

/* search form */
?>
<fieldset class="menuBox">
<div class="menuBoxInner masterFileIcon">
	<div class="per_title">
	    <h3><?php echo ('Kewarganegaraan'); ?></h3>
  </div>
	<div class="sub_section">
	  <div class="btn-group">
      <a href="<?php echo MWB; ?>master_file/citizen.php" class="btn btn-default"><i class="fa fa-bars"></i>&nbsp;<?php echo ('Daftar'); ?></a>
      <a href="<?php echo MWB; ?>master_file/citizen.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo ('Tambah'); ?></a>
	  </div>
    <form name="search" action="<?php echo MWB; ?>master_file/citizen.php" id="search" method="get" style="display: inline;"><?php echo ('Search'); ?> :
    <input type="text" name="keywords" size="30" style="width:30%;" />
    <input type="submit" id="doSearch" value="<?php echo ('Search'); ?>" class="button" />
    </form>
  </div>
</div>
</fieldset>
<?php
/* search form end */
/* main content */
if (isset($_POST['detail']) OR (isset($_GET['action']) AND $_GET['action'] == 'detail')) {
    if (!($can_read AND $can_write)) {
        die('<div class="errorBox">'.('You don\'t have enough privileges to view this section').'</div>');
    }
    $itemID = (integer)isset($_POST['itemID'])?$_POST['itemID']:0;
    $rec_q = $dbs->query("SELECT * FROM mst_negara WHERE id_negara=$itemID");
    $rec_d = $rec_q->fetch_assoc();

    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 'post');
    $form->submit_button_attr = 'name="saveData" value="'.('Save').'" class="button btn btn-success pull-right"';

    // form table attributes
    $form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
    $form->table_content_attr = 'class="alterCell2"';

    // edit mode flag set
    if ($rec_q->num_rows > 0) {
        $form->edit_mode = true;
        // record ID for delete process
        $form->record_id = $itemID;
        // form record title
        $form->record_title = $rec_d['nama_negara'];
        // submit button attribute
        $form->submit_button_attr = 'name="saveData" value="'.('Update').'" class="button"';
    }

    /* Form Element(s) */
    // coll_type_name
    $form->addTextField('text', 'nama_negara', ('Kewarganegaraan *)'), $rec_d['nama_negara']??'', 'required style="width: 60%;"');

    // edit mode messagge
    if ($form->edit_mode) {
        echo '<div class="infoBox alert bg-gray">'.('Anda akan merubah kewarganegaraan ').' : <b>'.$rec_d['nama_negara'].'</b>  <br />'.('Last Update').' '.$rec_d['last_update'].'</div>'; //mfc
    }
    // print out the form object
    echo $form->printOut();
} else {
    /* lokasi TYPE LIST */
    // table spec
    $table_spec = 'mst_negara';

    // create datagrid
    $datagrid = new simbio_datagrid();
    if ($can_read AND $can_write) {
        $datagrid->setSQLColumn(
            'id_negara', 
            'nama_negara AS \''.('Kewarganegaraan').'\'',
            'input_date AS \''.('Tanggal Input').'\'',
            'last_update AS \''.('Last Update').'\'');
    } else {
        $datagrid->setSQLColumn(
            'nama_negara AS \''.('Kewarganegaraan').'\'',
            'input_date AS \''.('Tanggal Input').'\'',
            'last_update AS \''.('Last Update').'\'');
    }
    $datagrid->setSQLorder('nama_negara ASC');

    // change the record order
    if (isset($_GET['fld']) AND isset($_GET['dir'])) {
        $datagrid->setSQLorder("'".urldecode($_GET['fld'])."' ".$dbs->escape_string($_GET['dir']));
    }

    // is there any search
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
       $keywords = $dbs->escape_string($_GET['keywords']);
       $datagrid->setSQLCriteria("citizen_name LIKE '%$keywords%'");
    }

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // set delete proccess URL
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, ($can_read AND $can_write));
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, ('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox alert alert-success">'.$msg.' : "'.$_GET['keywords'].'"</div>';
    }

    echo $datagrid_result;
}
/* main content end */
