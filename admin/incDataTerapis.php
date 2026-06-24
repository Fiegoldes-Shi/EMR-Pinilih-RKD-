<?php
// insert
if (!empty($_POST["savebtn"])) {
    $linkurl = 22;

    // Mapping ENUM dari Database ke Label Tampilan
    $enumJTMapping = [
        "Tenaga Medis" => "Tenaga Medis - Dokter, Psikiater",
        "Tenaga Paramedis" => "Tenaga Paramedis - Perawat",
        "Tenaga Non Medis" => "Tenaga Non Medis - Psikolog, Terapis, Volunteer, dll"
    ];

    // Mencari nilai $_POST["jenisTerapis"] dalam array $enumJTMapping, lalu mengembalikan key-nya (nilai ENUM)
    $jenisTerapis = array_search($_POST["jenisTerapis"], $enumJTMapping);
    if ($jenisTerapis === false) {
        $jenisTerapis = $_POST["jenisTerapis"]; // Gunakan langsung jika tidak ditemukan
    }

    //Upload File
    $targetDir = "uploads/sertifikasi/"; // Folder penyimpanan file
    $fileName = basename((string) $_FILES["dokumenSertifikasi"]["name"]);
    $filePath = $targetDir . time() . "_" . $fileName; // Buat nama unik

    if (!empty($_FILES["dokumenSertifikasi"]["tmp_name"])) {
        if (move_uploaded_file($_FILES["dokumenSertifikasi"]["tmp_name"], $filePath)) {
            $dokumenSertifikasi = $filePath; // No quotes
        } else {
            $dokumenSertifikasi = null; // PHP null for DB NULL
        }
    } else {
        $dokumenSertifikasi = null;
    }

    // Upload File dokumenLainnya
    $targetDir = "uploads/dokumenLain/";
    $fileNameLainnya = basename((string) $_FILES["dokumenLainnya"]["name"]);
    $filePathLainnya = $targetDir . time() . "_" . $fileNameLainnya;

    if (!empty($_FILES["dokumenLainnya"]["tmp_name"])) {
        if (move_uploaded_file($_FILES["dokumenLainnya"]["tmp_name"], $filePathLainnya)) {
            $dokumenLainnya = $filePathLainnya; // No quotes
        } else {
            $dokumenLainnya = null;
        }
    } else {
        $dokumenLainnya = null;
    }

    $alasanTidakAktif = isset($_POST["alasanTidakAktif"]) ? $_POST["alasanTidakAktif"] : "";

    // field
    $datafield_terapis = array("namaTerapis", "jenisKelamin", "alamat", "noTelepon", "jenisTerapis", "spesialisasi", "instansi", "pendidikanTerakhir", "pendidikanNonFormal", "noIzinPraktek", "dokumenSertifikasi", "tanggalAktif", "statusTerapis", "alasanTidakAktif", "statusPekerja", "dokumenLainnya", "keterangan");

    // value - HAPUS QUOTE MANUAL
    $datavalue_terapis = array(
        $_POST["namaTerapis"],
        $_POST["jenisKelamin"],
        $_POST["alamat"],
        $_POST["noTelepon"],
        $jenisTerapis,
        $_POST["spesialisasi"],
        $_POST["instansi"],
        $_POST["pendidikanTerakhir"],
        $_POST["pendidikanNonFormal"] === '' ? "" : $_POST["pendidikanNonFormal"],
        $_POST["noIzinPraktek"] === '' ? "" : $_POST["noIzinPraktek"],
        $dokumenSertifikasi,
        $_POST["tanggalAktif"],
        $_POST["statusTerapis"],
        $alasanTidakAktif === '' ? NULL : $alasanTidakAktif,
        $_POST["statusPekerja"] === '' ? "" : $_POST["statusPekerja"],
        $dokumenLainnya,
        $_POST["keterangan"] === '' ? "" : $_POST["keterangan"]
    );


    $insert = new cInsert();
    // Gunakan vInsertDataPrepared
    $insert->vInsertDataPrepared("terapis", $datafield_terapis, $datavalue_terapis);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = 22;

    $sql = "SELECT dokumenSertifikasi, dokumenLainnya FROM terapis WHERE idTerapis = ?";
    $view = new cView();
    $arrayTerapis = $view->vViewDataPrepared($sql, [$_POST["idTerapis"]], "i");

    $dokumenSertifikasiLama = $arrayTerapis[0]["dokumenSertifikasi"];
    $dokumenLainnyaLama = $arrayTerapis[0]["dokumenLainnya"];

    // Mapping ENUM ke Label Tampilan
    $enumJTMapping = [
        "Tenaga Medis" => "Tenaga Medis - Dokter, Psikiater",
        "Tenaga Paramedis" => "Tenaga Paramedis - Perawat",
        "Tenaga Non Medis" => "Tenaga Non Medis - Psikolog, Terapis, Volunteer, dll"
    ];

    // Ambil ENUM asli dari label panjang yang dipilih
    $jenisTerapis = array_search($_POST["jenisTerapis"], $enumJTMapping);
    if ($jenisTerapis === false) {
        $jenisTerapis = $_POST["jenisTerapis"]; // Gunakan langsung jika tidak ditemukan
    }

    //Upload File
    $targetDir = "uploads/sertifikasi/"; // Folder penyimpanan file
    $fileName = basename((string) $_FILES["dokumenSertifikasi"]["name"]);
    $filePath = $targetDir . time() . "_" . $fileName; // Buat nama unik

    if (!empty($_FILES["dokumenSertifikasi"]["tmp_name"])) {
        if (move_uploaded_file($_FILES["dokumenSertifikasi"]["tmp_name"], $filePath)) {
            $dokumenSertifikasi = $filePath; // No quotes
        } else {
            $dokumenSertifikasi = $dokumenSertifikasiLama;
        }
    } else {
        $dokumenSertifikasi = $dokumenSertifikasiLama;
    }

    // Upload File dokumenLainnya
    // __DIR__ = folder saat ini (admin/)
    $targetDirRelatif = "uploads/dokumenLain/";
    $targetDir = __DIR__ . "/uploads/dokumenLain/"; // Path absolut
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true); // Buat folder jika belum ada
    }
    $fileNameLainnya = basename((string) $_FILES["dokumenLainnya"]["name"]);
    $filePathLainnya = $targetDir . time() . "_" . $fileNameLainnya; // Path absolut
    $filePathLainnyaRelatif = $targetDirRelatif . time() . "_" . $fileNameLainnya; // Simpan relatif di DB

    if (!empty($_FILES["dokumenLainnya"]["tmp_name"])) {
        if (move_uploaded_file($_FILES["dokumenLainnya"]["tmp_name"], $filePathLainnya)) {
            $dokumenLainnya = $filePathLainnyaRelatif; // No quotes
        } else {
            $dokumenLainnya = $dokumenLainnyaLama;
        }
    } else {
        $dokumenLainnya = $dokumenLainnyaLama;
    }

    $alasanTidakAktif = isset($_POST["alasanTidakAktif"]) ? $_POST["alasanTidakAktif"] : "";

    $datafield_terapis = array("namaTerapis", "jenisKelamin", "alamat", "noTelepon", "jenisTerapis", "spesialisasi", "instansi", "pendidikanTerakhir", "pendidikanNonFormal", "noIzinPraktek", "dokumenSertifikasi", "tanggalAktif", "statusTerapis", "alasanTidakAktif", "statusPekerja", "dokumenLainnya", "keterangan");
    // Value - HAPUS QUOTE MANUAL
    $datavalue_terapis = array(
        $_POST["namaTerapis"],
        $_POST["jenisKelamin"],
        $_POST["alamat"],
        $_POST["noTelepon"],
        $jenisTerapis,
        $_POST["spesialisasi"],
        $_POST["instansi"],
        $_POST["pendidikanTerakhir"],
        $_POST["pendidikanNonFormal"] === '' ? "" : $_POST["pendidikanNonFormal"],
        $_POST["noIzinPraktek"] === '' ? "" : $_POST["noIzinPraktek"],
        $dokumenSertifikasi,
        $_POST["tanggalAktif"],
        $_POST["statusTerapis"],
        $alasanTidakAktif === '' ? NULL : $alasanTidakAktif,
        $_POST["statusPekerja"] === '' ? "" : $_POST["statusPekerja"],
        $dokumenLainnya,
        $_POST["keterangan"] === '' ? "" : $_POST["keterangan"]
    );

    $whereCol = "idTerapis";
    $whereVal = $_POST["idTerapis"];

    $update = new cUpdate();
    // Gunakan vUpdateDataPrepared
    $update->vUpdateDataPrepared("terapis", $datafield_terapis, $datavalue_terapis, $whereCol, $whereVal);
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

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-10 col-lg-11">
            <figure>
                <blockquote class="blockquote">
                    <p>DATA TENAGA MEDIS / PARAMEDIS / NON MEDIS</p>
                </blockquote>
                <figcaption class="blockquote-footer">
                    Entri Data Tenaga Medis / Paramedis / Non Medis
                </figcaption>
            </figure>
        </div>

        <div class="col-12 col-md-2 col-lg-1 mb-3">
            <?php
            // Query ENUM 'jenisKelamin'
            $jk = "SHOW COLUMNS FROM terapis LIKE 'jenisKelamin'";
            $view = new cView();
            $arrayJK = $view->vViewData($jk);
            $enumJK = [];
            if (!empty($arrayJK)) {
                $row = $arrayJK[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumJK = explode(",", str_replace("'", "", $matches[1]));
                }
            }

            // Query ENUM 'jenisTerapis'
            $jt = "SHOW COLUMNS FROM terapis LIKE 'jenisTerapis'";
            $view = new cView();
            $arrayJT = $view->vViewData($jt);
            $enumJT = [];
            if (!empty($arrayJT)) {
                $row = $arrayJT[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumJT = explode(",", str_replace("'", "", $matches[1]));
                }
            }

            // Mapping ENUM ke Label Tampilan
            $enumJTMapping = [
                "Tenaga Medis" => "Tenaga Medis - Dokter, Psikiater",
                "Tenaga Paramedis" => "Tenaga Paramedis - Perawat",
                "Tenaga Non Medis" => "Tenaga Non Medis - Psikolog, Terapis, Volunteer, dll"
            ];

            // Buat array untuk dropdown
            $enumJTForm = [];
            foreach ($enumJTMapping as $key => $label) {
                $enumJTForm[$key] = $label; // Dropdown menampilkan label panjang
            }

            // Query ENUM 'pendidikanTerakhir'
            $pt = "SHOW COLUMNS FROM terapis LIKE 'pendidikanTerakhir'";
            $view = new cView();
            $arrayPT = $view->vViewData($pt);
            $enumPT = [];
            if (!empty($arrayPT)) {
                $row = $arrayPT[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumPT = explode(",", str_replace("'", "", $matches[1]));
                }
            }

            // Query ENUM 'statusAktif'
            $status = "SHOW COLUMNS FROM terapis LIKE 'statusTerapis'";
            $view = new cView();
            $arrayStatus = $view->vViewData($status);
            $enumStatus = [];
            if (!empty($arrayStatus)) {
                $row = $arrayStatus[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumStatus = explode(",", str_replace("'", "", $matches[1]));
                }
            }
            // Query ENUM 'statusTetap'
            $statusPekerja = "SHOW COLUMNS FROM terapis LIKE 'statusPekerja'";
            $view = new cView();
            $arraystatusPekerja = $view->vViewData($statusPekerja);
            $enumstatusPekerja = [];
            if (!empty($arraystatusPekerja)) {
                $row = $arraystatusPekerja[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumstatusPekerja = explode(",", str_replace("'", "", $matches[1]));
                }
            }
            ?>

            <button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                data-bs-toggle="modal" data-bs-target="#exampleModal"
                style="border-radius: 10px; width: 100%; height: 60%; display: flex;">
                <i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>
            </button>

            <!-- Modal INSERT -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">
                                <blockquote class="blockquote">
                                    <p>Tambah Data Tenaga Medis / Paramedis / Non Medis</p>
                                </blockquote>
                            </h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form class="" method="post" action="22" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="namaTerapis">Nama Lengkap <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="namaTerapis" id="namaTerapis" value=""
                                        placeholder="Nama lengkap" maxlength="255" size="" required>
                                </div>
                                <div class="mb-3">
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
                                <div class="mb-3">
                                    <label for="alamat">Alamat <span class="required">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat"
                                        rows="3" cols="" id="floatingTextarea" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="noTelepon">Nomor Telepon <span class="required">*</span></label>
                                    <input class="form-control" type="number" name="noTelepon" id="noTelepon" value=""
                                        placeholder="Nomor telepon" maxlength="16" size="" required>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="jenisTerapis">Jenis Terapis <span class="required">*</span></label>
                                        <select name="jenisTerapis" class="form-control" required>
                                            <option value="">- pilihan -</option>
                                            <?php
                                            foreach ($enumJTForm as $option) {
                                                $trimmedValue = trim($option);
                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="spesialisasi">Spesialisasi <span class="required">*</span></label>
                                        <input class="form-control" type="text" name="spesialisasi" id="spesialisasi"
                                            value="" placeholder="Spesialisasi" maxlength="255" size="" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="instansi">Asal Instansi <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="instansi" id="instansi" value=""
                                        placeholder="Asal instansi" maxlength="255" size="" required>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="pendidikanTerakhir">Pendidikan Terakhir <span
                                                class="required">*</span></label>
                                        <select name="pendidikanTerakhir" class="form-control" required>
                                            <option value="">- pilihan -</option>
                                            <?php
                                            foreach ($enumPT as $option) {
                                                $trimmedValue = trim($option);
                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="pendidikanNonFormal">Pendidikan Non Formal </label>
                                        <input class="form-control" type="text" name="pendidikanNonFormal"
                                            id="pendidikanNonFormal" value="" placeholder="Pendidikan non formal"
                                            maxlength="255" size="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="tanggalAktif">Tanggal Mulai Aktif <span
                                                class="required">*</span></label>
                                        <input class="form-control" type="date" name="tanggalAktif" id="tanggalAktif"
                                            value="" placeholder="Tanggal terapis mulai aktif" maxlength="" size=""
                                            required>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="statusPekerja">Status Pekerja</label>
                                        <select name="statusPekerja" class="form-control">
                                            <option value="">- pilihan -</option>
                                            <?php
                                            foreach ($enumstatusPekerja as $option) {
                                                $trimmedValue = trim($option);
                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="statusTerapis">Status Terapis <span
                                                class="required">*</span></label>
                                        <select name="statusTerapis" class="form-control" id="statusTerapis" required>
                                            <option value="">- pilihan -</option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="alasanTidakAktif">Alasan Tidak Aktif</label>
                                        <input class="form-control" type="text" name="alasanTidakAktif"
                                            id="alasanTidakAktif" value="" placeholder="Alasan terapis tidak aktif"
                                            maxlength="255" disabled>
                                    </div>
                                    <script>
                                        document.getElementById("statusTerapis").addEventListener("change", function () {
                                            var alasanInput = document.getElementById("alasanTidakAktif");
                                            if (this.value === "Tidak Aktif") {
                                                alasanInput.removeAttribute("disabled"); // Aktifkan input jika "Tidak Aktif"
                                            } else {
                                                alasanInput.setAttribute("disabled", "true"); // Nonaktifkan input jika "Aktif"
                                                alasanInput.value = ""; // Kosongkan input ketika dinonaktifkan
                                            }
                                        });
                                    </script>
                                </div>
                                <div class="mb-3">
                                    <label for="noIzinPraktek">Nomor Izin Praktek</label>
                                    <input class="form-control" type="text" name="noIzinPraktek" id="noIzinPraktek"
                                        value="" placeholder="Nomor Izin Praktek" maxlength="255" size="">
                                </div>

                                <div class="mb-3">
                                    <label for="dokumenSertifikasi">Dokumen Sertifikasi</label>
                                    <input type="file" class="form-control" id="dokumenSertifikasi"
                                        name="dokumenSertifikasi">
                                </div>

                                <div class="mb-3">
                                    <label for="dokumenLainnya">Dokumen Lainnya</label>
                                    <input type="file" class="form-control" id="dokumenLainnya" name="dokumenLainnya">
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan"
                                        placeholder="Keterangan" rows="3" cols="" id="floatingTextarea"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;"
                                    name="savebtn" value="true">SIMPAN</button>
                                <button type="reset" class="btn btn-warning btn-sm" style="border-radius: 25px;" name=""
                                    value="true">ULANG</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                                    style="border-radius: 25px;">TUTUP</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <p></p>

        <div class="row">
            <div class="col-md-12">
                <div id="" class='table-responsive'>
                    <table id='tableTerapis' class='table table-condensed'>
                        <thead>
                            <tr class=''>
                                <th width='5%'>No.</th>
                                <th width=''>Nama Lengkap</th>
                                <th width=''>Spesialisasi</th>
                                <th width=''>Instansi</th>
                                <th width=''>No Telepon</th>
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

<!-- Container for dynamic modals -->
<div id="dynamic-modal-container"></div>

<script>
    $(document).ready(function () {
        var table = $('#tableTerapis').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax/get_terapis.php",
                "type": "POST"
            },
            "columns": [
                { "data": "0" },
                { "data": "1" },
                { "data": "2" },
                { "data": "3" },
                { "data": "4" },
                { "data": "5", "orderable": false, "searchable": false },
                { "data": "6", "orderable": false, "searchable": false },
                { "data": "7", "orderable": false, "searchable": false }
            ]
        });

        $('#tableTerapis').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_terapis.php',
                type: 'POST',
                data: { id: id, action: 'view' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalView'));
                    myModal.show();
                }
            });
        });

        $('#tableTerapis').on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_terapis.php',
                type: 'POST',
                data: { id: id, action: 'edit' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    myModal.show();
                }
            });
        });

        $('#tableTerapis').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_terapis.php',
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