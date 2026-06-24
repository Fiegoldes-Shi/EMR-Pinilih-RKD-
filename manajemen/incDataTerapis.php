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
                { "data": "5", "orderable": false, "searchable": false }
            ]
        });

        $('#tableTerapis').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_terapis.php',
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