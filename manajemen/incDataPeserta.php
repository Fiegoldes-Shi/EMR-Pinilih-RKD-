<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Peserta</title>

    <script src="../manajemen/js/jquery.min.js" type="text/javascript"></script>
    <script src="../manajemen/js/config.js?v=<?php echo time(); ?>" type="text/javascript"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-11">
                <figure>
                    <blockquote class="blockquote">
                        <p>DATA PESERTA</p>
                    </blockquote>
                    <figcaption class="blockquote-footer">Entri Data Peserta</figcaption>
                </figure>
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
                { "data": "7", "orderable": false, "searchable": false }
            ]
        });

        $('#tablePeserta').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_peserta.php',
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