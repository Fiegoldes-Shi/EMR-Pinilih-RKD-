<?php
// === BASEURL DINAMIS (tanpa hardcode localhost) ===
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'];
$path     = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'); // ambil parent folder (../)
$baseurl  = $protocol . $host . $path;


?>

<?php
// insert
if (!empty($_POST["savebtn"])) {
    $linkurl = 25;

    $idSubDisabilitas = $_POST["subDisabilitas"];
    $datafield_peserta = array("nama", "asalLembaga", "jenisKelamin", "usia", "alamat", "idSubDisabilitas");
    $datavalue_peserta = array($_POST["nama"], $_POST["asalLembaga"] === '' ? "" : $_POST["asalLembaga"], $_POST["jenisKelamin"], $_POST["usia"], $_POST["alamat"] === '' ? "" : $_POST["alamat"], $idSubDisabilitas === '' ? NULL : $idSubDisabilitas);

    $insert = new cInsert();
    // Gunakan vInsertDataPrepared
    $insert->vInsertDataPrepared("peserta", $datafield_peserta, $datavalue_peserta);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = 25;

    $idSubDisabilitas = !empty($_POST["subDisabilitas"]) ? (is_array($_POST["subDisabilitas"]) ? implode(",", $_POST["subDisabilitas"]) : $_POST["subDisabilitas"]) : null;

    $datafield_peserta = array("nama", "asalLembaga", "jenisKelamin", "usia", "alamat", "idSubDisabilitas");
    $datavalue_peserta = array($_POST["nama"], $_POST["asalLembaga"] === '' ? "" : $_POST["asalLembaga"], $_POST["jenisKelamin"], $_POST["usia"], $_POST["alamat"] === '' ? "" : $_POST["alamat"], $idSubDisabilitas === '' ? NULL : $idSubDisabilitas);

    $whereCol = "idPeserta";
    $whereVal = $_POST["idPeserta"];

    $update = new cUpdate();
    // Gunakan vUpdateDataPrepared
    $update->vUpdateDataPrepared("peserta", $datafield_peserta, $datavalue_peserta, $whereCol, $whereVal);
}
?>

<?php
// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    foreach ($_POST["hiddendeletevalue"] as $data) {
        // $delete->_dDeleteDataTrial($data["field"], $data["value"], $data["table"]);
        $delete->vDeleteDataPrepared($data["table"], $data["field"], $data["value"]);        
    }
}
?>

