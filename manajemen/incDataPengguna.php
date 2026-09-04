<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
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
            $statusPekerja = "SHOW COLUMNS FROM user LIKE 'statusPekerja'";
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
                { "data": "6", "orderable": false, "searchable": false }
            ]
        });

        $('#tablePengguna').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_pengguna.php',
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