<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
?>
<?php
// insert
if (!empty($_POST["savebtn"])) {
    // Mapping role ke urlbase
    $roleMapping = [
        "1" => "admin",
        "2" => "manajemen",
        "3" => "terapis"
    ];
    
    // Ambil role dari form
    $role = $_POST["role"];
    
    // Tentukan urlbase berdasarkan role
    $urlbase = isset($roleMapping[$role]) ? $roleMapping[$role] : "default";

    // Status aktif default = 1
    $status_aktif = 1;

    $datafield_user = array("nama", "jenisKelamin", "alamat", "jbtn", "urlbase", "status_aktif", "tglMulaiAktif", "noTelp", "role", "email", "username", "password", "statusPekerja", "keterangan");

    // value
    // HAPUS QUOTE MANUAL! Prepared statement akan menangani quoting.
    $datavalue_user = array( 
        $_POST["nama"],  
        $_POST["jenisKelamin"], 
        $_POST["alamat"],
        $_POST["jbtn"], 
        $urlbase, 
        $status_aktif,  
        $_POST["tglMulaiAktif"],  
        $_POST["noTelp"],  
        $_POST["role"], 
        $_POST["email"], 
        $_POST["username"], 
        md5($_POST["password"]), 
        $_POST["statusPekerja"] === '' ? "" : $_POST["statusPekerja"], 
        $_POST["keterangan"] === '' ? "" : $_POST["keterangan"]
    );


    // Cek apakah username sudah digunakan
    $sqlCekUsername = "SELECT idUser FROM user WHERE username = ?";
    $viewCek = new cView();
    $arrayCek = $viewCek->vViewDataPrepared($sqlCekUsername, [$_POST["username"]], "s");
    if (!empty($arrayCek)) {
        echo "<script>
            Swal.fire({
              position:'center',
              width:'20em',
              icon: 'error',
              title: 'Username Sudah Digunakan',
              text: 'Username \"" . addslashes($_POST["username"]) . "\" sudah dipakai pengguna lain. Silakan pilih username yang berbeda.',
            }).then(function() { window.location = ''; });
        </script>";
    } else {
        $insert = new cInsert();
        $insert->vInsertDataPrepared("user", $datafield_user, $datavalue_user);
    }
}
?>


<?php
// update
if (!empty($_POST["editbtn"])) {
    // Ambil data sebelumnya dari database
    // Ambil data sebelumnya dari database
    $sql = "SELECT password, role, urlbase FROM user WHERE idUser = ?";
    $view = new cView();
    $arrayUser = $view->vViewDataPrepared($sql, [$_POST["idUser"]], "i");

    if (!empty($arrayUser)) {
        $data = $arrayUser[0]; 
        $passwd = $data["password"];
        $oldRole = $data["role"];
        $oldUrlbase = $data["urlbase"];
    }

    // Cek apakah password diubah atau tidak
    if ($_POST["password"] == $passwd) {
        $passwd = $_POST["password"];
    } else {
        $passwd = md5($_POST["password"]);
    }

    // Mapping role ke urlbase
    $roleMapping = [
        "1" => "admin",
        "2" => "manajemen",
        "3" => "terapis"
    ];
    
    // Gunakan role baru jika ada, jika tidak ada pakai yang lama
    $role = isset($_POST["role"]) && $_POST["role"] !== "" ? $_POST["role"] : $oldRole;
    
    // Tentukan urlbase berdasarkan role, tetapi tetap gunakan nilai lama jika role tidak berubah
    $urlbase = isset($roleMapping[$role]) ? $roleMapping[$role] : $oldUrlbase;

    // Status aktif default = 1
    $status_aktif = 1;

    $datafield_user = array("nama", "jenisKelamin", "alamat", "jbtn", "urlbase", "status_aktif", "tglMulaiAktif", "noTelp", "role", "email", "username", "password", "statusPekerja", "keterangan");

    // value - HAPUS QUOTE MANUAL
    $datavalue_user = array( 
        $_POST["nama"],  
        $_POST["jenisKelamin"], 
        $_POST["alamat"], 
        $_POST["jbtn"], 
        $urlbase, 
        $status_aktif,  
        $_POST["tglMulaiAktif"],  
        $_POST["noTelp"], 
        $role, 
        $_POST["email"], 
        $_POST["username"], 
        $passwd, 
        $_POST["statusPekerja"] === '' ? "" : $_POST["statusPekerja"], 
        $_POST["keterangan"] === '' ? "" : $_POST["keterangan"]
    );

    // $datakey tidak lagi dibutuhkan full string, cukup Value-nya saja
    $whereCol = "idUser";
    $whereVal = $_POST["idUser"];

    $update = new cUpdate();
    // Gunakan vUpdateDataPrepared($table, $fields, $values, $whereCol, $whereVal)
    $update->vUpdateDataPrepared("user", $datafield_user, $datavalue_user, $whereCol, $whereVal);
}
?>