<?php
    // Query ENUM 'kelompokUsia'
    $usia = "SHOW COLUMNS FROM peserta LIKE 'usia'";
    $view = new cView();
    $arrayUsia = $view->vViewData($usia);
    $enumKelompokUsia = [];
    if (!empty($arrayUsia)) {
        $row = $arrayUsia[0]; // Ambil hasil pertama
        if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
            $enumKelompokUsia = explode(",", str_replace("'", "", $matches[1]));
        }
    }

    // Query ENUM 'jenisKelamin'
    $jk = "SHOW COLUMNS FROM peserta LIKE 'jenisKelamin'";
    $view = new cView();
    $arrayJK = $view->vViewData($jk);
    $enumJK = [];
    if (!empty($arrayJK)) {
        $row = $arrayJK[0]; // Ambil hasil pertama
        if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
            $enumJK = explode(",", str_replace("'", "", $matches[1]));
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Peserta</title>
    
    <script src="../admin/js/jquery.min.js" type="text/javascript"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-11">
                <figure>
                    <blockquote class="blockquote"><p>DATA PESERTA</p></blockquote>
                    <figcaption class="blockquote-footer">Entri Data Peserta</figcaption>
                </figure>
            </div>

            <div class="col-12 col-md-2 col-lg-1 mb-3">
                <button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center" 
                        data-bs-toggle="modal" data-bs-target="#exampleModal" 
                        style="border-radius: 10px; width: 100%; height: 60%; display: flex;">
                    <i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>
                </button>

                <!-- Modal Insert -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title fs-5" id="exampleModalLabel">
                                    <blockquote class="blockquote">
                                        <p>Tambah Data Peserta</p>
                                    </blockquote>
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form class="" method="post" action="25" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nama">Nama Lengkap Peserta <span class="required">*</span></label>
                                        <input class="form-control" type="text" name="nama" id="nama" value="" placeholder="Nama lengkap peserta" maxlength="255" size="" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="asalLembaga">Asal Lembaga</label>
                                        <input class="form-control" type="text" name="asalLembaga" id="asalLembaga" value="" placeholder="Asal Lembaga/Instansi" maxlength="255" size="">
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="jenisKelamin">Jenis Kelamin <span class="required">*</span></label>
                                            <select name="jenisKelamin" class="form-control" required>
                                                <option value="">- pilihan -</option>
                                                <?php
                                                    foreach ($enumJK as $option) {
                                                        $trimmedValue = trim($option);
                                                        echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="usia">Kelompok Usia <span class="required">*</span></label>
                                            <select name="usia" class="form-control" required>
                                                <option value="">- pilihan -</option>
                                                <?php
                                                    foreach ($enumKelompokUsia as $option) {
                                                        $trimmedValue = trim($option);
                                                        echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamat">Alamat Peserta</label>
                                        <input class="form-control" type="text" name="alamat" id="alamat" value="" placeholder="Alamat peserta" maxlength="255" size="">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="jenisDisabilitas">Jenis Disabilitas</label>
                                            <select name="jenisDisabilitas" id="jenisDisabilitasPeserta" class="form-control">
                                                <option value="">- pilihan -</option>
                                                
                                                <?php                                
                                                // Buat query untuk menampilkan semua data siswa
                                                $sql = "SELECT * FROM jenis_disabilitas ORDER BY idJenisDisabilitas";
                                                $view = new cView();
                                                $dataJD = $view->vViewData($sql);
                                                
                                                foreach($dataJD as $data){ 
                                                    echo "<option value='".$data['idJenisDisabilitas']."'>".$data['jenisDisabilitas']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="subDisabilitas">Sub Jenis Disabilitas <span id="reqSubDisPeserta" class="required" style="display:none;">*</span></label>
                                            <select name="subDisabilitas" id="subDisabilitasPeserta" class="form-control">
                                                <option value="">- pilihan -</option>
                                            </select>
                                        </div>
                                    </div>
                                    <script>
                                        document.getElementById('jenisDisabilitasPeserta').addEventListener('change', function() {
                                            var val = this.value;
                                            var subSelect = document.getElementById('subDisabilitasPeserta');
                                            var reqSpan = document.getElementById('reqSubDisPeserta');
                                            if(val && val.trim() !== "") {
                                                subSelect.setAttribute('required', 'required');
                                                reqSpan.style.display = 'inline-block';
                                            } else {
                                                subSelect.removeAttribute('required');
                                                reqSpan.style.display = 'none';
                                            }
                                        });
                                    </script>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;" name="savebtn" value="true">Simpan</button>
                                    <button type="reset" class="btn btn-warning btn-sm" style="border-radius: 25px;" name="" value="true">Ulang</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">Tutup</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                <div id="" class='table-responsive'>
                    <table id="tablePeserta" class="table table-condensed">
                        <thead>
                            <tr class=''>
                                <th width='5%'>No.</th>
                                <th width=''>Nama</th>
                                <th width=''>Asal Lembaga</th>
                                <th width=''>Usia</th>
                                <th width=''>Jenis Kelamin</th>
                                <th width=''>Alamat Domisili</th>
                                <th width=''>Disabilitas</th>
                                <th width='5%'>VIEW</th>
                                <th width='5%'>EDIT</th>
                                <th width='5%'>HAPUS</th>
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
</body>
</html>

<!-- Container for dynamic modals -->
<div id="dynamic-modal-container"></div>

<script>
    $(document).ready(function () {
        var table = $('#tablePeserta').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax/get_peserta.php",
                "type": "POST"
            },
            "columns": [
                { "data": "0" },
                { "data": "1" },
                { "data": "2" },
                { "data": "3" },
                { "data": "4" },
                { "data": "5" },
                { "data": "6" },
                { "data": "7", "orderable": false, "searchable": false },
                { "data": "8", "orderable": false, "searchable": false },
                { "data": "9", "orderable": false, "searchable": false }
            ]
        });

        $('#tablePeserta').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_peserta.php',
                type: 'POST',
                data: { id: id, action: 'view' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalView'));
                    myModal.show();
                }
            });
        });

        $('#tablePeserta').on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_peserta.php',
                type: 'POST',
                data: { id: id, action: 'edit' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    myModal.show();
                }
            });
        });

        $('#tablePeserta').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_peserta.php',
                type: 'POST',
                data: { id: id, action: 'delete' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalDelete'));
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