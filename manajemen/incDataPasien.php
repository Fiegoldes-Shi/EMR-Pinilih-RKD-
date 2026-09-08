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
