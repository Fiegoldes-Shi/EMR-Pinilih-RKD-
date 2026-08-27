<?php
class cInsert
{
	function vInsertDataPrepared($atable, $afields, $avalues, $types = "")
	{
		if (!isset($GLOBALS["conn"]) || !$GLOBALS["conn"]) {
			die("Koneksi ke database tidak ditemukan.");
		}

		// Generate placeholders (?, ?, ?)
		$placeholders = implode(", ", array_fill(0, count($avalues), "?"));
		$columns = implode(", ", $afields);

		$sql = "INSERT INTO " . $atable . " (" . $columns . ") VALUES (" . $placeholders . ")";

		if ($GLOBALS["conn"] instanceof mysqli) {
			$stmt = $GLOBALS["conn"]->prepare($sql);
			if (!$stmt) {
				die("Query Error: " . $GLOBALS["conn"]->error);
			}

			// Infer types if not provided
			if (empty($types)) {
				$types = "";
				foreach ($avalues as $value) {
					if (is_int($value))
						$types .= "i";
					elseif (is_double($value))
						$types .= "d";
					else
						$types .= "s";
				}
			}

			if (!empty($avalues)) {
				$stmt->bind_param($types, ...$avalues);
			}
			$result = $stmt->execute();
			$last_id = $stmt->insert_id;
			$stmt->close();
		} elseif ($GLOBALS["conn"] instanceof PDO) {
			$stmt = $GLOBALS["conn"]->prepare($sql);
			if (!$stmt) {
				die("Query Error (PDO): " . implode(" ", $GLOBALS["conn"]->errorInfo()));
			}
			try {
				$result = $stmt->execute($avalues);
				$last_id = $GLOBALS["conn"]->lastInsertId();
			} catch (PDOException $e) {
				die("Query Error (PDO Execute): " . $e->getMessage());
				$result = false;
			}
		} else {
			die("Unknown database connection type.");
		}

		echo "<br>";
		if ($result) {
			echo "<script>
                    Swal.fire({
                      position:'center',
                      width:'16em',
                      icon: 'success',
                      text: 'Data berhasil disimpan',
                      type: 'success',
                    }).then(function (result) {
                      if (true) {
                        window.location = '';
                      }
                    }) </script>";
			return $last_id;
		} else {
			echo "<script>
                    Swal.fire({
                      position:'center',
                      width:'16em',
                      icon: 'error',
                      text: 'Data tidak berhasil disimpan',
                      type: 'error',
                    }).then(function (result) {
                      if (true) {
                        window.location = '';
                      }
                    }) </script>";
			return false;
		}
	}

}
