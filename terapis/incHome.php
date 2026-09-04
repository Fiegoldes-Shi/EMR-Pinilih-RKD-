<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
?>
<div class="row justify-content-center">
    <div class="col-md-2">
        <div class="card text-center border border-4 border-success text-success">
            <div class="card-body">
                <?php  
                    $jmlPasien = "SELECT COUNT(*) as totalPasien FROM pasien";
                    $view = new cView();
                    $view->vViewData($jmlPasien);
                    $arrayjmlPasien = $view->vViewData($jmlPasien);
                    foreach ($arrayjmlPasien as $dataPasien) {
                        $totalPasien = $dataPasien["totalPasien"];
                    }
                ?>
                <h2><?= $totalPasien ?></h2>
                <h3>Pasien</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border border-4 border-success text-success">
            <div class="card-body">
                <?php  
                    $jmlTerapis = "SELECT COUNT(*) as totalTerapis FROM terapis";
                    $view = new cView();
                    $view->vViewData($jmlTerapis);
                    $arrayjmlTerapis = $view->vViewData($jmlTerapis);
                    foreach ($arrayjmlTerapis as $dataTerapis) {
                        $totalTerapis = $dataTerapis["totalTerapis"];
                    }
                ?>
                <h2><?= $totalTerapis ?></h2>
                <h3>Terapis</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border border-4 border-success text-success">
            <div class="card-body">
                <?php  
                    $jmlPengguna = "SELECT COUNT(*) as totalPengguna FROM user";
                    $view = new cView();
                    $view->vViewData($jmlPengguna);
                    $arrayjmlPengguna = $view->vViewData($jmlPengguna);
                    foreach ($arrayjmlPengguna as $dataPengguna) {
                        $totalPengguna = $dataPengguna["totalPengguna"];
                    }
                ?>
                <h2><?= $totalPengguna ?></h2>
                <h3>Pengguna</h3>
            </div>
        </div>
    </div>
</div>

<br>
<p></p>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card text-center border border-2">
            <div class="card-body">
                <h4>Daftar Kegiatan Terbaru</h4>
                <br>
                <?php
                $sqljadwal = "SELECT jp.*, prog.*, u.* FROM jadwal_program jp 
                            JOIN program prog ON jp.idProgram = prog.idProgram 
                            JOIN user u ON jp.idUser = u.idUser
                            ORDER BY jp.tanggalKegiatan DESC LIMIT 10";
                $view = new cView();
                $arrayjadwal = $view->vViewData($sqljadwal);
                $idUser = $_SESSION["idUser"];
                ?>

                <?php  
                $jumlahPeserta = [];
                // Ambil semua idJadwal untuk program edukasi
                $queryEdukasi = "SELECT pe.idEdukasi, jp.idJadwal 
                                FROM program_edukasi pe 
                                JOIN jadwal_program jp ON pe.idJadwal = jp.idJadwal";
                $view = new cView();
                $arrayEdukasi = $view->vViewData($queryEdukasi);

                // Buat mapping idJadwal ke idEdukasi
                $mapEdukasi = [];
                foreach ($arrayEdukasi as $data) {
                    $mapEdukasi[$data["idJadwal"]] = $data["idEdukasi"];
                }

                // Hitung jumlah peserta untuk semua idJadwal yang tersedia
                $queryPeserta = "SELECT idJadwal, COUNT(*) as totalPeserta FROM hasil_layanan GROUP BY idJadwal";
                $arrayPeserta = $view->vViewData($queryPeserta);

                // Simpan jumlah peserta berdasarkan idJadwal
                foreach ($arrayPeserta as $data) {
                    $jumlahPeserta[$data["idJadwal"]] = $data["totalPeserta"];
                }

                // Hitung jumlah peserta untuk program edukasi
                foreach ($mapEdukasi as $idJadwal => $idEdukasi) {
                    $queryPesertaEdukasi = "SELECT COUNT(*) as totalPeserta FROM peserta_edukasi WHERE idEdukasi = '$idEdukasi'";
                    $arrayPesertaEdukasi = $view->vViewData($queryPesertaEdukasi);
                    
                    if (!empty($arrayPesertaEdukasi)) {
                        $jumlahPeserta[$idJadwal] = $arrayPesertaEdukasi[0]["totalPeserta"];
                    }
                }
                ?>
                <div id="" class='table-responsive'>
                    <table id='' class='table table-condensed'>
                        <thead class="table table-primary">
                            <tr class='small text-center'>
                                <th width='5%'>No.</th>
                                <th width='15%'>Tanggal</th>
                                <th width=''>Waktu Mulai</th>
                                <th width=''>Waktu Selesai</th>
                                <th width=''>Program</th>
                                <th width='25%'>Instansi</th>
                                <th width=''>Jumlah Peserta</th>
                                <th width='5%'>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cnourut = 0;
                            foreach ($arrayjadwal as $datajadwal) {
                                $cnourut = $cnourut + 1;
                                $idJadwal = $datajadwal["idJadwal"];
                                $totalPeserta = isset($jumlahPeserta[$idJadwal]) ? $jumlahPeserta[$idJadwal] : 0;

                            // Mapping idProgram ke angka awal URL
                            $idProgram = $datajadwal["idProgram"];
                            $urlProgram = "";

                            switch ($idProgram) {
                                case "1":
                                    $urlProgram = $ROUTES['fisioterapi_detail'];
                                    break;
                                case "2":
                                    $urlProgram = $ROUTES['kinesioterapi_detail'];
                                    break;
                                case "3":
                                    $urlProgram = $ROUTES['screening_detail'];
                                    break;
                                case "4":
                                    $urlProgram = $ROUTES['konsultasi_detail'];
                                    break;
                                case "5":
                                    $urlProgram = $ROUTES['edukasi_detail'];
                                    break;
                            }
                            ?>
                                <tr class=''>
                                    <td class="text-center"><?= $cnourut; ?></td>
                                    <td class="text-center"><?= $datajadwal["tanggalKegiatan"]; ?></td>
                                    <td class="text-center"><?= $datajadwal["waktuMulai"]; ?></td>
                                    <td class="text-center"><?= $datajadwal["waktuSelesai"]; ?></td>
                                    <td><?= $datajadwal["namaProgram"]; ?></td>
                                    <td><?= $datajadwal["instansi"]; ?></td>
                                    <td class="text-center"><?= $totalPeserta ?></td>
                                    <td>
                                        <a href="<?php echo $baseurl . '/terapis/' . $urlProgram .'/'. $datajadwal["idJadwal"]; ?>" class="btn btn-info" style="border-radius: 8px;">
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
    </div>
</div>

<p></p>
<div class="row">
    <div class="col-md-12">
        <p><br><br><br><br><br></p>
        
    </div>
</div>