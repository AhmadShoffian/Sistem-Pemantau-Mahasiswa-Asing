<?php

// key to authenticate
if (!defined('INDEX_AUTH')) {
    define('INDEX_AUTH', '1');
}

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    include_once '../../sysconfig.inc.php';
}


$a = $dbs->query("SELECT id_orang_asing FROM orang_asing");
$foreigner_count = $a->num_rows;


// INNER JOIN sponsor ON orang_asing.id_sponsor=sponsor.id_sponsor GROUP BY jenis_sponsor";
$query = "SELECT id_orang_asing FROM orang_asing INNER JOIN paspor ON orang_asing.id_pasport=paspor.id_pasport WHERE tanggal_berakhir_pasport > '".date("Y-m-d")."'";
$b = $dbs->query($query);
$passport_expired = $b->num_rows;


$query = "SELECT id_orang_asing FROM orang_asing INNER JOIN ijin_tinggal ON orang_asing.id_ijin_tinggal=ijin_tinggal.id_ijin_tinggal WHERE tanggal_berakhir_ijin_tinggal > '".date("Y-m-d")."'";
$c = $dbs->query($query);
$residence_permit_expired = $c->num_rows;


//jenis kerjasama
function rand_color() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

$query =  "SELECT mc.nama_negara, count(mc.id_negara) FROM orang_asing f LEFT JOIN mst_negara mc ON f.id_negara = mc.id_negara GROUP BY f.id_negara";
$d = $dbs->query($query);
$label = '';
$val = '';
$clr  = '';
$c = 0;
while($data = $d->fetch_row()){
  $label .= '"'.$data[0].'",';
  $val .= $data[1].',';
  $clr .= '"'.rand_color().'",';
  $c = $c+60;
}

$query =  "SELECT p.nama, count(p.id) FROM orang_asing f LEFT JOIN provinsi p ON p.id = f.id_provinsi GROUP BY p.id";
$d = $dbs->query($query);
$label1 = '';
$val1 = '';
$clr1  = '';
$p = 0;
while($data = $d->fetch_row()){
  $label1 .= '"'.$data[0].'",';
  $val1 .= $data[1].',';
  $clr1 .= '"'.rand_color().'",';
  $p = $p+60;
}


$query =  "SELECT 
CASE
    WHEN jenis_ijin_tinggal=1 THEN 'VISA KUNJUNGAN'
    WHEN jenis_ijin_tinggal=2 THEN 'KITAS'
    WHEN jenis_ijin_tinggal=3 THEN 'KITAP'
    ELSE ''
END,count(jenis_ijin_tinggal) FROM orang_asing INNER JOIN ijin_tinggal ON orang_asing.id_ijin_tinggal=ijin_tinggal.id_ijin_tinggal GROUP BY jenis_ijin_tinggal";
$d = $dbs->query($query);
$label2 = '';
$val2 = '';
$clr2  = '';
while($data1 = $d->fetch_row()){
  $label2 .= '"'.$data1[0].'",';
  $val2 .= $data1[1].',';
  $clr2 .= '"'.rand_color().'",';
}

$query =  "SELECT 
CASE
    WHEN jenis_sponsor=1 THEN 'PERORANGAN'
    WHEN jenis_sponsor=2 THEN 'ORGANISASI'
    WHEN jenis_sponsor=3 THEN 'LEMBAGA PENDIDIKAN'
    WHEN jenis_sponsor=4 THEN 'PERUSAHAAN'
    ELSE ''
END,count(jenis_sponsor) FROM orang_asing INNER JOIN sponsor ON orang_asing.id_sponsor=sponsor.id_sponsor GROUP BY jenis_sponsor";
$d = $dbs->query($query);
$label3 = '';
$val3 = '';
$clr3  = '';
while($data = $d->fetch_row()){
  $label3 .= '"'.$data[0].'",';
  $val3 .= $data[1].',';
  $clr3 .= '"'.rand_color().'",';
}

?>
<section class="content-header">
      <h1>
        <ion-icon name="finger-print-outline"></ion-icon>
SIPANTAU WNA
        <small>sistem pmantauan warga negara asing v.1.0.0</small>
      </h1>
      <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

<section class="content" style="min-height: auto;">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Jumlah Warga Asing</span>
              <span class="info-box-number"><h3><?php echo $foreigner_count; ?></h3></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-bookmark-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Paspor Aktif</span>
              <span class="info-box-number"><h3><?php echo $passport_expired; ?></h3></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-files-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Ijin Tinggal Aktif</span>
              <span class="info-box-number"><h3><?php echo $residence_permit_expired; ?></h3></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

      </div>
      <!-- /.row -->



</section>
<section class="content">

    <div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- Info boxes -->
          <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Jumlah Menurut Jenis Ijin Tinggal</h3>
              </div>
              <div class="box-body">
                <div class="chart">
                  <canvas id="canvas2"  height="300" width="510"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
          </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Jumlah Menurut Sponsor</h3>
              </div>
              <div class="box-body">
                  <canvas id="canvas3" height="300" width="510"></canvas>
              </div>
              <!-- /.box-body -->
            </div>
          </div>
          </div>


  <div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- Info boxes -->
          <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Jumlah Menurut Kewarganegaraan</h3>
              </div>
              <div class="box-body">
                <div class="chart">
                  <canvas id="canvas"  height="<?=$c?>" width="510"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
          </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Jumlah Menurut Wilayah Provinsi</h3>
              </div>
              <div class="box-body">
                  <canvas id="canvas1" height="<?=$p?>" width="510"></canvas>
              </div>
              <!-- /.box-body -->
            </div>
          </div>
          </div>      




</section>

<script>
new Chart("canvas2", {
  type: "doughnut",
  data: {
    labels: [<?=$label2?>],
    datasets: [{
      backgroundColor: [<?=$clr2?>],
      data: [<?=$val2?>]
    }]
  },
  options: {
    title: {
      display: false,
    }
  }
});

new Chart("canvas3", {
  type: "doughnut",
  data: {
    labels: [<?=$label3?>],
    datasets: [{
      backgroundColor: [<?=$clr3?>],
      data: [<?=$val3?>]
    }]
  },
  options: {
    title: {
      display: false,
    }
  }
});

new Chart("canvas", {
  type: "horizontalBar",

  data: {
    labels: [<?=$label?>],
    datasets: [{
      backgroundColor: [<?=$clr?>],
      data: [<?= $val?>]
    }]
  },

  options: {
    indexAxis: 'y',
    legend: {display: false},
        scales: {
        xAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
    }
  }
});

new Chart("canvas1", {
  type: "horizontalBar",

  data: {
    labels: [<?=$label1?>],
    datasets: [{
      backgroundColor: [<?=$clr1?>],
      data: [<?= $val1?>]
    }]
  },

  options: {
    indexAxis: 'y',
    legend: {display: false},
        scales: {
        xAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
    }
  }
});
</script>