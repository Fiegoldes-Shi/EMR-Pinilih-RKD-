<?php
class cUpdate
{
	function vUpdateDataPrepared($atable, $afields, $avalues, $whereCol, $whereVal, $types = "")
	{
		if (!isset($GLOBALS["conn"]) || !$GLOBALS["conn"]) {
			die("Koneksi ke database tidak ditemukan.");
		}

		// Generate SET clause: col1=?, col2=?
		$setClauseArr = [];
		foreach ($afields as $field) {
			$setClauseArr[] = $field . "=?";
		}
		$setClause = implode(", ", $setClauseArr);

		$sql = "UPDATE " . $atable . " SET " . $setClause . " WHERE " . $whereCol . " = ?";

		// Combine params: values + whereVal
		$params = $avalues;
		$params[] = $whereVal;

		if ($GLOBALS["conn"] instanceof mysqli) {
			$stmt = $GLOBALS["conn"]->prepare($sql);
			if (!$stmt) {
				die("Query Error: " . $GLOBALS["conn"]->error);
			}

			// Infer types if not provided
			if (empty($types)) {
				$types = "";
				foreach ($params as $param) {
					if (is_int($param))
						$types .= "i";
					elseif (is_double($param))
						$types .= "d";
					else
						$types .= "s";
				}
			}

			$stmt->bind_param($types, ...$params);
			$result = $stmt->execute();
			$stmt->close();
		} elseif ($GLOBALS["conn"] instanceof PDO) {
			$stmt = $GLOBALS["conn"]->prepare($sql);
			if (!$stmt) {
				die("Query Error (PDO): " . implode(" ", $GLOBALS["conn"]->errorInfo()));
			}
			try {
				$result = $stmt->execute($params);
			} catch (PDOException $e) {
				die("Query Error (PDO Execute): " . $e->getMessage());
				$result = false;
			}
		} else {
			die("Unknown database connection type.");
		}


		if ($result) {
			echo "<script>
                    Swal.fire({
                      position:'center',
                      width:'16em',
                      icon: 'success',
                      text: 'Data berhasil diubah',
                      type: 'success',
                    }).then(function (result) {
                      if (true) {
                        window.location = '';
                      }
                    }) </script>";
		} else {
			echo "<script>
                    Swal.fire({
                      position:'center',
                      width:'16em',
                      icon: 'error',
                      text: 'Data tidak berhasil diubah',
                      type: 'error',
                    }).then(function (result) {
                      if (true) {
                        window.location = '';
                      }
                    }) </script>";
		}
	}

}
