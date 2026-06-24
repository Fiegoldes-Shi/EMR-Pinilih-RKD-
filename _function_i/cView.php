<?php
class cView
{
	function vViewData($sSql)
	{
		if (!isset($GLOBALS["conn"]) || !$GLOBALS["conn"]) {
			die("Koneksi ke database tidak ditemukan.");
		}
		$data = array();

		if ($GLOBALS["conn"] instanceof mysqli) {
			$query = mysqli_query($GLOBALS["conn"], $sSql);
			if (!$query) {
				die("Query Error (mysqli): " . mysqli_error($GLOBALS["conn"]));
			}
			while ($row = mysqli_fetch_assoc($query)) {
				$data[] = $row;
			}
		} elseif ($GLOBALS["conn"] instanceof PDO) {
			try {
				$stmt = $GLOBALS["conn"]->query($sSql);
				if ($stmt) {
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$data[] = $row;
					}
				}
			} catch (PDOException $e) {
				die("Query Error (PDO): " . $e->getMessage());
			}
		} else {
			// Fallback if it's something else (e.g. wrapper class that behaves like mysqli/PDO?)
			// Try object oriented style
			try {
				$query = $GLOBALS["conn"]->query($sSql);
				if ($query instanceof mysqli_result) {
					while ($row = $query->fetch_assoc())
						$data[] = $row;
				} elseif ($query instanceof PDOStatement) {
					while ($row = $query->fetch(PDO::FETCH_ASSOC))
						$data[] = $row;
				}
			} catch (Exception $e) {
				die("Unknown database connection type.");
			}
		}
		return $data;
	}

	function vViewDataPrepared($sSql, $params = [], $types = "")
	{
		if (!isset($GLOBALS["conn"]) || !$GLOBALS["conn"]) {
			die("Koneksi ke database tidak ditemukan.");
		}

		$data = array();

		if ($GLOBALS["conn"] instanceof mysqli) {
			$stmt = $GLOBALS["conn"]->prepare($sSql);
			if (!$stmt) {
				die("Query Error: " . $GLOBALS["conn"]->error);
			}
			if (!empty($params)) {
				$stmt->bind_param($types, ...$params);
			}
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc())
				$data[] = $row;
			$stmt->close();
		} elseif ($GLOBALS["conn"] instanceof PDO) {
			$stmt = $GLOBALS["conn"]->prepare($sSql);
			if (!$stmt) {
				die("Query Error (PDO): " . implode(" ", $GLOBALS["conn"]->errorInfo()));
			}
			// For PDO, we bind values. Types are less strict/handled differently, 
			// but we can pass params to execute() directly.
			// Note: PDO execute() takes array of values.

			// If $params matches placeholders (e.g. ?), simple execute($params) works.
			// If types string was mandatory for mysqli, we ignore it for PDO simple usage or map it.
			// Since we use ? placeholders, execute($params) is sufficient.

			try {
				$stmt->execute($params);
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$data[] = $row;
				}
			} catch (PDOException $e) {
				die("Query Error (PDO Execute): " . $e->getMessage());
			}
		}

		return $data;
	}

	function vViewDataTrial($sSql)
	{
		echo "<p>";
		echo $sSql;
		echo "</p>";
	}
}