<?php
// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    foreach ($_POST["hiddendeletevalue"] as $data) {
        $delete->vDeleteDataPrepared($data["table"], $data["field"], $data["value"]);        
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-10 col-lg-11">
            <figure>
                <blockquote class="blockquote">
                    <p>DATA PENGGUNA</p>
                </blockquote>
                <figcaption class="blockquote-footer">
                    Entri Data Pengguna
                </figcaption>
            </figure>
        </div>

        <div class="col-12 col-md-2 col-lg-1 mb-3">
            <?php
            // Query ENUM 'jenisKelamin'
            $jk = "SHOW COLUMNS FROM user LIKE 'jenisKelamin'";
            $view = new cView();
            $arrayJK = $view->vViewData($jk);
            $enumJK = [];
            if (!empty($arrayJK)) {
                $row = $arrayJK[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumJK = explode(",", str_replace("'", "", $matches[1]));
                }
            }

            // Query ENUM 'statusPengguna'
            $sp = "SHOW COLUMNS FROM user LIKE 'role'";
            $view = new cView();
            $arraySP = $view->vViewData($sp);
            $enumSP = [];
            if (!empty($arraySP)) {
                $row = $arraySP[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumSP = explode(",", str_replace("'", "", $matches[1]));
                }
            }
                
            // Mapping ENUM ke Label Tampilan
            $enumSPMapping = [
                "1" => "1 - Admin/Operator",
                "2" => "2 - Manajemen RKD",
                "3" => "3 - Terapis"
            ];
            // Buat array dalam format yang sesuai untuk $afield
            $enumSPForm = [];
            foreach ($enumSP as $value) {
                $trimmedValue = trim($value);
                if (isset($enumSPMapping[$trimmedValue])) {
                    $enumSPForm[$trimmedValue] = $enumSPMapping[$trimmedValue];
                }
            }

            // Query ENUM 'status_pekerja'
            $statusPekerja= "SHOW COLUMNS FROM user LIKE 'statusPekerja'";
            $view = new cView();
            $arraystatusPekerja = $view->vViewData($statusPekerja);
            $enumStatusPekerja = [];
            if (!empty($arraystatusPekerja)) {
                $row = $arraystatusPekerja[0]; // Ambil hasil pertama
                if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                    $enumStatusPekerja = explode(",", str_replace("'", "", $matches[1]));
                }
            }
            ?>

            <button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center" 
                    data-bs-toggle="modal" data-bs-target="#exampleModal" 
                    style="border-radius: 10px; width: 100%; height: 60%; display: flex;">
                <i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>
            </button>

            <!-- MODAL INSERT -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">
                                <blockquote class="blockquote">
                                    <p>Tambah Data Pengguna</p>
                                </blockquote>
                            </h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form class="" method="post" action="data-pengguna" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama">Nama Pengguna <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="nama" id="nama" value="" placeholder="Nama pengguna" maxlength="255" size="" required>
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
                                    <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat pengguna" rows="3" cols="" id="floatingTextarea" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="noTelp">Nomor Telepon <span class="required">*</span></label>
                                    <input class="form-control" type="number" name="noTelp" id="noTelepon" value="" placeholder="Nomor telepon pengguna" maxlength="16" size="" required>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="role">Status Pengguna <span class="required">*</span></label>
                                        <select name="role" class="form-control" required>
                                            <option value="">- pilihan -</option>
                                            <?php
                                                foreach ($enumSPForm as $key => $label) {
                                                    echo '<option value="' . $key . '">' . $label . '</option>';
                                                }
                                            ?>
                                        </select>                                    
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="jbtn">Jabatan <span class="required">*</span></label>
                                        <input class="form-control" type="text" name="jbtn" id="jbtn" value="" placeholder="Jabatan" maxlength="255" size="" required>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="tglMulaiAktif">Tanggal Mulai Aktif <span class="required">*</span></label>
                                        <input class="form-control" type="date" name="tglMulaiAktif" id="tglMulaiAktif" value="" placeholder="Tanggal Mulai Aktif Pengguna" maxlength="" size="" required>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="statusPekerja">Status Pekerja <span class="required">*</span></label>
                                        <select name="statusPekerja" class="form-control" required>
                                            <option value="">- pilihan -</option>
                                            <?php
                                                foreach ($enumStatusPekerja as $option) {
                                                    $trimmedValue = trim($option);
                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                }
                                            ?>
                                        </select>                                    
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="email" id="email" value="" placeholder="Email pengguna" maxlength="255" size="" required>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="username">Username <span class="required">*</span></label>
                                        <input class="form-control" type="text" name="username" id="username" value="" placeholder="Username" maxlength="255" size="" required>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="password">Password <span class="required">*</span></label>
                                        <input class="form-control" type="text" name="password" id="password" value="" placeholder="Password" maxlength="255" size="" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" rows="3" cols="" id="floatingTextarea"></textarea>
                                </div>  
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;" name="savebtn" value="true">SIMPAN</button>
                                <button type="reset" class="btn btn-warning btn-sm" style="border-radius: 25px;" name="" value="true">ULANG</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                            </div>
                            </form>                                  
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <p></p>

    <div class="row">
        <div class="col-md-12">
            <div id="" class='table-responsive'>
                <table id='tablePengguna' class='table table-condensed'>
                    <thead>
                        <tr class=''>
                            <th width='5%'>No.</th>
                            <th>Nama Pengguna</th>
                            <th>Jabatan</th>
                            <th>No Telepon</th>
                            <th>Username</th>
                            <th>Email</th>
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

<!-- Container for dynamic modals -->
<div id="dynamic-modal-container"></div>

<script>
    $(document).ready(function () {
        var table = $('#tablePengguna').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax/get_pengguna.php",
                "type": "POST"
            },
            "columns": [
                { "data": "0" },
                { "data": "1" },
                { "data": "2" },
                { "data": "3" },
                { "data": "4" },
                { "data": "5" },
                { "data": "6", "orderable": false, "searchable": false },
                { "data": "7", "orderable": false, "searchable": false },
                { "data": "8", "orderable": false, "searchable": false }
            ]
        });

        $('#tablePengguna').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_pengguna.php',
                type: 'POST',
                data: { id: id, action: 'view' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalView'));
                    myModal.show();
                }
            });
        });

        $('#tablePengguna').on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_pengguna.php',
                type: 'POST',
                data: { id: id, action: 'edit' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    myModal.show();
                }
            });
        });

        $('#tablePengguna').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_pengguna.php',
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
