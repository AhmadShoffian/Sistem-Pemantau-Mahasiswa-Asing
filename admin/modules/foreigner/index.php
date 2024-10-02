<?php
use PhpOffice\PhpSpreadsheet\Chart\Title;

// perintah untuk mengautentikasi
if (!defined('INDEX_AUTH')) {
  define('INDEX_AUTH', '1');
}
// perintah untuk mendapatkan akses database penuh
define('DB_ACCESS', 'fa');
if (!defined('SB')) {
  // konfigurasi sistem utama
  require '../../../sysconfig.inc.php';
  // memulai sesi
  require SB.'admin/default/session.inc.php';
}
// pembatasan akses berbasis ip
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-kerjasama');
require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';
require SIMBIO.'simbio_FILE/simbio_file_upload.inc.php';
// pemeriksaan hak istimewa
$can_read = utility::havePrivilege('foreigner', 'r');
$can_write = utility::havePrivilege('foreigner', 'w');

// load settings
utility::loadSettings($dbs);

$in_pop_up = false;
// periksa apakah kita berada didalam jendela popup
if (isset($_GET['inPopUp'])) {
  $in_pop_up = true;
}

if (!function_exists('getimagesizefromstring')) {
  function getimagesizefromstring($string_data)
  {
     $uri = 'data://application/octet-stream;base64,'  . base64_encode($string_data);
     return getimagesize($uri);
  }
}

