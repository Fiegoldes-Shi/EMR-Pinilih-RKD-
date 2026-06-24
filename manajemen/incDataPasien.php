<?php
// Query ENUM 'kelompokUsia'
$usia = "SHOW COLUMNS FROM pasien LIKE 'kelompokUsia'";
$view = new cView();
$arrayUsia = $view->vViewData($usia);
$enumKelompokUsia = [];
if (!empty($arrayUsia)) {
    $row = $arrayUsia[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumKelompokUsia = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'jenisKelamin'
$jk = "SHOW COLUMNS FROM pasien LIKE 'jenisKelamin'";
$view = new cView();
$arrayJK = $view->vViewData($jk);
$enumJK = [];
if (!empty($arrayJK)) {
    $row = $arrayJK[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumJK = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'golonganDarah'
$goldar = "SHOW COLUMNS FROM pasien LIKE 'golonganDarah'";
$view = new cView();
$arrayGoldar = $view->vViewData($goldar);
$enumGoldar = [];
if (!empty($arrayGoldar)) {
    $row = $arrayGoldar[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumGoldar = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'agama'
$agama = "SHOW COLUMNS FROM pasien LIKE 'agama'";
$view = new cView();
$arrayAgama = $view->vViewData($agama);
$enumAgama = [];
if (!empty($arrayAgama)) {
    $row = $arrayAgama[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumAgama = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'pendidikan'
$pendidikan = "SHOW COLUMNS FROM pasien LIKE 'pendidikan'";
$view = new cView();
$arrayPendidikan = $view->vViewData($pendidikan);
$enumPendidikan = [];
if (!empty($arrayPendidikan)) {
    $row = $arrayPendidikan[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumPendidikan = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'pekerjaan'
$pekerjaan = "SHOW COLUMNS FROM pasien LIKE 'pekerjaan'";
$view = new cView();
$arrayPekerjaan = $view->vViewData($pekerjaan);
$enumPekerjaan = [];
if (!empty($arrayPekerjaan)) {
    $row = $arrayPekerjaan[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumPekerjaan = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'statusPernikahan'
$statusPernikahan = "SHOW COLUMNS FROM pasien LIKE 'statusPernikahan'";
$view = new cView();
$arrayStatusNikah = $view->vViewData($statusPernikahan);
$enumNikah = [];
if (!empty($arrayStatusNikah)) {
    $row = $arrayStatusNikah[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumNikah = explode(",", str_replace("'", "", $matches[1]));
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-10 col-lg-11">
            <figure>
                <blockquote class="blockquote">
                    <p>DATA PASIEN</p>
                </blockquote>
                <figcaption class="blockquote-footer">Entri Data Pasien</figcaption>
            </figure>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class='table-responsive'>
                    <table id="tablePasien" class="table table-condensed">
                        <thead>
                            <tr class=''>
                                <th width='5%'>No.</th>
                                <th width='17%'>Nama Pasien</th>
                                <th width='18%'>Usia</th>
                                <th width='15%'>Jenis Kelamin</th>
                                <th width='15%'>Kelurahan Domisili</th>
                                <th width=''>Disabilitas</th>
                                <th width='5%'>VIEW</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Container for dynamic modals -->
<div id="dynamic-modal-container"></div>

<script>
    $(document).ready(function () {
        var table = $('#tablePasien').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax/get_pasien.php",
                "type": "POST"
            },
            "columns": [
                { "data": "0" },
                { "data": "1" },
                { "data": "2" },
                { "data": "3" },
                { "data": "4" },
                { "data": "5" },
                { "data": "6", "orderable": false, "searchable": false }
            ]
        });

        $('#tablePasien').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_pasien.php',
                type: 'POST',
                data: { id: id },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalView'));
                    myModal.show();
                }
            });
        });
    });
</script>

<p></p>
<div class="row">
    <div class="col-md-12">
        <p><br><br><br><br><br></p>
    </div>
</div>
