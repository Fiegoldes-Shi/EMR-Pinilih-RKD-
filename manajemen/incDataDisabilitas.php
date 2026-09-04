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
                    <p>DATA DISABILITAS</p>
                </blockquote>
                <figcaption class="blockquote-footer">
                    Entri Data Disabilitas
                </figcaption>
            </figure>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div id="" class='table-responsive'>
                    <table id='tableDisabilitas' class='table table-condensed'>
                        <thead>
                            <tr class=''>
                                <th width='15%' class="text-right">No.</th>
                                <th width=''>Jenis Disabilitas</th>
                                <th width=''>Subjenis Disabilitas</th>
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
        var table = $('#tableDisabilitas').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax/get_disabilitas.php",
                "type": "POST"
            },
            "columns": [
                { "data": "0" },
                { "data": "1" },
                { "data": "2" },
                { "data": "3", "orderable": false, "searchable": false }
            ]
        });

        $('#tableDisabilitas').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_disabilitas.php',
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