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
                    <p>DATA TENAGA MEDIS / PARAMEDIS / NON MEDIS</p>
                </blockquote>
                <figcaption class="blockquote-footer">
                    Entri Data Tenaga Medis / Paramedis / Non Medis
                </figcaption>
            </figure>
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