<div class="row">
    <div class="col-12 col-md-10 col-lg-11">
        <?php
        _myHeader("KONSULTASI", "Jadwal Program Konsultasi");
        ?>
    </div>
</div>
<p></p>

<p></p>
<div class="row">
    <div class="col-md-12">
        <?php
        $sqljadwal = "SELECT jp.*, prog.*, u.* FROM jadwal_program jp 
                    JOIN program prog ON jp.idProgram = prog.idProgram 
                    JOIN user u ON jp.idUser = u.idUser
                    WHERE jp.idProgram = 4
                    ORDER BY jp.tanggalKegiatan DESC";
        $view = new cView();
        $arrayjadwal = $view->vViewData($sqljadwal);
        ?>
        <div id="" class='table-responsive'>
            <table id='example' class='table table-condensed'>
                <thead>
                    <tr>
                        <th width='5%' class="text-right">No.</th>
                        <th width=''>Tanggal</th>
                        <th width=''>Waktu Mulai</th>
                        <th width=''>Waktu Selesai</th>
                        <th width=''>Instansi</th>
                        <th width='5%'>VIEW</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    foreach ($arrayjadwal as $datajadwal) {
                        $cnourut = $cnourut + 1;
                        ?>
                        <tr class=''>
                            <td class="text-right"><?= $cnourut; ?></td>
                            <td><?= $datajadwal["tanggalKegiatan"]; ?></td>
                            <td><?= $datajadwal["waktuMulai"]; ?></td>
                            <td><?= $datajadwal["waktuSelesai"]; ?></td>
                            <td><?= $datajadwal["instansi"]; ?></td>
                            <td>
                                <a href="<?= $baseurl ?>/manajemen/konsultasi/detail/<?php echo $datajadwal["idJadwal"]; ?>" class="btn btn-info"
                                    style="border-radius: 8px;">
                                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<p></p>
<div class="row">
    <div class="col-md-12">
        <p><br><br><br><br><br></p>

    </div>
</div>




