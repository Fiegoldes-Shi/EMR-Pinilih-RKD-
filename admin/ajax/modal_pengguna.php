<?php
include 'connection.php';

$action = $_POST['action'] ?? '';
$idUser = $_POST['id'] ?? '';

if (empty($idUser)) {
    echo "ID Pengguna tidak ditemukan.";
    exit;
}

$sql = "SELECT * FROM user WHERE idUser = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idUser]);
$datapengguna = $stmt->fetch();

if (!$datapengguna) {
    echo "Data Pengguna tidak ditemukan.";
    exit;
}

function getEnumValuesUser($pdo, $column)
{
    $stmt = $pdo->query("SHOW COLUMNS FROM user LIKE '$column'");
    $row = $stmt->fetch();
    $enum = [];
    if ($row && preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enum = explode(",", str_replace("'", "", $matches[1]));
    }
    return $enum;
}

$enumSPMapping = [
    "1" => "1 - Admin/Operator",
    "2" => "2 - Manajemen RKD",
    "3" => "3 - Terapis"
];

if ($action == 'view') {
    $roleLabel = $enumSPMapping[$datapengguna["role"]] ?? $datapengguna["role"];
    ?>
    <!-- Modal View Detil -->
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog text-start modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">DETAIL PENGGUNA
                            <?= htmlspecialchars($datapengguna["idUser"]); ?>
                        </blockquote>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed">
                        <tr>
                            <td width="39%">Nama Lengkap</td>
                            <td width="1%">:</td>
                            <td width="60%"><?= htmlspecialchars($datapengguna["nama"]); ?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["jenisKelamin"]); ?></td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["jbtn"]); ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["alamat"]); ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Mulai Aktif</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["tglMulaiAktif"]); ?></td>
                        </tr>
                        <tr>
                            <td>No Telepon</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["noTelp"]); ?></td>
                        </tr>
                        <tr>
                            <td>Status Pengguna</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($roleLabel); ?></td>
                        </tr>
                        <tr>
                            <td>Status Pekerja</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["statusPekerja"]); ?></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["username"]); ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["email"]); ?></td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($datapengguna["keterangan"]); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                        style="border-radius: 25px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php
} elseif ($action == 'edit') {
    $enumJK = getEnumValuesUser($pdo, 'jenisKelamin');
    $enumStatusPekerja = getEnumValuesUser($pdo, 'statusPekerja');
    ?>
    <!-- Modal UPDATE -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-left">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">EDIT DATA PENGGUNA</blockquote>
                        <figcaption class="blockquote-footer"><?= htmlspecialchars($datapengguna["idUser"]); ?></figcaption>
                        <figcaption class="blockquote-footer"><?= htmlspecialchars($datapengguna["nama"]) ?> - <?= htmlspecialchars($datapengguna["jbtn"]) ?></figcaption>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="idUser" value="<?= htmlspecialchars($datapengguna["idUser"]); ?>" hidden>
                        </div>
                        <div class="mb-3">
                            <label>Nama Pengguna <span class="required">*</span></label>
                            <input class="form-control" type="text" name="nama" value="<?= htmlspecialchars($datapengguna["nama"]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Kelamin <span class="required">*</span></label>
                            <select name="jenisKelamin" class="form-control" required>
                                <option value="<?= htmlspecialchars($datapengguna["jenisKelamin"]); ?>"><?= htmlspecialchars($datapengguna["jenisKelamin"]); ?></option>
                                <?php foreach ($enumJK as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Alamat <span class="required">*</span></label>
                            <textarea class="form-control" name="alamat" required><?= htmlspecialchars($datapengguna["alamat"]); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Nomor Telepon <span class="required">*</span></label>
                            <input class="form-control" type="number" name="noTelp" value="<?= htmlspecialchars($datapengguna["noTelp"]); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Status Pengguna <span class="required">*</span></label>
                                <select name="role" class="form-control" required>
                                    <option value="<?= htmlspecialchars($datapengguna["role"]); ?>"><?= htmlspecialchars($datapengguna["role"]); ?></option>
                                    <?php foreach ($enumSPMapping as $key => $label) echo '<option value="' . $key . '">' . $label . '</option>'; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Jabatan <span class="required">*</span></label>
                                <input class="form-control" type="text" name="jbtn" value="<?= htmlspecialchars($datapengguna["jbtn"]); ?>" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Tanggal Mulai Aktif <span class="required">*</span></label>
                                <input class="form-control" type="date" name="tglMulaiAktif" value="<?= htmlspecialchars($datapengguna["tglMulaiAktif"]); ?>" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Status Pekerja <span class="required">*</span></label>
                                <select name="statusPekerja" class="form-control" required>
                                    <option value="<?= htmlspecialchars($datapengguna["statusPekerja"]); ?>"><?= htmlspecialchars($datapengguna["statusPekerja"]); ?></option>
                                    <?php foreach ($enumStatusPekerja as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email <span class="required">*</span></label>
                            <input class="form-control" type="text" name="email" value="<?= htmlspecialchars($datapengguna["email"]); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Username <span class="required">*</span></label>
                                <input class="form-control" type="text" name="username" value="<?= htmlspecialchars($datapengguna["username"]); ?>" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Password <span class="required">*</span></label>
                                <input class="form-control" type="password" name="password" value="<?= htmlspecialchars($datapengguna["password"]); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="keterangan"><?= htmlspecialchars($datapengguna["keterangan"]); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="editbtn" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px;">SIMPAN</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
} elseif ($action == 'delete') {
    ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="" method="post">
                    <input type="hidden" name="hiddendeletevalue[0][field]" value="idUser">
                    <input type="hidden" name="hiddendeletevalue[0][value]" value="<?= htmlspecialchars($datapengguna["idUser"]); ?>">
                    <input type="hidden" name="hiddendeletevalue[0][table]" value="user">

                    <div class="modal-header">
                        <figure>
                            <blockquote class="blockquote">
                                <h5 class="modal-title">HAPUS</h5>
                            </blockquote>
                            <figcaption class="blockquote-footer">Pengguna <?= htmlspecialchars($datapengguna["username"]) ?></figcaption>
                        </figure>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus pengguna <strong><?= htmlspecialchars($datapengguna["username"]) ?></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="btnhapus" class="btn btn-danger btn-sm" style="border-radius: 25px;">HAPUS</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>