/* hapus gambar */
if (isset($_POST['removeImage']) && isset($_POST['img']) && isset($_POST['id']) && isset($_POST['img_name'])) {
    $query = sprintf('UPDATE orang_asing SET '.$_POST['img'].'_img = NULL WHERE id_orang_asing=%d', $_POST['id']);
    $_delete = $dbs->query($query);
    @unlink(sprintf(REPOBS.'/documents/passport/%s',$_POST['img_name']));
    utility::jsAlert('Gambar dihapus, halaman disegarkan !');
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.MWB.'foreigner/index.php\', {method: \'post\', addData: \'itemID='.$_POST['id'].'&detail=true&ajaxload=1\'});</script>';
    exit();
}
/* RECORD OPERATION */
if (isset($_POST['saveData']) AND $can_read) {
  foreach ($_POST as $key => $value) {
    $post[$key] = trim($dbs->escape_string(strip_tags($value)));
  }

  $name = trim(strip_tags($_POST['nama_mhs']));
  // check form validity
  if (empty($name)) {
    utility::jsAlert('Nama tidak boleh kosong');
    //exit();
  } else {
    $id_random = rand(10000, 99999);
    $sponsor_insert      = $id_random;
    $pasport_insert       = $id_random;
    $itas_insert         = $id_random;
    $orangtua_insert     = $id_random;

    $id_sponsor      = $_post['id_sponsor'];
    $id_paspor       = $_post['id_pasport'];
    $id_itas         = $_post['id_ijin_tinggal'];
    $id_orangtua     = $_post['id_orangtua'];

    // personal data
    $data['nim'] = $post['nim'];
    $data['nama_mhs'] = $post['nama_mhs'];
    $data['program'] = $post['program'];
    $data['jurusan'] = $post['jurusan'];

    //cek kewarganegaraan
    if (stripos($_POST['id_negara'], 'NEW:') === 0) {
      $citizen = strtoupper(str_ireplace('NEW:', '', trim(strip_tags($_POST['id_negara']))));
      $new_id = utility::getID($dbs, 'mst_negara', 'id_negara', 'nama_negara', $citizen);
      $data['id_negara'] = $new_id;
    } else if (intval($_POST['id_negara']) > 0) {
      $data['id_negara'] = intval($_POST['id_negara']);
    }
    $data['tempat_lahir'] = $post['tempat_lahir'];
    $data['tanggal_lahir'] = $_POST['tanggal_lahir'];
    $data['jenis_kelamin'] = $post['jenis_kelamin'];
    $data['nomor_telepon'] = $post['nomor_telepon'];
    $data['email'] = $post['email'];
    $data['alamat'] = $post['alamat'];
    $data['id_provinsi'] = intval($post['id_provinsi']);
    $data['id_kabupaten'] = intval($_POST['id_kabupaten']);
    $data['id_kecamatan'] = intval($_POST['id_kecamatan']);
    $data['id_desa'] = intval($_POST['id_desa']);
    

    // contact
    $orangtua['nama_orangtua'] = $post['nama_orangtua'];
    $orangtua['kontak_telepon'] = $post['kontak_telepon'];
    $orangtua['kontak_email'] = $post['kontak_email'];
    $orangtua['alamat_orangtua'] = $post['alamat_orangtua'];

    // passport
    $pasport['no_pasport'] = $post['no_pasport'];
    $pasport['tanggal_terbit_pasport'] = $_POST['tanggal_terbit_pasport'];
    $pasport['tanggal_berakhir_pasport'] = $_POST['tanggal_berakhir_pasport'];

    // permit
    $itas['no_ijin_tinggal'] = $post['no_ijin_tinggal'];
    $itas['jenis_ijin_tinggal'] = intval($post['jenis_ijin_tinggal']);
    $itas['tanggal_terbit_ijin_tinggal'] = $_POST['tanggal_terbit_ijin_tinggal'];
    $itas['tanggal_berakhir_ijin_tinggal'] = $_POST['tanggal_berakhir_ijin_tinggal'];

    // sponsor
    $sponsor['nama_sponsor'] = $post['nama_sponsor'];
    $sponsor['jenis_sponsor'] = intval($post['jenis_sponsor']);
    $sponsor['alamat_sponsor'] = $post['alamat_sponsor'];

    $data['input_date'] = date('Y-m-d H:i:s');
    $data['last_update'] = date('Y-m-d H:i:s');

    if(isset($_FILES)){
      foreach ($_FILES as $key => $value) {
        if(!$value['error']){
          $image_upload = new simbio_file_upload();
          $image_upload->setAllowableFormat($sysconf['allowed_images']);
          $image_upload->setMaxSize($sysconf['max_image_upload']*1024);
          $image_upload->setUploadDir(REPOBS.DS.str_replace('/', DS, 'documents/'.$key));
          $img_upload_status = $image_upload->doUpload($key,$key.'_'.$pasport['nama_mhs'].'_'.$pasport['no_pasport']);
          if ($img_upload_status == UPLOAD_SUCCESS) {
            $file_ext = substr($value['name'], strrpos($value['name'], '.')+1);
            $pasport[$key.'_img'] = $key.'_'.$pasport['nama_mhs'].'_'.$pasport['no_pasport'].'.'.$file_ext;
          }else{
            utility::jsAlert($image_upload->error); 
          }
        }
      }
    }

    // buat objek sql_op
    $sql_op = new simbio_dbop($dbs);


    if (isset($_POST['updateRecordID'])) {

      /* UPDATE RECORD MODE */
      // remove input date
      unset($data['input_date']);
      unset($data['uid']);
      // filter update record ID
      $updateRecordID = (integer)$_POST['updateRecordID'];
      $updateSponsor = (integer)$_POST['id_sponsor'];
      $updatePasport = (integer)$_POST['id_pasport'];
      $updateItas = (integer)$_POST['id_ijin_tinggal'];
      $updateOrngTua = (integer)$_POST['id_orangtua'];

      // update data
      $update_data = [
        $update1 = $sql_op->update('orang_asing', $data, 'id_orang_asing='.$updateRecordID),
        $update2 = $sql_op->update('sponsor', $sponsor, 'id_sponsor='.$updateSponsor),
        $update3 = $sql_op->update('paspor', $pasport, 'id_pasport='.$updateItas),
        $update4 = $sql_op->update('ijin_tinggal', $itas, 'id_ijin_tinggal='.$updatePasport),
        $update5 = $sql_op->update('orang_tua', $orangtua, 'id_orangtua='.$updateOrngTua),
      ];
      // send an alert
      if ($update_data) {
          utility::jsAlert(('Data Successfully Updated'));
      } else { 
        utility::jsAlert(('Data FAILED to Updated. Please Contact System Administrator'.json_encode($data))."\n".$sql_op->error); 
      }
    } else {
      $data['id_sponsor'] = $sponsor_insert;
      $data['id_pasport'] = $pasport_insert;
      $data['id_ijin_tinggal'] = $itas_insert;
      $data['id_orangtua'] = $orangtua_insert;
      /* INSERT RECORD MODE */
      $sponsor['id_sponsor'] = $sponsor_insert;
      $orangtua['id_orangtua'] = $orangtua_insert;
      $pasport['id_pasport'] = $pasport_insert;
      $itas['id_ijin_tinggal'] = $itas_insert;  

      // insert the data
      $insert_data = [
        $insert1 = $sql_op->insert('orang_asing', $data),
        $insert2 = $sql_op->insert('sponsor', $sponsor),
        $insert3 = $sql_op->insert('paspor', $pasport),
        $insert4 = $sql_op->insert('ijin_tinggal', $itas),
        $insert5 = $sql_op->insert('orang_tua', $orangtua),
      ];
      //utility::jsAlert(json_encode($data));
      if ($insert_data) {
        utility::jsAlert(('Data disimpan'));
      } else { 
        utility::jsAlert(('Data FAILED to Save. Please Contact System Administrator')."\n".$sql_op->error); 
      }
    }
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.MWB.'foreigner/index.php\', {method: \'post\', addData: \'itemID='.(isset($updateRecordID)?$updateRecordID:$citizen_id).'&detail=true\'});</script>';
    exit();
  }
  exit();
} else if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
  if (!($can_read AND $can_write)) {
    die();
  }
  if (!simbio_form_maker::isTokenValid()) {
    utility::jsAlert(('Invalid form submission token!'));
    exit();
  }

  /* DATA DELETION PROCESS */
  // create sql op object
  $sql_op = new simbio_dbop($dbs);
  $failed_array = array();
  $error_num = 0;
  $still_have_item = array();
  if (!is_array($_POST['itemID'])) {
    // make an array
    $_POST['itemID'] = array((integer)$_POST['itemID']);
  }
  // loop array
  $http_query = sprintf('SELECT * FROM orang_asing 
                          JOIN ijin_tinggal ON orang_asing.id_ijin_tinggal=ijin_tinggal.id_ijin_tinggal WHERE id_orang_asing
                          JOIN paspor ON orang_asing.id_pasport=paspor.id_pasport WHERE id_orang_asing
                          JOIN sponsor ON orang_asing.id_sponsor=sponsor.id_sponsor WHERE id_orang_asing
                          JOIN orang_tua ON orang_asing.id_orangtua=orang_tua.id_orangtua
                        WHERE id_orang_asing=%d',$itemID);
  // $rec_q = $dbs->query($http_query);
  // $rec_d = $rec_q->fetch_assoc();
  foreach ($_POST['itemID'] as $itemID) {
    $itemID = (integer)$itemID;
      if (!
      $sql_op->delete('orang_asing', "id_orang_asing=$itemID")) {
        $error_num++;
      } 
  }
  // error alerting
  if ($error_num == 0) {
    utility::jsAlert(('All Data Successfully Deleted'));
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\', {addData: \''.$_POST['lastQueryStr'].'\'});</script>';
  } else {
    utility::jsAlert(('Some or All Data NOT deleted successfully!\nPlease contact system administrator'));
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\', {addData: \''.$_POST['lastQueryStr'].'\'});</script>';
  }
  exit();
}
/* RECORD OPERATION END */

