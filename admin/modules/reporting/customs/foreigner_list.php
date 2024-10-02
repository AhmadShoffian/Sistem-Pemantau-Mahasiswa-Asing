<?php
// kunci untuk mengautentikasi
define('INDEX_AUTH', '1');

// main system configuration
require '../../../../sysconfig.inc.php';
// konfigurasi sistem utama
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-circulation');
// memulai sesi
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
// pemeriksaan hak istimewa
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.('You don\'t have enough privileges to access this area!').'</div>');
}

require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_element.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require MDLBS.'reporting/report_dbgrid.inc.php';

$page_title = 'Laporan WNA';
$reportView = false;
$num_recs_show = 20;
if (isset($_GET['reportView'])) {
    $reportView = true;
}

if (!$reportView) {
?>
    <!-- filter -->
<fieldset class="menuBox box box-solid">
  <div class="menuBoxInner systemIcon">
    <div class="per_title box-header with-border">
      <h2>Laporan WNA</h2>
    </div>
    <div class="infoBox">
      <div class="box-body">Filter Pelaporan</div></div>
  </div>

    <div class="sub_section">
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
    <div id="filterForm">
        <div class="form-group divRow">
            <label><?php echo ('Nama / Nomor Paspor / Nomor Ijin Tinggal'); ?></label>
            <?php echo simbio_form_element::textField('text', 'id_name', '', 'class="form-control col-4"'); ?>
        </div>

        <div class="form-group divRow">
            <label><?php echo ('NIM'); ?></label>
            <?php echo simbio_form_element::textField('text', 'nim', '', 'class="form-control col-4"'); ?>
        </div>

        <div class="form-group divRow">
            <label><?php echo ('Alamat / Nomor Telepon'); ?></label>
            <?php echo simbio_form_element::textField('text', 'alamat', '', 'class="form-control col-4"'); ?>
        </div>

        <div class="form-group divRow">
            <label><?php echo ('Kewarganegaraan'); ?></label>
            <?php
            $loc_q = $dbs->query('SELECT id_negara, nama_negara FROM mst_negara');
            $loc_options = array();
            $loc_options[] = array('', ('ALL'));
            while ($loc_d = $loc_q->fetch_row()) {
                $loc_options[] = array($loc_d[0], $loc_d[1]);
            }
            echo simbio_form_element::selectList('id_negara', $loc_options,'','class="select2"');
            ?>
        </div>     

        <div class="form-group divRow">
            <label><?php echo ('Jenis Ijin Tinggal'); ?></label>
            <?php
            $permit_options = [];
            $permit_options[] = array('','SEMUA');
            $permit_options[] = array('1','VISA KUNJUNGAN');
            $permit_options[] = array('2','KITAS');
            $permit_options[] = array('3','KITAP');
            echo simbio_form_element::selectList('permit', $permit_options,'','class="select2"');
            ?>
        </div>     

        <div class="form-group divRow">
            <label><?php echo ('Sponsor'); ?></label>
            <?php
            $sponsor_options = [];
            $sponsor_options[] = array('','SEMUA');
            $sponsor_options[] = array('1','PERORANGAN');
            $sponsor_options[] = array('2','ORGANISASI');
            $sponsor_options[] = array('3','LEMBAGA PENDIDIKAN');
            $sponsor_options[] = array('4','PERUSAHAAN');
            echo simbio_form_element::selectList('sponsor', $sponsor_options,'','class="select2"');
            ?>
        </div>
        
        <div class="form-group divRow">
            <label><?php echo ('Dari Tanggal'); ?></label>
            <?php
            echo simbio_form_element::textField('date', 'startDate', '', 'class="form-control col-4"');
            ?>
        </div>
        <div class="form-group divRow">
            <label><?php echo ('Sampai Tanggal'); ?></label>
            <?php
            echo simbio_form_element::textField('date', 'untilDate', '', 'class="form-control col-4"');
            ?>
        </div>

        <div class="form-group divRow">
            <label><?php echo ('Masa Berlaku Paspor'); ?></label>
            <?php
            $passport_options = [];
            $passport_options[] = array('','SEMUA');
            $passport_options[] = array('1','BERLAKU');
            $passport_options[] = array('2','KADALUWARSA');
            echo simbio_form_element::selectList('tanggal_berakhir_pasport', $passport_options,'','class="select2"');
            ?>
        </div>

        <div class="form-group divRow">
            <label><?php echo ('Masa Berlaku Ijin Tinggal'); ?></label>
            <?php
            $permit_options = [];
            $permit_options[] = array('','SEMUA');
            $permit_options[] = array('1','BERLAKU');
            $permit_options[] = array('2','KADALUWARSA');
            echo simbio_form_element::selectList('permit_expired', $permit_options,'','class="select2"');
            ?>
        </div>

        <!-- <div class="form-group divRow">
            <div class="headerField"><b>ITAS</b></div>
            <label><?php echo ('Dari Tanggal'); ?></label>
            <?php
            echo simbio_form_element::textField('date', 'tgl_awal', '', 'class="form-control col-4"');
            ?>
        <div class="form-group divRow">
            <label><?php echo ('Sampai Tanggal'); ?></label>
            <?php
            echo simbio_form_element::textField('date', 'tgl_akhir', '', 'class="form-control col-4"');
            ?>
        </div> -->

        <div class="form-group divRow">
            <label><?php echo ('Record each page'); ?></label>
            <input type="text" name="recsEachPage" size="3" maxlength="3" class="form-control col-1" value="<?php echo $num_recs_show; ?>" />
            <small class="text-muted"><?php echo ('Set between 20 and 200'); ?></small>
        </div>
    </div>
    <input type="button" class="s-btn btn btn-default" name="moreFilter" value="<?php echo ('Show More Filter Options'); ?>" />
    <input type="submit" class="s-btn btn btn-primary" name="applyFilter" value="<?php echo ('Apply Filter'); ?>" />
    <input type="hidden" name="reportView" value="true" />
    </form>
    </div>
    </fieldset>
    <!-- filter end -->
    <div class="paging-area"><div class="pt-3 pr-3" id="pagingBox"></div></div>
    <iframe name="reportView" id="reportView" src="<?php echo $_SERVER['PHP_SELF'].'?reportView=true'; ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
<?php
} else {
    ob_start();
    // table spec
    $table_spec = 'orang_asing f LEFT JOIN mst_negara mc ON f.id_negara=mc.id_negara
        LEFT JOIN provinsi p ON f.id_provinsi = p.id 
        LEFT JOIN kabupaten r ON r.id=f.id_kabupaten 
        LEFT JOIN kecamatan d ON d.id=f.id_kecamatan
        LEFT JOIN desa v ON v.id=f.id_desa';

    // create datagrid
    $reportgrid = new report_datagrid();
    $reportgrid->table_attr = 'class="s-table table table-sm table-bordered"';

    $reportgrid->setSQLColumn(
        'f.id_orang_asing','f.tanggal_terbit_pasport','f.tanggal_berakhir_pasport',
        'f.tanggal_terbit_ijin_tinggal', 'tanggal_berakhir_ijin_tinggal',
        'f.nama_mhs AS \''.('NAMA LENGKAP').'\'',
        'mc.nama_negara AS \''.('WN').'\'',
        'f.no_pasport AS \''.('PASPOR').'\'',
        'f.no_ijin_tinggal AS \''.('IJIN TINGGAL').'\'',
        'jenis_ijin_tinggal',
        'f.id_orang_asing AS \''.('TELEPON / ALAMAT DOMISILI').'\'');

    $reportgrid->invisible_fields = array(0,1,2,3,4,9);
    $reportgrid->setSQLorder('f.id_orang_asing DESC');

    $criteria = 'f.id_orang_asing IS NOT NULL ';
    if (isset($_GET['id_name']) AND !empty($_GET['id_name'])) {
        $id_name = utility::filterData('id_name', 'get', true, true, true);
        $criteria .= ' AND (f.no_pasport LIKE \'%'.$id_name.'%\' OR f.nama_mhs LIKE \'%'.$id_name.'%\')';
    }

    if (isset($_GET['alamat']) AND !empty($_GET['alamat'])) {
        $address = utility::filterData('alamat', 'get', true, true, true);
        $criteria .= ' AND (
        f.alamat LIKE \'%'.$address.'%\' 
        OR f.nomor_telepon LIKE \'%'.$address.'%\'
        OR f.email LIKE \'%'.$address.'%\'
        OR v.name LIKE \'%'.$address.'%\'
        OR d.name LIKE \'%'.$address.'%\'
        OR r.name LIKE \'%'.$address.'%\'
        OR p.name LIKE \'%'.$address.'%\'
    )';
    }

    if (isset($_GET['startDate']) AND ($_GET['untilDate'])) {
        $criteria .= ' AND(TO_DAYS(f.tanggal_berakhir_pasport) > TO_DAYS(\''.utility::filterData        ('startDate', 'get', true, true, true).'\') AND TO_DAYS(f.tanggal_berakhir_pasport) <= TO_DAYS(\''.utility::filterData('untilDate', 'get' , true, true, true).'\')) OR (TO_DAYS(f.tanggal_berakhir_ijin_tinggal) > TO_DAYS(\''.utility::filterData('startDate', 'get', true, true, true).'\') AND TO_DAYS(f.tanggal_berakhir_ijin_tinggal) <= TO_DAYS(\''.utility::filterData('untilDate', 'get' , true, true, true).'\'))';
    } 
    // if (isset($_GET['tgl_awal']) AND ($_GET['tgl_akhir'])) {
    //     $criteria .= ' AND (TO_DAYS(f.tanggal_berakhir_ijin_tinggal) BETWEEN TO_DAYS(\''.utility::filterData('tgl_awal', 'get', true, true, true).'\') AND
    //         TO_DAYS(\''.utility::filterData('tgl_akhir', 'get', true, true, true).'\'))';
    // }


    if (isset($_GET['tanggal_berakhir_pasport']) AND !empty($_GET['tanggal_berakhir_pasport'])) {
        $passport_expired = utility::filterData('tanggal_berakhir_pasport', 'get', true, true, true);
        if($passport_expired == '1'){
        $criteria .= ' AND f.tanggal_berakhir_pasport >= \''.date("Y-m-d").'\'';
        }else{
        $criteria .= ' AND f.tanggal_berakhir_pasport < \''.date("Y-m-d").'\'';
        }
    }

    if (isset($_GET['sponsor']) AND !empty($_GET['sponsor'])) {
        $sponsor = utility::filterData('sponsor', 'get', true, true, true);
        $criteria .= ' AND f.jenis_sponsor = \''.$sponsor.'\'';
    }

    if (isset($_GET['permit']) AND !empty($_GET['permit'])) {
        $permit = utility::filterData('permit', 'get', true, true, true);
        $criteria .= ' AND f.jenis_ijin_tinggal = \''.$permit.'\'';
    }

    if (isset($_GET['permit_expired']) AND !empty($_GET['permit_expired'])) {
        $permit_expired = utility::filterData('permit_expired', 'get', true, true, true);
        if($permit_expired == '2'){
        $criteria .= ' AND f.tanggal_berakhir_ijin_tinggal < \''.date("Y-m-d").'\'';
        }else{
        $criteria .= ' AND f.tanggal_berakhir_ijin_tinggal  >= \''.date("Y-m-d").'\'';
        }
    }
  
    if (isset($_GET['id_negara']) AND !empty($_GET['id_negara'])) {
        $citizen_id = utility::filterData('id_negara', 'get', true, true, true);
        $criteria .= ' AND f.id_negara = \''.$citizen_id.'\'';
    }
    
    if (isset($_GET['recsEachPage'])) {
        $recsEachPage = (integer)utility::filterData('recsEachPage', 'get', true, true, true);
        $num_recs_show = ($recsEachPage >= 20 && $recsEachPage <= 200)?$recsEachPage:$num_recs_show;
    }
    $reportgrid->setSQLCriteria($criteria);

   // callback function to show loan status
    $reportgrid->modifyColumnContent(7, 'callback{passport_detail}');
    function passport_detail($obj_db, $array_data)
    {   
        $str = $array_data[7].'&nbsp;';
        if($array_data[2] < date("Y-m-d")){
            $str .= '<span class="badge bg-red">expired</span>';      
        }
        $str .= '<br/><small>berlaku : <i>'.date_format(date_create($array_data[1]),"d-M-y").'</i>  sd.  <i>'.date_format(date_create($array_data[2]),"d-M-y").'</i></small>';
         return $str;
    }

   // callback function to show loan status
    $reportgrid->modifyColumnContent(8, 'callback{permit_detail}');
    function permit_detail($obj_db, $array_data)
    {   
        $str = $array_data[8].'&nbsp;';
        if($array_data[4] < date("Y-m-d")){
            $str .= '<span class="badge bg-red">expired</span>';      
        }
        $str .= '<br/><small>berlaku : <i>'.date_format(date_create($array_data[3]),"d-M-y").'</i>  sd.  <i>'.date_format(date_create($array_data[4]),"d-M-y").'</i></small>';
        $str .= '<br/>Jenis Ijin : <b>';
        switch ($array_data[9]) {
            case '1':
                $str .= 'VISA KUNJUNGAN';
                break;
            case '2':
                $str .= 'KITAS';
                break;
            case '3':
                $str .= 'KITAP';
                break;
        }
        $str .= '</b>';
         return $str;
    }

   // callback function to show loan status
    $reportgrid->modifyColumnContent(10, 'callback{alamat_detail}');
    function alamat_detail($obj_db, $array_data)
    {   $_q = $obj_db->query("SELECT f.nomor_telepon,f.email,f.alamat,v.nama, d.nama,  r.nama, p.nama FROM orang_asing f 
        LEFT JOIN provinsi p ON f.id_provinsi = p.id 
        LEFT JOIN kabupaten r ON r.id=f.id_kabupaten 
        LEFT JOIN kecamatan d ON d.id=f.id_kecamatan
        LEFT JOIN desa v ON v.id=f.id_desa 
        WHERE f.id_orang_asing=".$array_data[10]);
        $_d = $_q->fetch_row();
        $str  = '<small>';
        $str .= 'Telp. : '.$_d[0].'<br/>';
        $str .= 'Email : '.$_d[1].'<br/>';
        $str .= 'Alamat : '.$_d[2].'<br/>';
        $str .= 'DESA/KEL. '.$_d[3].'<br/>KEC. '.$_d[4].'<br/>'.$_d[5].'<br/>PROV. '.$_d[6];
        $str .= '</small>';
        return $str;
    }    
    // modify column value

    //echo $criteria. json_encode($_GET);
    // show spreadsheet export button
    $reportgrid->show_spreadsheet_export = true;

    // put the result into variables
    echo $reportgrid->createDataGrid($dbs, $table_spec, 20);

    echo '<script type="text/javascript">'."\n";
    echo 'parent.$(\'#pagingBox\').html(\''.str_replace(array("\n", "\r", "\t"), '', $reportgrid->paging_set).'\');'."\n";
    echo '</script>';
    $xlsquery = 'SELECT f.nama_mhs as \'NAMA LENGKAP\',
    mc.nama_negara as \'WARGA NEGARA\',
    f.tempat_lahir as \'TEMPAT LAHIR\',
    f.tanggal_lahir as \'TANGGAL LAHIR\',
    IF(f.jenis_kelamin=0,\'LAKI-LAKI\',\'PEREMPUAN\') as \'JENIS KELAMIN\',
    f.nomor_telepon as \'NOMOR TELEPON\',
    f.email as \'EMAIL\',
    f.alamat as \'ALAMAT\',
    v.nama as \'DESA/KELURAHAN\',
    d.nama as \'KECAMATAN\',
    r.nama as \'KABUPATEN\',
    p.nama as \'PROVINSI\',
    f.nama_orangtua as \'NAMA KELUARGA\',
    f.alamat_orangtua as \'ALAMAT KELUARGA\',
    f.kontak_telepon as \'TELEPON KELUARGA\', 
    f.kontak_email as \'EMAIL KELUARGA\',  
    f.no_pasport as \'NOMOR PASPOR\',
    f.tanggal_terbit_pasport as \'PASPOR DITERBITKAN\',
    f.tanggal_berakhir_pasport as \'PASPOR BERLAKU\',
    f.no_ijin_tinggal as \'NOMOR IJIN TINGGAL\',
    IF(f.jenis_ijin_tinggal=1,\'VISA KUNJUNGAN\',IF(f.jenis_ijin_tinggal=2,\'KITAS\',\'KITAP\')) as \'JENIS IJIN TINGGAL\',
    f.tanggal_terbit_ijin_tinggal as \'IJIN TINGGAL DITERBITKAN\',
    f.tanggal_berakhir_ijin_tinggal as \'IJIN TINGGAL BERLAKU\',
    f.nama_sponsor as \'NAMA SPONSOR\',
    IF(f.jenis_sponsor=1,\'PERORANGAN\',IF(f.jenis_sponsor=2,\'ORGANISASI\',IF(f.jenis_sponsor=3,\'LEMBAGA PENDIDIKAN\',\'ORGANISASI\'))) as \'JENIS SPONSOR\',
    f.alamat_sponsor as \'ALAMAT SPONSOR\'
     FROM '.$table_spec.' WHERE '.$criteria;

        unset($_SESSION['xlsdata']);
        $_SESSION['xlsquery'] = $xlsquery;
        $_SESSION['tblout'] = "laporan_wna_".date("YmdHis");

    $content = ob_get_clean();
    // include the page template
    require SB.'/admin/'.$sysconf['admin_template']['dir'].'/printed_page_tpl.php';
}
?>
