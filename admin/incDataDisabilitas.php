<?php
if (!empty($_POST["savebtn"])) {
    $linkurl = 24;

    // Data untuk sub disabilitas
    $datafield_dis = array("idJenisDisabilitas", "namaDisabilitas");

    if (!empty($_POST["idJenisDisabilitas"]) && $_POST["idJenisDisabilitas"] !== "baru") {
        // Jika sudah ada, langsung masukkan ke sub disabilitas
        $idJenisDisabilitas = $_POST["idJenisDisabilitas"];
    } else {
        // Jika disabilitas baru, langsung set jadi utama
        $datafield_jd = array("jenisDisabilitas", "is_utama");
        // Hapus quote manual
        $datavalue_jd = array($_POST["jenisDisabilitasBaru"], 1);

        $insert1 = new cInsert();
        // Gunakan vInsertDataPrepared
        $idJenisDisabilitas = $insert1->vInsertDataPrepared("jenis_disabilitas", $datafield_jd, $datavalue_jd);
    }

    // Insert ke peserta_edukasi jika $idPeserta valid
    if ($idJenisDisabilitas) {
        $datavalue_dis = array($idJenisDisabilitas, $_POST["namaDisabilitas"]);
        $insert2 = new cInsert();
        // Gunakan vInsertDataPrepared
        $insert2->vInsertDataPrepared("sub_disabilitas", $datafield_dis, $datavalue_dis);
    }
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = 24;

    $idJenisDisabilitas = $_POST["idJenisDisabilitas"] ? $_POST['idJenisDisabilitas'] : NULL;

    $datafield_dis = array("idJenisDisabilitas", "namaDisabilitas");

    $datavalue_dis = array($idJenisDisabilitas, $_POST["namaDisabilitas"]);

    $whereCol = "idSubDisabilitas";
    $whereVal = $_POST["idSubDisabilitas"];

    $update = new cUpdate();
    // Gunakan vUpdateDataPrepared
    $update->vUpdateDataPrepared("sub_disabilitas", $datafield_dis, $datavalue_dis, $whereCol, $whereVal);
}
?>

<?php
// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    foreach ($_POST["hiddendeletevalue"] as $data) {
        $idSubDisabilitas = $data["value"]; // ID Sub Disabilitas yang akan dihapus

        // Ambil idJenisDisabilitas dari sub_disabilitas sebelum dihapus
        $view = new cView();
        $queryGetJenis = "SELECT idJenisDisabilitas FROM sub_disabilitas WHERE idSubDisabilitas = ?";
        $rows = $view->vViewDataPrepared($queryGetJenis, [$idSubDisabilitas], "i");
        $idJenisDisabilitas = $rows[0]["idJenisDisabilitas"] ?? null;

        if ($idJenisDisabilitas) {
            // Hapus sub_disabilitas terlebih dahulu
            $delete->vDeleteDataPrepared("sub_disabilitas", "idSubDisabilitas", $idSubDisabilitas);

            // Cek apakah idJenisDisabilitas masih digunakan di tabel sub_disabilitas DIHILANGKAN
            // Hal ini agar Master Jenis Disabilitas tidak ikut terhapus otomatis walau jumlah relasinya sudah 0.
            // Penghapusan Jenis kini hanya bisa dilakukan via Modal "Kelola Master Jenis"
        }
    }
}

// hapus jenis disabilitas independen (manual via modal Kelola Jenis)
if (!empty($_POST["btnHapusJenis"])) {
    $delete = new cDelete();
    // Hapus juga semua sub_disabilitas yang menggunakan jenis ini (hapus data-data terhubung)
    $delete->vDeleteDataPrepared("sub_disabilitas", "idJenisDisabilitas", $_POST["idHapusJenis"]);
    // Hapus jenis_disabilitanya itu sendiri
    $delete->vDeleteDataPrepared("jenis_disabilitas", "idJenisDisabilitas", $_POST["idHapusJenis"]);
}