if (!$in_pop_up) {

  /* search form */
?>
<fieldset class="menuBox">
<div class="menuBoxInner biblioIcon">
  <div class="per_title" style="font-weight: bolder;">
	  <h3><?php echo ('WNA'); ?></h3>
  </div>
  <div class="sub_section">
	  <div class="btn-group">
		  <a href="<?php echo MWB; ?>foreigner/index.php" class="btn btn-default"><i class="fa fa-bars"></i>&nbsp;<?php echo ('Daftar'); ?></a>
		  <a href="<?php echo MWB; ?>foreigner/index.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo ('Tambah'); ?></a>
	  </div>
    
    <form name="search" action="<?php echo MWB; ?>foreigner/index.php" id="search" method="get" style="display: inline;"><?php echo ('Search'); ?> :
    <input type="text" name="keywords" size="30" style="width:30%;" />
    <input type="submit" id="doSearch" value="<?php echo ('Search'); ?>" class="button" />
    </form>

  </div>
</div>
</fieldset>
<?php
/* search form end */
}

/* content utama */
if (isset($_POST['detail']) OR (isset($_GET['action']) AND $_GET['action'] == 'detail')) {
  /* RECORD FORM */
  // try query
  $itemID = (integer)isset($_POST['itemID'])?$_POST['itemID']:0;
  $_sql_rec_q = sprintf('SELECT * FROM orang_asing 
                              LEFT JOIN ijin_tinggal ON orang_asing.id_ijin_tinggal=ijin_tinggal.id_ijin_tinggal
                              LEFT JOIN paspor ON orang_asing.id_pasport=paspor.id_pasport
                              LEFT JOIN sponsor ON orang_asing.id_sponsor=sponsor.id_sponsor
                              LEFT JOIN orang_tua ON orang_asing.id_orangtua=orang_tua.id_orangtua
                        WHERE id_orang_asing=%d', $itemID);
  $rec_q = $dbs->query($_sql_rec_q);
  $rec_d = $rec_q->fetch_assoc();

  // create new instance
  $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 'post');
  $form->submit_button_attr = 'name="saveData" value="'.('Simpan').'" class="btn btn-default pull-right" style="margin:12px;" ';

  // form table attributes
  $form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
  $form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
  $form->table_content_attr = 'class="alterCell2"';

  $visibility = 'makeVisible';
  // edit mode flag set
  if ($rec_q->num_rows > 0) {
    $form->edit_mode = true;

    $visibility = 'makeVisible';
    // record ID for delete process
    if (!$in_pop_up) {
      // form record id
      $form->record_id = $itemID;
    } else {
      $form->addHidden('updateRecordID', $itemID);
      if (isset($_POST['itemCollID'])) {
        $form->addHidden('itemCollID', $_POST['itemCollID']);
      }
      $form->back_button = false;
    }
    // form record title
    $form->record_title = $rec_d['nama_mhs'];
    // submit button attribute
    $form->submit_button_attr = 'name="saveData" value="'.('Update').'" class="btn btn-default"';
    // element visibility class toogle
    $visibility = 'makeHidden';

  } 

  /* Form Element(s) */
//===================================================
  $form->addAnything((' '),'<div class="headerField">DATA PRIBADI</div>');

  $form->addTextField('hidden', 'id_pasport', (''), $rec_d['id_pasport']??'');
  $form->addTextField('hidden', 'id_sponsor', (''), $rec_d['id_sponsor']??'');
  $form->addTextField('hidden', 'id_ijin_tinggal', (''), $rec_d['id_ijin_tinggal']??'');
  $form->addTextField('hidden', 'id_orangtua', (''), $rec_d['id_orangtua']??'');
  // nim
  $form->addTextField('text', 'nim', ('NIM'), $rec_d['nim']??'', 'rows="1" required style="width: 100%; overflow: auto;');
  //nama
  $form->addTextField('text', 'nama_mhs', ('Nama Lengkap'), $rec_d['nama_mhs']??'', 'rows="1" required style="width: 100%; overflow: auto;');
  
  // kewarganegaraan
  $lk_options[] = array('NONE', '');
  if (isset($rec_d['id_negara'])) {
    $lk_q = $dbs->query(sprintf('SELECT id_negara, nama_negara FROM mst_negara WHERE id_negara=%d', $rec_d['id_negara']));
    while ($lk_d = $lk_q->fetch_row()) {
      $lk_options[] = array($lk_d[0], $lk_d[1]);
    }
  }
  
  $form->addSelectList('id_negara', ('Kewarganegaraan'), $lk_options, $rec_d['id_negara']??'', 'class="select2" data-src="'.SWB.'admin/AJAX_lookup_handler.php?format=json&allowNew=true" data-src-table="mst_negara" data-src-cols="id_negara:nama_negara"');
  
  // tempat lahir
  $form->addTextField('text', 'tempat_lahir', ('Tempat Lahir'), $rec_d['tempat_lahir']??'', 'style="width: 100%;" required');
  
  // tanggal lahir
  $str_date = '<div class="input-group date" data-provide="datepicker" style="width:150px;" data-date-format="yyyy-mm-dd">
  <input type="text" class="form-control" name= "tanggal_lahir" value="'.($rec_d['tanggal_lahir']??date('Y-m-d')).'">
  <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
  </div>';
  $form->addAnything(('Tanggal Lahir'),$str_date);
  
  // jenis kelamin
  $gender_chbox[0] = array('1', ('Laki Laki'));
  $gender_chbox[1] = array('0', ('Perempuan'));
  $form->addRadio('jenis_kelamin', ('Jenis Kelamin'), $gender_chbox, !empty($rec_d['jenis_kelamin'])?$rec_d['jenis_kelamin']:'0');
  
  // nomor telepon
  $form->addTextField('text', 'nomor_telepon', ('Nomor Telepon'), $rec_d['nomor_telepon']??'', 'style="width: 100%;" required');
  
  // surel
  $form->addTextField('text', 'email', ('Email'), $rec_d['email']??'', 'style="width: 100%;" required');
  
   // Jurusan
    $jurusan_mhs = null;
    $jurusan_mhs[] = array('S1 - TARI','S1 - TARI');
    $jurusan_mhs[] = array('S1 - SENI KARAWITAN','S1 - SENI KARAWITAN');
    $jurusan_mhs[] = array('S1 - MUSIC','S1 - MUSIC');
    $jurusan_mhs[] = array('S1 - PENCIPTAAN MUSIC','S1 - PENCIPTAAN MUSIC');
    $jurusan_mhs[] = array('S1 - PENDIDIKAN MUSIC','S1 - PENDIDIKAN MUSIC');
    $jurusan_mhs[] = array('S1 - PENYAJIAN MUSIC','S1 - PENYAJIAN MUSIC');
    $jurusan_mhs[] = array('S1 - TEATER','S1 - TEATER');
    $jurusan_mhs[] = array('S1 - ETNOMUSIKALOGI','S1 - ETNOMUSIKALOGI');
    $jurusan_mhs[] = array('S1 - SENI PENDALANGAN','S1 - SENI PENDALANGAN');
    $jurusan_mhs[] = array('S1 - PENDIDIKAN SENI PERTUNJUKAN','S1 -  PENDIDIKAN SENI PERTUNJUKAN');
    $jurusan_mhs[] = array('S1 - SENI MURNI','S1 - SENI MURNI');
    $jurusan_mhs[] = array('S1 - KRIYA','S1 - KRIYA');
    $jurusan_mhs[] = array('S1 - DESAIN MODE KRIYA BATIK','S1 - DESAIN MODE KRIYA BATIK');
    $jurusan_mhs[] = array('S1 - DESAIN INTERIOR','S1 - DESAIN INTERIOR');
    $jurusan_mhs[] = array('S1 - DESAIN KOMUNIKASI VISUAL','S1 - DESAIN KOMUNIKASI VISUAL');
    $jurusan_mhs[] = array('S1 - DESAIN PRODUK','S1 - DESAIN PRODUK');
    $jurusan_mhs[] = array('S1 - TATA KELOLA SENI','S1 - TATA KELOLA SENI');
    $jurusan_mhs[] = array('S1 - KONSERVASI SENI','S1 - KONSERVASI SENI');
    $jurusan_mhs[] = array('S1 - FOTOGRAFI','S1 - FOTOGRAFI');
    $jurusan_mhs[] = array('S1 - FILM DAN TELEVISI','S1 - FILM DAN TELEVISI');
    $jurusan_mhs[] = array('D4 - ANIMASI','D4 - ANIMASI');
    $jurusan_mhs[] = array('D4 - DESAIN MEDIA','D4 - DESAIN MEDIA');
    $jurusan_mhs[] = array('D4 - PRODUK FILM DAN TELEVISI','D4 - PRODUK FILM DAN TELEVISI');

    $form->addSelectList('jurusan', ('Jurusan'), $jurusan_mhs, $rec_d['jurusan']??'', 'class="select2 ' . $visibility . '"');

  // program beasiswa
  $progam_beasiswa = null;
  $progam_beasiswa[] = array('DARMASISWA','DARMASISWA');
  $progam_beasiswa[] = array('BEASISWA UNGGULAN','BEASISWA UNGGULAN');
  $progam_beasiswa[] = array('REGULER','REGULER');
  $progam_beasiswa[] = array('SHORT COURSE','SHORT COURSE');
  $progam_beasiswa[] = array('STUDENT EXCHAGE','STUDENT EXCHAGE');
  $form->addSelectList('program', ('Jenis Program Beasiswa'), $progam_beasiswa, $rec_d['program']??'', 'class="select2 ' . $visibility . '"');

  // alamat
  $form->addTextField('textarea', 'alamat', ('Alamat Lengkap'), $rec_d['alamat']??'', ' class="form-control" style="width: 100%;"  required');

  // provinsi
  $prov_q = $dbs->query('SELECT id, nama FROM provinsi');
  $prov_options = array('','');
  while ($prov_d = $prov_q->fetch_row()) {
    $prov_options[] = array($prov_d[0], $prov_d[1]);
  }
  $form->addSelectList('id_provinsi', ('Provinsi'), $prov_options, $rec_d['id_provinsi']??'', 'class="select2 ' . $visibility . '" ');

    // kabupaten
    $regencies_options = [];
    if(isset($rec_d['id_kabupaten'])){
      $query = 'SELECT id, nama FROM kabupaten WHERE id_provinsi=\''.$rec_d['id_provinsi'].'\'';
      $regencies_q = $dbs->query($query);
      while ($regencies_d = $regencies_q->fetch_row()) {
        $regencies_options[] = array($regencies_d[0], $regencies_d[1]);
      }
    }

    $form->addSelectList('id_kabupaten', ('Kabupaten / Kota'), $regencies_options, $rec_d['id_kabupaten']??'', 'class="select2 ' . $visibility . '" ');

    // kecamatan
    $district_options = [];
    if(isset($rec_d['id_kabupaten'])){
      $query = 'SELECT id, nama FROM kecamatan WHERE id_kabupaten=\''.$rec_d['id_kabupaten'].'\'';
      $district_q = $dbs->query($query);
      while ($district_d = $district_q->fetch_row()) {
        $district_options[] = array($district_d[0], $district_d[1]);
      }
    }

    $form->addSelectList('id_kecamatan', ('Kecamatan'), $district_options, $rec_d['id_kecamatan']??'', 'class="select2 ' . $visibility . '" ');

    // desa
    $villages_options = [];
    if(isset($rec_d['id_desa'])){
      $query = 'SELECT id, nama FROM desa WHERE id_kecamatan=\''.$rec_d['id_kecamatan'].'\'';
      $villages_q = $dbs->query($query);
      while ($villages_d = $villages_q->fetch_row()) {
        $villages_options[] = array($villages_d[0], $villages_d[1]);
      }
    }

    $form->addSelectList('id_desa', ('Desa / Kelurahan'), $villages_options, $rec_d['id_desa']??'', 'class="select2 ' . $visibility . '" ');

    // family relation
    $form->addAnything((' '),'<div class="headerField">DATA KELUARGA</div>');

    // nama 
    $form->addTextField('text', 'nama_orangtua', ('Nama'), $rec_d['nama_orangtua']??'', 'style="width: 100%;"  required');

    // nomor telepon
    $form->addTextField('text', 'kontak_telepon', ('Nomor Telepon'), $rec_d['kontak_telepon']??'', 'style="width: 100%;" required');

    // surel
    $form->addTextField('text', 'kontak_email', ('Email'), $rec_d['kontak_email']??'', 'style="width: 100%;" required');

    // alamat
    $form->addTextField('textarea', 'alamat_orangtua', ('Alamat Lengkap'), $rec_d['alamat_orangtua']??'', ' class="form-control" style="width: 100%;" required');

    // family relation
    $form->addAnything((' '),'<div class="headerField">DATA IMIGRASI</div>');

    $form->addTextField('text', 'no_pasport', ('Nomor Paspor'), $rec_d['no_pasport']??'', 'style="width: 100%;" ');

    $str_date = '<div class="input-group date" data-provide="datepicker" style="width:150px;" data-date-format="yyyy-mm-dd">
                 <input type="text" class="form-control" name= "tanggal_terbit_pasport" value="'.($rec_d['tanggal_terbit_pasport']??date('Y-m-d')).'">
                 <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                 </div>';
    $form->addAnything(('Tanggal Terbit Paspor'),$str_date);

    $str_date = '<div class="input-group date" data-provide="datepicker" style="width:150px;" data-date-format="yyyy-mm-dd">
                 <input type="text" class="form-control" name= "tanggal_berakhir_pasport" value="'.($rec_d['tanggal_berakhir_pasport']??date('Y-m-d')).'">
                 <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                 </div>';
    $form->addAnything(('Tanggal Berakhir Paspor'),$str_date);

    $form->addTextField('text', 'no_ijin_tinggal', ('Nomor Ijin Tinggal'), $rec_d['no_ijin_tinggal']??'', 'style="width: 100%;" ');

    $permit_options = null;
    $permit_options[] = array('1','VISA KUNJUNGAN');
    $permit_options[] = array('2','KITAS');
    $permit_options[] = array('3','KITAP');
    $form->addSelectList('jenis_ijin_tinggal', ('Jenis Ijin Tinggal'), $permit_options, $rec_d['jenis_ijin_tinggal']??'', 'class="select2 ' . $visibility . '"');


    $str_date = '<div class="input-group date" data-provide="datepicker" style="width:150px;" data-date-format="yyyy-mm-dd">
                 <input type="text" class="form-control" name= "tanggal_terbit_ijin_tinggal" value="'.($rec_d['tanggal_terbit_ijin_tinggal']??date('Y-m-d')).'">
                 <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                 </div>';
    $form->addAnything(('Tanggal Terbit Ijin Tinggal'),$str_date);

    $str_date = '<div class="input-group date" data-provide="datepicker" style="width:150px;" data-date-format="yyyy-mm-dd">
                 <input type="text" class="form-control" name= "tanggal_berakhir_ijin_tinggal" value="'.($rec_d['tanggal_berakhir_ijin_tinggal']??date('Y-m-d')).'">
                 <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                 </div>';
    $form->addAnything(('Tanggal Berakhir Ijin Tinggal'),$str_date);

    $form->addTextField('text', 'nama_sponsor', ('Nama Sponsor'), $rec_d['nama_sponsor']??'', 'style="width: 100%;" ');

    $sponsor_options = null;
    $sponsor_options[] = array('1','PERORANGAN');
    $sponsor_options[] = array('2','ORGANISASI');
    $sponsor_options[] = array('3','LEMBAGA PENDIDIKAN');
    $sponsor_options[] = array('4','PERUSAHAAN');
    $form->addSelectList('jenis_sponsor', ('Jenis Sponsor'), $sponsor_options, $rec_d['jenis_sponsor']??'', 'class="select2 ' . $visibility . '"');

    // alamat
    $form->addTextField('textarea', 'alamat_sponsor', ('Alamat Sponsor'), $rec_d['alamat_sponsor']??'', ' class="form-control" style="width: 100%;" ');

    //===================================================
    $form->addAnything((' '),'<div class="headerField">DOKUMEN</div>');

    // photo 
    $str_input = '';
    if(isset($rec_d['photos_img']) && file_exists(REPOBS.'/documents/photos/'.$rec_d['photos_img'])){
        $str_input .= '<div style="padding:10px;">';
        $str_input .= '<img src="../repository/documents/photos/'.$rec_d['photos_img'].'" class="img-fluid rounded" style="width:250px;" alt="Image cover">';
        $str_input .= '<br/><a id="btn-hapus" href="'.MWB.'foreigner/index.php" postdata="removeImage=true&id='.$rec_d['id_orang_asing'].'&img=photos&img_name='.$rec_d['photos_img'].'" class="btn btn-sm btn-danger ' . $visibility . '">'.('Remove Image').'</a></div>';
    }else{
    $str_input .= '<div class="custom-file col-4">';
    $str_input .= simbio_form_element::textField('file', 'photos', '', 'class="custom-file-input"');
    $str_input .= '<label class="custom-file-label" for="customFile">pilih file gambar </label>';
    $str_input .= '</div>';
    $str_input .= ' <div class="mt-2 ml-2">Maximum '.$sysconf['max_image_upload'].' KB</div>';
    $str_input .= '</div>';
    }
    $form->addAnything(('Foto'), $str_input);

    // gambar paspor
    $str_input = '';
    if(isset($rec_d['passport_img']) && file_exists(REPOBS.'/documents/passport/'.$rec_d['passport_img'])){
        $str_input .= '<div style="padding:10px;">';
        $str_input .= '<img src="../repository/documents/passport/'.$rec_d['passport_img'].'" class="img-fluid rounded" style="width:250px;" alt="Image cover">';
        $str_input .= '<br/><a href="'.MWB.'foreigner/index.php" postdata="removeImage=true&id='.$rec_d['id_orang_asing'].'&img=passport&img_name='.$rec_d['passport_img'].'" class="btn btn-sm btn-danger ' . $visibility . '">'.('Remove Image').'</a></div>';
    }else{
    $str_input .= '<div class="custom-file col-4">';
    $str_input .= simbio_form_element::textField('file', 'passport', '', 'class="custom-file-input"');
    $str_input .= '<label class="custom-file-label" for="customFile">pilih file gambar </label>';
    $str_input .= '</div>';
    $str_input .= ' <div class="mt-2 ml-2">Maximum '.$sysconf['max_image_upload'].' KB</div>';
    $str_input .= '</div>';
    }
    $form->addAnything(('Gambar Paspor'), $str_input);

        // edit mode messagge
    if ($form->edit_mode) {
        echo '<div class="infoBox alert bg-gray">'.('Anda akan merubah data ').' : <b>'.$rec_d['nama_mhs'].'</b>  <br />'.('Last Update').' '.$rec_d['last_update'].'</div>'; //mfc
    }

    // print out the form object
    echo $form->printOut();
?>
    <script type="text/javascript">
         $('#id_provinsi').chosen().change(function() {
              var ID = $(this).val();
                if(ID) {
                    $.ajax({
                        url: '<?=SWB?>admin/AJAX_lookup_handler.php?getchain=true',
                        type: "POST",
                        data: {tableName:'kabupaten',tableFields:'id,nama', id:ID, tableKey:'id_provinsi'},
                        success:function(datas) {    
                          $('#id_kabupaten').html(datas).trigger("liszt:updated");
                          $('#id_kecamatan').html('').trigger("liszt:updated");
                          $('#id_desa').html('').trigger("liszt:updated");
                        }
                  });
                }
            });
            $('#id_kabupaten').chosen().change(function() {
              var ID = $(this).val();
                if(ID) {
                    $.ajax({
                        url: '<?=SWB?>admin/AJAX_lookup_handler.php?getchain=true',
                        type: "POST",
                        data: {tableName:'kecamatan',tableFields:'id,nama', id:ID, tableKey:'id_kabupaten'},
                        success:function(datas) {    
                          $('#id_kecamatan').html(datas).trigger("liszt:updated");
                          $('#id_desa').html('').trigger("liszt:updated");
                        }
                  });
                }
            });
            $('#id_kecamatan').chosen().change(function() {
              var ID = $(this).val();
                if(ID) {
                    $.ajax({
                        url: '<?=SWB?>admin/AJAX_lookup_handler.php?getchain=true',
                        type: "POST",
                        data: {tableName:'desa',tableFields:'id,nama', id:ID, tableKey:'id_kecamatan'},
                        success:function(datas) {    
                          $('#id_desa').html(datas).trigger("liszt:updated");
                        }
                  });
                }
            });
        </script>
    <?php
    
} else {

  // create datagrid
  $datagrid = new simbio_datagrid();
  // table spec
  $table_spec = 'orang_asing f LEFT JOIN mst_negara mc ON f.id_negara=mc.id_negara
                               LEFT JOIN paspor p ON f.id_pasport=p.id_pasport
                               LEFT JOIN sponsor sp ON f.id_sponsor=sp.id_sponsor
                               LEFT JOIN orang_tua ot ON f.id_orangtua=ot.id_orangtua
                               LEFT JOIN ijin_tinggal it ON f.id_ijin_tinggal=it.id_ijin_tinggal';
  // $table_spec = 'orang_asing f WHERE jurusan.$jurusan_mhs';
  if ($can_read AND $can_write) {
    $datagrid->setSQLColumn('f.id_orang_asing', 'f.id_orang_asing AS bid',
    'f.nama_mhs AS \''.('Nama').'\'',
    'mc.nama_negara AS \''.('Kewarganegaraan').'\'',
    'p.no_pasport AS \''.('Nomor Paspor').'\'',
    'it.no_ijin_tinggal AS \''.('Nomor Ijin Tinggal').'\'',
    'f.input_date AS \''.('Tanggal Entri').'\'');
    $datagrid->column_width = array('5%', '10%', '20%', '20%');
  } else {
    $datagrid->setSQLColumn('f.id_orang_asing AS bid', 
    'f.nim AS \''.('NIM').'\'',
    'f.nama_mhs AS \''.('Nama').'\'',
    'f.jurusan AS \''.('Jurusan').'\'',
    'f.program AS \''.('Program').'\'',
    'mc.nama_negara AS \''.('Negara').'\'',
    'p.tanggal_berakhir_pasport AS \''.('Masa Paspor').'\'',
    'f.tanggal_berakhir_ijin_tinggal AS \''.('Masa Ijin Tinggal').'\'');
    $datagrid->column_width = array('5%', '10%', '20%', '20%');
  }

  $datagrid->invisible_fields = array(0);
  $datagrid->setSQLorder('f.last_update DESC');

  // set group by
  //$datagrid->sql_group_by = 'f._id';

  // atur tabel dan atribut tabel
  $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
  $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
  // set delete proccess URL
  $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];
  $datagrid->debug = true;

  if (isset($_GET['keywords']) AND $_GET['keywords']) {
    $keywords = $dbs->escape_string($_GET['keywords']);
    $datagrid->setSQLCriteria("f.nama_mhs LIKE '%$keywords%'");
  }
  // masukkan hasilnya ke dalam variabel
  $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 10, ($can_read AND $can_write));

  if (isset($_GET['keywords']) AND $_GET['keywords']) {
    $msg = str_replace('{result->num_rows}', $datagrid->num_rows, ('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
    echo '<div class="infoBox alert alert-success">'.$msg.' : "'.$_GET['keywords'].'"</div>';
  }
  echo $datagrid_result;
}
?>
