<?php
/**
 * Collection general report
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com
 *
 * Copyright (C) 2008 Arie Nugraha (dicarve@yahoo.com)
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

/* Reporting section */


// key to authentication
define('INDEX_AUTH', '1');

if (!defined('SB')) {
    // main system configuration
    require '../../../sysconfig.inc.php';
    // start the session
    require SB.'admin/default/session.inc.php';
}

// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');

require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';

// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.('You don\'t have enough privileges to access this area!').'</div>');
}

/* collection statistic */
$table = new simbio_table();
$table->table_attr = 'class="s-table table table-bordered mb-0"';

// total number of titles
$stat_query = $dbs->query('SELECT COUNT(id_orang_asing) FROM orang_asing');
$stat_data = $stat_query->fetch_row();
$result_stat[('Total WNA')] = $stat_data[0].' '.(' termasuk dengan paspor dan surat ijin kadaluwarsa');


// total number of titles
$stat_query = $dbs->query("SELECT id_orang_asing FROM orang_asing WHERE tanggal_berakhir_pasport >= '".date("Y-m-d")."'");
$stat_data = $stat_query->num_rows;
$result_stat[('Paspor Aktif')] = $stat_data.(' paspor aktif');

// total number of titles
$stat_query = $dbs->query("SELECT id_orang_asing FROM orang_asing WHERE tanggal_berakhir_pasport < '".date("Y-m-d")."'");
$stat_data = $stat_query->num_rows;
$result_stat[('Paspor Kadaluwarsa')] = $stat_data.(' paspor kadaluwarsa');


// total number of titles
$stat_query = $dbs->query("SELECT id_orang_asing FROM orang_asing WHERE tanggal_berakhir_ijin_tinggal >= '".date("Y-m-d")."'");
$stat_data = $stat_query->num_rows;
$result_stat[('Ijin Tinggal Aktif')] = $stat_data.(' ijin tinggal aktif');

// total number of titles
$stat_query = $dbs->query("SELECT id_orang_asing FROM orang_asing WHERE tanggal_berakhir_ijin_tinggal < '".date("Y-m-d")."'");
$stat_data = $stat_query->num_rows;
$result_stat[('Ijin Tinggal Kadaluwarsa')] = $stat_data.(' ijin tinggal kadaluwarsa');


// total items by Collection Type
$stat_query = $dbs->query("SELECT 
CASE
    WHEN jenis_ijin_tinggal=1 THEN 'VISA KUNJUNGAN'
    WHEN jenis_ijin_tinggal=2 THEN 'KITAS'
    WHEN jenis_ijin_tinggal=3 THEN 'KITAP'
    ELSE ''
END,count(jenis_ijin_tinggal) FROM orang_asing  GROUP BY jenis_ijin_tinggal");

$stat_data = '';
while ($data = $stat_query->fetch_row()) {
    $stat_data .= $data[0].' (<strong>'.$data[1].'</strong>)'.'<br/>';
}
$stat_data = substr($stat_data,0,-1);
$result_stat[('Total WNA berdasarkan Ijin Tinggal')] = $stat_data;


$stat_query = $dbs->query("SELECT 
CASE
    WHEN jenis_sponsor=1 THEN 'PERORANGAN'
    WHEN jenis_sponsor=2 THEN 'ORGANISASI'
    WHEN jenis_sponsor=3 THEN 'LEMBAGA PENDIDIKAN'
    WHEN jenis_sponsor=4 THEN 'PERUSAHAAN'
    ELSE ''
END,count(jenis_sponsor) FROM orang_asing  GROUP BY jenis_sponsor");

$stat_data = '';
while ($data = $stat_query->fetch_row()) {
    $stat_data .= $data[0].' (<strong>'.$data[1].'</strong>)'.'<br/>';
}
$stat_data = substr($stat_data,0,-1);
$result_stat[('Total WNA berdasarkan Sponsor')] = $stat_data;


$stat_query = $dbs->query("SELECT mc.nama_negara, count(mc.id_negara) FROM orang_asing f LEFT JOIN mst_negara mc ON f.id_negara = mc.id_negara GROUP BY f.id_negara");
$stat_data = '';
while ($data = $stat_query->fetch_row()) {
    $stat_data .= $data[0].' (<strong>'.$data[1].'</strong>)'.'<br/>';
}
$stat_data = substr($stat_data,0,-1);
$result_stat[('Total WNA berdasarkan Kewarganedaraan')] = $stat_data;



$stat_query = $dbs->query("SELECT p.nama, count(p.id), f.id_kabupaten FROM orang_asing f LEFT JOIN provinsi p ON p.id = f.id_provinsi GROUP BY p.id");
$stat_data = '';
while ($data = $stat_query->fetch_row()) {
    $_q = "SELECT r.nama, count(r.id) 
FROM orang_asing f
LEFT JOIN provinsi p ON p.id=f.id_provinsi
LEFT JOIN kabupaten r ON p.id = r.id_provinsi 
WHERE r.id=".$data[2]." GROUP BY r.id";
    $st_query = $dbs->query($_q);
        $str = '';
        $n = 1;
        while($dt = $st_query->fetch_row()){
            $str .= '&nbsp;'.$n.'.  '.ucwords($dt[0]).' (<strong>'.$dt[1].'</strong>)'.'<br/>';
            $n++;
        }

    $stat_data .= '<strong><u>'.$data[0].'</u> ('.$data[1].'</strong>)'.'<br/>'.$str;



}
$stat_data = substr($stat_data,0,-1);
$result_stat[('Total WNA berdasarkan Provinsi Domisili')] = $stat_data;

// table header
$table->setHeader(array(('Ringkasan Statistik WNA')));
$table->table_header_attr = 'class="dataListHeader"';
$table->setCellAttr(0, 0, 'colspan="2"');
// initial row count
$row = 1;
foreach ($result_stat as $headings=>$stat_data) {
    $table->appendTableRow(array('<b>'.$headings.'</b>', $stat_data));
    // set cell attribute
    $table->setCellAttr($row, 0, 'class="alterCell" valign="top" style="width: 300px;"');
    $table->setCellAttr($row, 1, 'class="alterCell" valign="top" style="width: auto;"');
    // add row count
    $row++;
}

// if we are in print mode
$page_title = ('Laporan WNA');
if (isset($_GET['print'])) {
    // load print template
    require_once SB.'admin/admin_template/printed.tpl.php';
    // write to file
    $file_write = @file_put_contents(REPBS.'biblio_stat_print_result.html', $html_str);
    if ($file_write) {
        // open result in new window
        echo '<script type="text/javascript">
        top.$.colorbox({
            href: "'.SWB.FLS.'/'.REP.'/biblio_stat_print_result.html", 
            height: 500,  
            width: 800,
            iframe : true,
            fastIframe: false,
            title: function(){return "'.$page_title.'";}
        })
        </script>';
    } else { utility::jsAlert(str_replace('{directory}', REPBS, ('ERROR! Collection Statistic Report failed to generate, possibly because {directory} directory is not writable'))); }
    exit();
}

?>
<div class="menuBox">
    <div class="menuBoxInner statisticIcon">
        <div class="per_title">
        <h2><?php echo ('Statistik WNA'); ?></h2>
    </div>
    <div class="infoBox">
        <form name="printForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="submitPrint" id="printForm" class="notAJAX" method="get">
            <input type="hidden" name="print" value="true" />
        </form>
    </div>
    <iframe name="submitPrint" style="display: none; visibility: hidden; width: 0; height: 0;"></iframe>
    </div>
</div>
<?php
echo $table->printTable();
/* collection statistic end */
