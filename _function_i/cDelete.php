<?php
class cDelete
{
	private static $allowedDeleteTargets = [
		"hasil_layanan" => ["idHasilLayanan"],
		"jadwal_program" => ["idJadwal"],
		"program_edukasi" => ["idEdukasi"],
		"peserta_edukasi" => ["idPesertaEdukasi"],
		"terapis" => ["idTerapis"],
		"peserta" => ["idPeserta"],
		"user" => ["idUser"],
		"sub_disabilitas" => ["idSubDisabilitas", "idJenisDisabilitas"],
		"pasien" => ["idPasien"],
		"jenis_disabilitas" => ["idJenisDisabilitas"],
	];

	function vDeleteDataPrepared($table, $field, $value, $type = "")
	{
		if (!isset($GLOBALS["conn"]) || !$GLOBALS["conn"]) {
			die("Koneksi ke database tidak ditemukan.");
		}

		if (!isset(self::$allowedDeleteTargets[$table]) || !in_array($field, self::$allowedDeleteTargets[$table], true)) {
			die("Permintaan hapus data ditolak: target tidak valid.");
		}

		$sql = "DELETE FROM " . $table . " WHERE " . $field . " = ?";

		$error_msg = 'Data tidak berhasil dihapus'; // Default error message

		if ($GLOBALS["conn"] instanceof mysqli) {
			$stmt = $GLOBALS["conn"]->prepare($sql);
			if (!$stmt) {
				die("Query Error: " . $GLOBALS["conn"]->error);
			}

			if (empty($type)) {
				if (is_int($value))
					$type = "i";
				elseif (is_double($value))
					$type = "d";
				else
					$type = "s";
			}

			$stmt->bind_param($type, $value);
			try {
				$result = $stmt->execute();
			} catch (mysqli_sql_exception $e) {
				$result = false;
				if ($e->getCode() == 1451) {
					$error_msg = 'Data tidak bisa dihapus karena sedang digunakan/terkait dengan data lain (misal: riwayat layanan).';
				} else {
					$error_msg = 'Terjadi kesalahan sistem saat menghapus data.';
				}
			}
			$stmt->close();
		} elseif ($GLOBALS["conn"] instanceof PDO) {
			$stmt = $GLOBALS["conn"]->prepare($sql);
			if (!$stmt) {
				die("Query Error (PDO): " . implode(" ", $GLOBALS["conn"]->errorInfo()));
			}
			try {
				$result = $stmt->execute([$value]);
			} catch (PDOException $e) {
				$result = false;
				if ($e->getCode() == 23000) { // PDO uses SQLSTATE 23000 for Integrity constraint violation (including FK)
					$error_msg = 'Data tidak bisa dihapus karena sedang digunakan/terkait dengan data lain (misal: riwayat layanan).';
				} else {
					$error_msg = 'Terjadi kesalahan sistem saat menghapus data.';
				}
			}
		} else {
			die("Unknown database connection type.");
		}

		if ($result) {
			echo "<script>
                    Swal.fire({
                      position:'center',
                      width:'20em',
                      icon:'success',
                      text: 'Data berhasil dihapus',
                      type: 'success', // Fixed typo 'error' to 'success'
                    }).then(function (result) {
                      if (true) {
                        window.location = '';
                      }
                    }) </script>";
		} else {
			echo "<script>
                    Swal.fire({
                      position:'center',
                      width:'20em',
                      icon: 'error',
                      text: '$error_msg',
                      type: 'error',
                    }).then(function (result) {
                      if (true) {
                        window.location = '';
                      }
                    }) </script>";
		}
	}
}