// toggle set is_utama
if (!empty($_POST["btnSetUtama"])) {
    $update = new cUpdate();
    $statusBaru = $_POST["statusUtamaBaru"];
    $update->vUpdateDataPrepared("jenis_disabilitas", ["is_utama"], [$statusBaru], "idJenisDisabilitas", $_POST["idSetUtama"]);
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
        <div class="col-12 col-md-2 col-lg-1 mb-3">
            <button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                data-bs-toggle="modal" data-bs-target="#exampleModal"
                style="border-radius: 10px; width: 100%; height: 60%; display: flex;">
                <i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>
            </button>

            <!-- Modal Insert -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">
                                <blockquote class="blockquote">
                                    <p>Tambah Data Disabilitas</p>
                                </blockquote>
                            </h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="data-disabilitas" enctype="multipart/form-data">
                            <div class="modal-body">
                                <!-- Pilih Jenis Disabilitas -->
                                <div id="selectDisabilitasContainer">
                                    <div class="mb-3">
                                        <label for="pilihJD"
                                            class="d-flex justify-content-between align-items-center w-100">
                                            <span>Pilih Jenis Disabilitas <span class="required">*</span></span>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalKelolaJenis"
                                                class="text-info text-decoration-none" style="font-size: 0.85em;"><i
                                                    class="fa-solid fa-list-check"></i> Kelola Jenis</a>
                                        </label>
                                        <select id="pilihJD" name="idJenisDisabilitas" class="form-control" required>
                                            <option value="">- Pilih Jenis Disabilitas -</option>
                                            <?php
                                            // Ambil data jenis disabilitas dari database
                                            $queryJD = "SELECT * FROM jenis_disabilitas";
                                            $view = new cView();
                                            $resultJD = $view->vViewData($queryJD);

                                            foreach ($resultJD as $row): ?>
                                                <option value="<?= $row['idJenisDisabilitas'] ?>">
                                                    <?= $row['jenisDisabilitas'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <option value="baru" style="color: blue;"> + Tambah Jenis Disabilitas Baru
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namaDisabilitas">Sub Jenis Disabilitas <span
                                                class="required">*</span></label>
                                        <input class="form-control" type="text" name="namaDisabilitas"
                                            id="namaDisabilitas" placeholder="Nama Disabilitas" maxlength="255"
                                            required>
                                    </div>
                                </div>

                                <!-- Form Jenis Disabilitas Baru -->
                                <div id="formDisabilitasBaru" style="display: none;">
                                    <div class="mb-3">
                                        <label for="jenisDisabilitasBaru"
                                            class="d-flex justify-content-between align-items-center w-100">
                                            <span>Jenis Disabilitas <span class="required">*</span></span>
                                            <a href="javascript:void(0)" id="btnBatalBaru"
                                                class="text-danger text-decoration-none" style="font-size: 0.85em;"><i
                                                    class="fa-solid fa-circle-xmark"></i> Batal Baru</a>
                                        </label>
                                        <input class="form-control" type="text" name="jenisDisabilitasBaru"
                                            id="jenisDisabilitasBaru" placeholder="Jenis Disabilitas" maxlength="255"
                                            disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namaDisabilitasBaru">Sub Jenis Disabilitas <span
                                                class="required">*</span></label>
                                        <input class="form-control" type="text" name="namaDisabilitas"
                                            id="namaDisabilitasBaru" placeholder="Nama Disabilitas" maxlength="255"
                                            disabled required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;"
                                    name="savebtn" value="true">Simpan</button>
                                <button type="reset" class="btn btn-warning btn-sm"
                                    style="border-radius: 25px;">Ulang</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                                    style="border-radius: 25px;">Tutup</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const selectContainer = document.getElementById("selectDisabilitasContainer");
                const formBaru = document.getElementById("formDisabilitasBaru");
                const selectPilihJD = document.getElementById("pilihJD");
                const btnBatalBaru = document.getElementById("btnBatalBaru");

                selectPilihJD.addEventListener("change", function () {
                    // Jenis Disabilitas Baru
                    if (this.value === "baru") {
                        // Sembunyikan dropdown, tetapi biarkan input sub jenis tetap terlihat
                        selectContainer.style.display = "none";
                        formBaru.style.display = "block";

                        // Aktifkan input dalam form jenis disabilitas baru
                        document.querySelectorAll("#formDisabilitasBaru input").forEach(el => {
                            el.removeAttribute("disabled");
                        });

                        // Pastikan nama input sub jenis tetap sesuai agar terkirim dalam form
                        document.getElementById("namaDisabilitasBaru").setAttribute("name", "namaDisabilitas");
                        // Hanya sub jenis disabilitas yang baru
                    } else {
                        // Jika memilih jenis yang ada, tampilkan dropdown
                        selectContainer.style.display = "block";
                        formBaru.style.display = "none";

                        // Matikan input jenis disabilitas baru
                        document.querySelectorAll("#formDisabilitasBaru input").forEach(el => {
                            el.setAttribute("disabled", "true");
                        });

                        document.getElementById("namaDisabilitasBaru").removeAttribute("name");
                    }
                });

                // Aksi tombol batal tambah baru
                if (btnBatalBaru) {
                    btnBatalBaru.addEventListener("click", function () {
                        selectPilihJD.value = ""; // Reset pilihan dropdown ke default
                        formBaru.style.display = "none";
                        selectContainer.style.display = "block";
                        document.querySelectorAll("#formDisabilitasBaru input").forEach(el => {
                            el.value = "";
                            el.setAttribute("disabled", "true");
                        });
                        document.getElementById("namaDisabilitasBaru").removeAttribute("name");
                    });
                }

                // Tambahkan listener untuk mereset seluruh tampilan jika modal ditutup atau tombol ulang ditekan
                const formTambahData = selectPilihJD.closest("form");
                if (formTambahData) {
                    formTambahData.addEventListener("reset", function () {
                        setTimeout(() => {
                            selectPilihJD.value = "";
                            formBaru.style.display = "none";
                            selectContainer.style.display = "block";
                            document.querySelectorAll("#formDisabilitasBaru input").forEach(el => {
                                el.setAttribute("disabled", "true");
                            });
                            document.getElementById("namaDisabilitasBaru").removeAttribute("name");
                        }, 50);
                    });
                }
            });
        </script>

        <!-- Modal Kelola Jenis Disabilitas -->
        <div class="modal fade" id="modalKelolaJenis" tabindex="-1" aria-labelledby="modalKelolaJenisLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKelolaJenisLabel">
                            <blockquote class="blockquote">
                                <p>Kelola Master Jenis Disabilitas</p>
                            </blockquote>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 border-0">Jenis Disabilitas</th>
                                        <th width="18%" class="text-center border-0">Status</th>
                                        <th width="18%" class="text-center border-0">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query dengan LEFT JOIN untuk cek penggunaan di sub_disabilitas
                                    $queryKJD = "SELECT jd.idJenisDisabilitas, jd.jenisDisabilitas, jd.is_utama, COUNT(sd.idSubDisabilitas) as jml FROM jenis_disabilitas jd LEFT JOIN sub_disabilitas sd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas GROUP BY jd.idJenisDisabilitas, jd.jenisDisabilitas, jd.is_utama ORDER BY jd.is_utama DESC, jd.jenisDisabilitas ASC";
                                    $viewKJD = new cView();
                                    $resultKJD = $viewKJD->vViewData($queryKJD);
                                    foreach ($resultKJD as $rowKJD):
                                        ?>
                                        <tr>
                                            <td class="align-middle ps-3 border-0 py-2"><?= $rowKJD['jenisDisabilitas'] ?>
                                            </td>
                                            <td class="text-center align-middle border-0 py-2">
                                                <form method="post" action="data-disabilitas" style="margin:0;">
                                                    <input type="hidden" name="idSetUtama"
                                                        value="<?= $rowKJD['idJenisDisabilitas'] ?>">
                                                    <?php if ($rowKJD['is_utama'] == 1): ?>
                                                        <input type="hidden" name="statusUtamaBaru" value="0">
                                                        <button type="submit" name="btnSetUtama" value="true"
                                                            class="btn btn-sm text-warning p-0"
                                                            title="Klik untuk jadikan Biasa"><i class="fa-solid fa-star"></i>
                                                            Utama</button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="statusUtamaBaru" value="1">
                                                        <button type="submit" name="btnSetUtama" value="true"
                                                            class="btn btn-sm text-secondary p-0"
                                                            title="Klik untuk jadikan Utama"><i class="fa-regular fa-star"></i>
                                                            Biasa</button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>
                                            <td class="text-center align-middle border-0 py-2">
                                                <?php if ($rowKJD['is_utama'] == 1): ?>
                                                    <span class="badge bg-success px-3 py-2"
                                                        title="Jenis Utama tidak bisa dihapus"><i class="fa-solid fa-lock"></i>
                                                        Utama</span>
                                                <?php elseif ($rowKJD['jml'] > 0): ?>
                                                    <form method="post" action="data-disabilitas" style="margin:0;">
                                                        <input type="hidden" name="idHapusJenis"
                                                            value="<?= $rowKJD['idJenisDisabilitas'] ?>">
                                                        <button type="submit" name="btnHapusJenis" value="true"
                                                            class="btn btn-warning btn-sm fw-bold px-2 py-1 text-nowrap"
                                                            style="border-radius: 5px; font-size: 0.75em;"
                                                            onclick="return confirm('PERHATIAN:\n\nKategori Disabilitas ini sedang terhubung dengan <?= $rowKJD['jml'] ?> data pasien.\n\nMenghapus kategori ini juga akan MENGHAPUS SELURUH DATA PASIEN yang terhubung secara permanen.\n\nApakah Anda yakin ingin melanjutkan tindakan ini?');"><i
                                                                class="fa-solid fa-trash-can"></i> Dipakai
                                                            (<?= $rowKJD['jml'] ?>)</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="post" action="data-disabilitas" style="margin:0;">
                                                        <input type="hidden" name="idHapusJenis"
                                                            value="<?= $rowKJD['idJenisDisabilitas'] ?>">
                                                        <button type="submit" name="btnHapusJenis" value="true"
                                                            class="btn btn-danger btn-sm" style="border-radius: 5px;"
                                                            onclick="return confirm('Anda yakin menghapus Jenis Disabilitas ini permanen?');"><i
                                                                class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: none;">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModal" style="border-radius: 25px;">Kembali</button>
                    </div>
                </div>
            </div>
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
                                <th width='5%' data-searchable="false">VIEW</th>
                                <th width='5%' data-searchable="false">EDIT</th>
                                <th width='5%' data-searchable="false">HAPUS</th>
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
                { "data": "3", "orderable": false, "searchable": false },
                { "data": "4", "orderable": false, "searchable": false },
                { "data": "5", "orderable": false, "searchable": false }
            ]
        });

        $('#tableDisabilitas').on('click', '.btn-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_disabilitas.php',
                type: 'POST',
                data: { id: id, action: 'view' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalView'));
                    myModal.show();
                }
            });
        });

        $('#tableDisabilitas').on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_disabilitas.php',
                type: 'POST',
                data: { id: id, action: 'edit' },
                success: function (response) {
                    $('#dynamic-modal-container').html(response);
                    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    myModal.show();
                }
            });
        });

        $('#tableDisabilitas').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax/modal_disabilitas.php',
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