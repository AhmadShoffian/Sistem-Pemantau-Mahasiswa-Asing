<?php

// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';

// hanya administrator yang memiliki hak istimewa untuk mengubah data modul
if ($_SESSION['uid'] != 1) {
    die('<div class="errorBox">'.('You don\'t have enough privileges to view this section').'</div>');
}

require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';

/* RECORD OPERATION */
if (isset($_POST['saveData'])) {
    // check form validity
    $moduleName = trim(strip_tags($_POST['moduleName']));
    $modulePath = trim(strip_tags($_POST['modulePath']));
    if (empty($moduleName) OR empty($modulePath)) {
        utility::jsAlert(('Module name and path can\'t be empty'));
        exit();
    } else {
        $data['module_path'] = $dbs->escape_string($modulePath);
        // periksa keberadaan jalur modul
        if (!file_exists(MDLBS.$data['module_path'].DS)) {
            utility::jsAlert(('Modules path doesn\'t exists! Please check again in module base directory'));
            exit();
        }
        $data['module_name'] = $dbs->escape_string($moduleName);
        $data['module_desc'] = trim($dbs->escape_string(strip_tags($_POST['moduleDesc'])));

        // create sql op object
        $sql_op = new simbio_dbop($dbs);
        if (isset($_POST['updateRecordID'])) {
            /* UPDATE RECORD MODE */
            // filter update record ID
            $updateRecordID = (integer)$_POST['updateRecordID'];
            // update the data
            $update = $sql_op->update('mst_module', $data, 'module_id='.$updateRecordID);
            if ($update) {
                // write log
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'].' update module data ('.$moduleName.') with path ('.$modulePath.')');
                utility::jsAlert(('Module Data Successfully Updated'));
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
            } else { utility::jsAlert(('Module Data FAILED to Updated. Please Contact System Administrator')."\nDEBUG : ".$sql_op->error); }
            exit();
        } else {
            /* INSERT RECORD MODE */
            // insert the data
            if ($sql_op->insert('mst_module', $data)) {
                // insert module privileges for administrator
                $module_id = $sql_op->insert_id;
                $dbs->query('INSERT INTO group_access VALUES (1, '.$module_id.', 1, 1)');
                // write log
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'].' add new module ('.$moduleName.') with path ('.$modulePath.')');
                utility::jsAlert(('New Module Data Successfully Saved'));
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\');</script>';
            } else { utility::jsAlert(('Module Data FAILED to Save. Please Contact System Administrator')."\n".$sql_op->error); }
            exit();
        }
    }
    exit();
} else if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
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
        // get module data
        $module_q = $dbs->query('SELECT module_name, module_path FROM mst_module WHERE module_id='.$itemID);
        $module_d = $module_q->fetch_row();
        if (!$sql_op->delete('mst_module', "module_id=$itemID")) {
            $error_num++;
        } else {
            // also delete all records related to this data
            // delete group privileges
            $dbs->query('DELETE FROM group_access WHERE module_id='.$itemID);
            // write log
            utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'].' DELETE module ('.$module_d[0].') with path ('.$module_d[1].')');
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
/* RECORD OPERATION */

/* search form */
?>
<fieldset class="menuBox">
<div class="menuBoxInner moduleIcon">
	<div class="per_title">
	  <h2><?php echo ('Modules'); ?></h2>
  </div>
	<div class="sub_section">
	  <div class="btn-group">
      <a href="<?php echo MWB; ?>system/module.php" class="btn btn-default"><i class="fa fa-bars"></i>&nbsp;<?php echo ('Modules List'); ?></a>
      <a href="<?php echo MWB; ?>system/module.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo ('Add New Modules'); ?></a>
	  </div>
    <form name="search" action="<?php echo MWB; ?>system/module.php" id="search" method="get" style="display: inline;"><?php echo ('Search'); ?> :
    <input type="text" name="keywords" size="30" />
    <input type="submit" id="doSearch" value="<?php echo ('Search'); ?>" class="btn btn-default" />
    </form>
  </div>
</div>
</fieldset>
<?php
/* search form end */
/* main content */
if (isset($_POST['detail']) OR (isset($_GET['action']) AND $_GET['action'] == 'detail')) {
    /* RECORD FORM */
    $itemID = (integer)isset($_POST['itemID'])?$_POST['itemID']:0;
    $rec_q = $dbs->query('SELECT * FROM mst_module WHERE module_id='.$itemID);
    $rec_d = $rec_q->fetch_assoc();

    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 'post');
    $form->submit_button_attr = 'name="saveData" value="'.('Save').'" class="btn btn-default"';

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
        $form->record_title = $rec_d['module_name'];
        // submit button attribute
        $form->submit_button_attr = 'name="saveData" value="'.('Update').'" class="btn btn-default"';
    }

    /* Form Element(s) */
    // module
    $form->addTextField('text', 'moduleName', ('Module Name').'*', $rec_d['module_name'], 'style="width: 50%;"');
    // module path
    $form->addTextField('text', 'modulePath', ('Module Path').'*', $rec_d['module_path'], 'style="width: 100%;"');
    // module desc
    $form->addTextField('text', 'moduleDesc', ('Module Description'), $rec_d['module_desc'], 'style="width: 100%;"');

    // edit mode messagge
    if ($form->edit_mode) {
        echo '<div class="infoBox alert bg-gray">'.('You are going to edit data').' : <b>'.$rec_d['module_name'].'</b></div>'; //mfc
    }
    // mencetak objek formulir
    echo $form->printOut();
} else {
    /* MODULE LIST */
    // table spec
    $table_spec = 'mst_module AS mdl';

    // create datagrid
    $datagrid = new simbio_datagrid();
    $datagrid->setSQLColumn('mdl.module_id',
        'mdl.module_name AS \''.('Module Name').'\'',
        'mdl.module_desc AS \''.('Module Description').'\'');
    $datagrid->setSQLorder('module_name ASC');

    // is there any search
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
       $keywords = $dbs->escape_string($_GET['keywords']);
       $datagrid->setSQLCriteria("mdl.module_name LIKE '%$keywords%'");
    }

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // set delete proccess URL
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, true);
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, ('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox alert alert-success">'.$msg.' : "'.$_GET['keywords'].'"</div>';
    }

    echo $datagrid_result;
}
/* main content end */
