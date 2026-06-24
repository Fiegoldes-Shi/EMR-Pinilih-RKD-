<?php
function _myobject($object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled)
{
	switch ($object) {

		case 1: //textbox
?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $disabled; ?>>
			</div>
		<?php
			break;

		case 12: //textbox required
?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $disabled; ?> <?= $required ?>>
			</div>
		<?php
			break;

		case 11111:	
?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="password" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $disabled; ?> <?= $required; ?>>
			</div>
		<?php
			break;


		case 11:	// textbox disabled
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="text" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" disabled <?= $required; ?>>
				<input type="hidden" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= $value; ?>">
			</div>
		<?php
			break;

		case 111:	// textbox for number required
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input type="number" name="<?= $name; ?>" value="<?= $value; ?>" id="<?= $id; ?>" class="form-control" placeholder="<?= $placeholder; ?>" aria-label="Username" aria-describedby="basic-addon1" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>" <?= $required; ?>>
			</div>
		<?php
			break;

		case 1113:	// textbox for number
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input type="number" name="<?= $name; ?>" value="<?= $value; ?>" id="<?= $id; ?>" class="form-control" placeholder="<?= $placeholder; ?>" aria-label="Username" aria-describedby="basic-addon1" maxlength="<?= $maxlength; ?>" size="<?= $size; ?>">
			</div>
		<?php
			break;

		case 13:	// upload file
		?>
			<div class="mb-3">
				<label for="formFile" class="form-label"><?= strtoupper($caption); ?></label>
				<input class="form-control" type="file" name="dokumentasi" id="dokumentasi" accept=".jpg,.jpeg,.png,.pdf,.zip,.rar">
			</div>
		<?php
			break;

		case 14:	// date required
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="date" id="<?= $id; ?>" name="<?= $name; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>" <?= $required; ?>>
			</div>
		<?php
			break;

		case 143:	// date
		?>
			<div class="mb-3">
				<label for="<?= $id; ?>"><?= $caption; ?></label>
				<input class="form-control" type="date" id="<?= $id; ?>" name="<?= $name; ?>" value="<?= $value; ?>" placeholder="<?= $placeholder; ?>">
			</div>
		<?php
			break;

		case 2:		// textbox hidden	
		?>
			<input type="hidden" name="<?= $name; ?>" value="<?= $value; ?>" id="<?= $id; ?>" class="form-control" placeholder="<?= $placeholder; ?>">
			<p></p>
		<?php
			break;

		case 23:	// upload file
			echo '<input type="file" name="' . $name . '" class="' . $class . '" id="' . $id . '" value="' . $value . '">';
			break;

		case 3:		// textarea
		?>
			<label for="<?= $id; ?>"><?= $caption; ?></label>
			<textarea class="form-control" id="<?= $id; ?>" name="<?= $name; ?>" placeholder="<?= $placeholder; ?>" rows="<?= $rows; ?>" cols="<?= $cols; ?>" id="floatingTextarea"><?= $value; ?></textarea>
			<br>
		<?php
			break;

		case 32:		// textarea required
		?>
			<label for="<?= $id; ?>"><?= $caption; ?></label>
			<textarea class="form-control" id="<?= $id; ?>" name="<?= $name; ?>" placeholder="<?= $placeholder; ?>" rows="<?= $rows; ?>" cols="<?= $cols; ?>" id="floatingTextarea" <?= $required; ?>><?= $value; ?></textarea>
			<br>
		<?php
			break;

		case 4:		// radio
		?>
			<?php
			$sql = "SELECT keterangan field1, keterangan field2 FROM akunjenis";
			$view = new cView();
			$arraypilihan = $view->vViewData($sql);
			$x = 0;
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				if (!empty($value)) {
					$checked = "checked";
				} else {
					$checked = "";
				}

				if ($x == 1) {
			?>
					<label for="<?= $id; ?>"><?= $caption; ?></label>
					<br>
				<?php
				}
				?>
				<div class="form-check">
					<input class="form-check-input" id="<?= $id; ?>" name="<?= $name; ?>" type="radio" id="inlineCheckbox1" value="<?= $value; ?>" $checked>
					<label class="form-check-label" for="inlineCheckbox1"><?= $datapilihan["field1"]; ?></label>
				</div>
			<?php
			}
			?>
			<p></p>
		<?php
			break;

		case 41:	// radio inline
		?>
			<?php
			$sql = "SELECT keterangan field1, keterangan field2 FROM akunjenis";
			$view = new cView();
			$arraypilihan = $view->vViewData($sql);
			$x = 0;
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				if ($value == $datapilihan["field1"]) {
					$checked = "checked";
				} else {
					$checked = "";
				}

				if ($x == 1) {
			?>
					<label for="<?= $id; ?>"><?= $caption; ?></label>
					<br>
				<?php
				}
				?>
				<div class="form-check form-check-inline">
					<input class="form-check-input" id="<?= $id; ?>" name="<?= $name; ?>" type="radio" id="inlineCheckbox1" value="<?= $datapilihan["field2"]; ?>" <?= $checked; ?>>
					<label class="form-check-label" for="inlineCheckbox1"><?= $datapilihan["field1"]; ?></label>
				</div>
			<?php
			}
			?>
			<p></p>
		<?php
			break;

		case 5:  //select combo
			$view = new cView();
			$view->vViewData($caption);
			$arraypilihan = $view->vViewData($caption);
			$x = 0;
			$data[0][0] = "";
			$data[0][1] = "- pilihan -";
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>
			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>">
				<?php
				for ($i = 0; $i <= $x; $i++) {
					if ($data[$i][0] == $value) {
				?>
						<option value="<?php echo $data[$i][0]; ?>" selected><?php echo $data[$i][1]; ?></option>
					<?php } else { ?>
						<option value="<?php echo $data[$i][0]; ?>"><?php echo $data[$i][1]; ?></option>
				<?php }
				} ?>
			</select><br>
		<?php
			break;

		case 52:  //select combo required
			$view = new cView();
			$view->vViewData($caption);
			$arraypilihan = $view->vViewData($caption);
			$x = 0;
			$data[0][0] = "";
			$data[0][1] = "- pilihan -";
			foreach ($arraypilihan as $datapilihan) {
				$x = $x + 1;
				$data[$x][0] = $datapilihan["field1"];
				$data[$x][1] = $datapilihan["field2"];
			}
		?>
			<select name="<?php echo $name; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>" <?php echo $required; ?>>
				<?php
				for ($i = 0; $i <= $x; $i++) {
					if ($data[$i][0] == $value) {
				?>
						<option value="<?php echo $data[$i][0]; ?>" selected><?php echo $data[$i][1]; ?></option>
					<?php } else { ?>
						<option value="<?php echo $data[$i][0]; ?>"><?php echo $data[$i][1]; ?></option>
				<?php }
				} ?>
			</select><br>
		<?php
			break;

		case 8:		// checkbox
			?>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="<?= $name; ?>" value="">
					<label class="form-check-label" for="inlineCheckbox1"><?= $caption; ?></label>
				</div>
			<?php
				break;
			
			
		case 9:		// label
		?>
			<label for=""><?= $caption; ?></label>
			<br>
		<?php
			break;

		case 91:	// break
		?>
			<br>
	<?php
			break;
	}
}

function _myHeader($header, $footer)
{
	?>
	<figure>
		<blockquote class="blockquote">
			<p><?= $header; ?></p>
		</blockquote>
		<figcaption class="blockquote-footer">
			<?= $footer; ?>
		</figcaption>
	</figure>
<?php
}

//function _mytable($object,$class,$id,$width,$align,$valign,$value,$rowspan,$colspan) {
function _mytable($object, $class, $id, $width, $align, $valign, $value)
{
	switch ($object) {
		case "table":
			echo '<div class="table-responsive">';
			echo '<table class="' . $class . '" id="' . $id . '" width="' . $width . '" ' . $value . '>';
			break;
		case "th":
			if (empty($colspan)) {
				echo '<th class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</th>';
			} else {
				echo '<th class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '" colspan=' . $colspan . '>' . $value . '</th>';
			}
			break;
		case "tr":		// open tr
			echo '<tr class="' . $class . '">';
			break;
		case "td":		// td
			switch ($align) {
				case "c":
					$align = "center";
					break;
				case "r":
					$align = "right";
					break;
				case "":
					$align = "left";
					break;
			}
			switch ($valign) {
				case "":
					$valign = "top";
					break;
				case "m":
					$align = "midle";
					break;
				case "b":
					$align = "bottom";
					break;
			}
			if (empty($colspan)) {
				if (empty($rowspan)) {
					echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</td>';
				}
			} else {
				if (empty($rowspan)) {
					echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '" colspan=' . $colspan . '>' . $value . '</td>';
				} else {
					echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '" rowspan=' . $rowspan . '>' . $value . '</td>';
				}
			}
			break;
		case "/tr":		// close tr
			echo '</tr>';
			break;
		case "/table":	// close table
			echo '</table>';
			echo '</div>';
			break;
	}
}

// mytable_coloumn
function _mytable_coloumn($object, $class, $id, $width, $align, $valign, $value, $caption)
{
	switch ($object) {
		case "table":
			echo '<div class="table-responsive">';
			echo '<table class="' . $class . '" id="' . $id . '" width="' . $width . '" ' . $value . '>';
			break;
		case "th":
			echo '<th class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</th>';
			break;
		case "tr":		// open tr
			echo '<tr class="' . $class . '">';
			break;
		case "td":		// td
			switch ($align) {
				case "c":
					$align = "center";
					break;
				case "r":
					$align = "right";
					break;
				case "":
					$align = "left";
					break;
			}
			switch ($valign) {
				case "":
					$valign = "top";
					break;
				case "m":
					$align = "midle";
					break;
				case "b":
					$align = "bottom";
					break;
			}
			echo '<tr>';
			$width_value = $width - 5;
			echo '<td class="' . $class . '" id="' . $id . '" width="' . $width . '" align="' . $align . '" valign="' . $valign . '">' . $caption . '</td>';
			echo '<td class="' . $class . '" id="' . $id . '" width="2%" align="center" valign="' . $valign . '">' . ':' . '</td>';
			echo '<td class="' . $class . '" id="' . $id . '" width="' . $width_value . '" align="' . $align . '" valign="' . $valign . '">' . $value . '</td>';
			echo '</tr>';
			break;
		case "/tr":		// close tr
			echo '</tr>';
			break;
		case "/table":	// close table
			echo '</table>';
			echo '</div>';
			break;
	}
}
?>

<?php
// create window modal
function _CreateModalInsert($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	// $footer_field = count($footer) - 1;
	$count_field = count($afield) - 1;
?>
	<button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center" 
        data-bs-toggle="modal" data-bs-target="#exampleModal" 
        style="border-radius: 10px; width: 50%; height: 60%; display: flex;">            
		<i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>
    </button>

	<!-- Modal -->

	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title fs-5" id="exampleModalLabel">
						<blockquote class="blockquote">
							<p><?= $acaption[1]; ?></p>
						</blockquote>
					</h3>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form class="" method="post" action="<?php echo $linkurl; ?>" enctype="multipart/form-data">
					<div class="modal-body">
						<?php
						for ($i = 0; $i <= $count_field; $i++) {
							switch ($afield[$i][3]) {

									// $object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled

								case 1:		// textbox
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 12:		// textbox required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 1113:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 11111:	// password
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][1], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 143:	// date
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][1], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", "", strtoupper($afield[$i][0]), "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 32:		// textarea required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", "", strtoupper($afield[$i][0]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 52:		// select required
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								
								case 6: // Select / ComboBox
									echo '<label for="' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									echo '<select name="' . $afield[$i][1] . '" class="form-control">';
									echo '<option value="">' . "- pilihan -" . '</option>';
									if ($afield[$i][1] === 'role') {
										foreach ($afield[$i][4] as $key => $label) {
											echo '<option value="' . $key . '">' . $label . '</option>';
										}
									} else {
										foreach ($afield[$i][4] as $option) {
											$trimmedValue = trim($option);
											echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
										}
									}
									echo '</select><br>';
								break;

								case 62: // Select / ComboBox required
									echo '<label for="' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									echo '<select name="' . $afield[$i][1] . '" class="form-control" required>';
									echo '<option value="">' . "- pilihan -" . '</option>';
									if ($afield[$i][1] === 'role') {
										foreach ($afield[$i][4] as $key => $label) {
											echo '<option value="' . $key . '">' . $label . '</option>';
										}
									} else {
										foreach ($afield[$i][4] as $option) {
											$trimmedValue = trim($option);
											echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
										}
									}
									echo '</select><br>';
								break;

								case 7: // Checkbox
									echo '<label>' . strtoupper($afield[$i][0]) . '</label><br>';
									
									// Pastikan data tersedia
									$arrayPilihan = is_array($afield[$i][4]) ? $afield[$i][4] : [];
								
									// Cek apakah ada nilai yang sudah tersimpan
									$selectedValues = is_string($afield[$i][2]) ? explode(", ", $afield[$i][2]) : [];
								
									echo '<div class="row">';
									
									$jumlahKolom = 3;
									$totalPilihan = count($arrayPilihan);
									$pilihanPerKolom = ceil($totalPilihan / max(1, $jumlahKolom)); // Hindari pembagian dengan nol
									
									$x = 0;
									foreach ($arrayPilihan as $index => $pilihan) {
										$x++;
										$checkboxId = $afield[$i][1] . "_" . $x; // ID unik untuk tiap checkbox
										$checked = in_array($pilihan, $selectedValues) ? "checked" : "";
								
										// Mulai kolom baru jika perlu
										if ($index % $pilihanPerKolom == 0) {
											echo '<div class="col-md-4">';
										}
										
										echo '<div class="form-check">';
										echo '<input class="form-check-input" type="checkbox" id="' . $checkboxId . '" name="' . $afield[$i][1] . '[]" value="' . $pilihan . '" ' . $checked . '>';
										echo '<label class="form-check-label" for="' . $checkboxId . '">' . $pilihan . '</label>';
										echo '</div>';
								
										// Tutup div kolom jika sudah mencapai batas per kolom
										if (($index + 1) % $pilihanPerKolom == 0 || $index == $totalPilihan - 1) {
											echo '</div>';
										}
									}
								
									echo '</div><br>';
									break;
								

								case 8:  // checkbox inline
									//case type, field name, class, id, placeholder, caption, maxlength, size, rows,cols,required, pilihan, nilai tersimpan
									_myobject($afield[$i][3],  $afield[$i][1],"form-control", $afield[$i][1], "", trim($afield[$i][0]), "", "", "", "", "", $afield[$i][4], $afield[$i][2]);
									break;

								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							}
						}
						?>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;" name="savebtn" value="true">Simpan</button>
						<button type="reset" class="btn btn-warning btn-sm" style="border-radius: 25px;" name="" value="true">Ulang</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">Tutup</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
// end of function _CreateModalInsert
?>


<?php
// create window detil
function _CreateWindowModalDetil($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl, $footer)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

	$number = $type . $name . $number;
	$count_field = count($afield) - 1;
?>
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#formview<?= $number; ?>" style="border-radius: 8px;">
		<i class="fa-regular fa-eye" style="color: #000000;"></i>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formview<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg text-start">
			<div class="modal-content">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?php
					_mytable("table", "table table-condensed", "", "100%", "", "", "");
					for ($i = 0; $i <= $count_field; $i++) {
						_mytable("tr", "", "", "", "", "", "");
						_mytable("td", "", "", "29%", "l", "", $afield[$i][0]);
						_mytable("td", "", "", "1%", "c", "", ":");
						_mytable("td", "", "", "70%", "l", "", $afield[$i][2]);
						_mytable("/tr", "", "", "", "", "", "");
					}
					_mytable("/table", "", "", "", "", "", "");
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">Tutup</button>
				</div>
			</div>
		</div>
	</div>
<?php

}


function _CreateWindowModalDetilxx($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl, $footer)
{
	$number = $type . $name . $number;
	$count_field = count($afield) - 1;

	echo '<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#myModal' . $number . '" style="border-radius:25px;">&nbsp;<ion-icon name="search-sharp"></ion-icon>&nbsp;';
	echo '</button>';
	echo '<FORM method="post" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg text-left">
			<div class="modal-content">
				<div class="modal-header">
					<blockquote class="blockquote">
						<p class="mb-0"><?php echo $title; ?></p>
						<?php
						for ($baris = 0; $baris <= $count_field; $baris++) {
							echo '<footer class="blockquote-footer">' . $footer[$baris] . '</footer>';
						}
						?>
					</blockquote>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php
					_mytable("table", "table table-condensed", "", "100%", "", "", "");
					for ($i = 0; $i <= $count_field; $i++) {
						_mytable("tr", "", "", "", "", "", "");
						_mytable("td", "", "", "29%", "l", "", $afield[$i][0]);
						_mytable("td", "", "", "1%", "c", "", ":");
						_mytable("td", "", "", "70%", "l", "", $afield[$i][2]);
						_mytable("/tr", "", "", "", "", "", "");
					}
					_mytable("/table", "", "", "", "", "", "");
					?>
				</div>
				<div class="modal-footer">
					<?php
					echo '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">TUTUP</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</FORM>';
}

// create window upload
function _CreateWindowModalUpload($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	$number = $type . $name . $number;
	$count_field   = count($afield) - 1;
	echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal' . $number . '"><ion-icon name="cloud-upload-outline"></ion-icon>';
	echo '</button>';
	echo '<FORM method="post" enctype="multipart/form-data" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<blockquote><?php echo $title; ?></blockquote>
					</h4>
				</div>
				<div class="modal-body">
					<?php
					//echo "jumlah field".$count_field."<br>";
					for ($i = 0; $i <= $count_field; $i++) {
						//echo "0".$afield[$i][0]." 1".$afield[$i][1]." 2".$afield[$i][2]." 3".$afield[$i][3];
						switch ($afield[$i][3]) {
							case 1:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 2:
								echo '<div class="form-group">';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;

							case 5:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], "", trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 13:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 14:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
						} // end of switch
					} // end of for
					?>
				</div>
				<div class="modal-footer">
					<?php
					echo '<button class="btn btn-primary btn-sm" type="submit" name="upload" value="upload">UPLOAD</button>';
					echo '<button type="reset" class="btn btn-primary btn-sm" name="edit" value="RESET">RESET</button>&nbsp;&nbsp;';
					echo '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</FORM>';
}



// create window 
function _CreateWindowModalAdd($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	$number = $type . $name . $number;
	$count_field   = count($afield) - 1;
	echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal' . $number . '">&nbsp;<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;';
	echo '</button>';
	echo '<FORM method="post" enctype="multipart/form-data" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<blockquote>
							<p><?php echo $title; ?></p>
						</blockquote>
					</h4>
				</div>
				<div class="modal-body">
					<?php
					for ($i = 0; $i <= $count_field; $i++) {
						switch ($afield[$i][3]) {
							case 1:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 13:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled)
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 2:
								echo '<div class="form-group">';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 3:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 4:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 5:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 6:
								echo '<div class="form-grup">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "", trim($afield[0][4]), "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 8:
								echo '<label class="checkbox-inline">';
								//_myobject($object,$name,$class,$id,$value,$caption,$maxlength,$size,$rows,$cols,$required,$placeholder,$disabled) {
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");

								//_myobject($afield[$i][3],$afield[$i][1],"","inlineCheckbox1","1",strtoupper($afield[$i][0]),"","","","","",strtolower($afield[$i][0]),"","RENCANA  PELAKSANAAN","")		;
								echo '</label>';
								break;
							case 11:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 14:  // date
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 111:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
							case 1111:
								echo '<div class="form-group">';
								echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
								_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], $afield[$i][4], "", "20", "", "", "", strtolower($afield[$i][0]), "");
								echo '</div>';
								break;
						} // end of switch
					} // end of for
					?>
				</div>
				<div class="modal-footer">
					<?php
					echo '<button class="btn btn-primary btn-sm" type="submit" name="save" value="INSERT">SAVE</button>';
					echo '<button type="reset" class="btn btn-primary btn-sm" name="edit" value="RESET">RESET</button>&nbsp;&nbsp;';
					echo '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</FORM>';
}
?>
<?php
function _CreateWindowModalUpdate($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}
	$number = $type . $name . $number;
	$count_field = count($afield) - 1;
?>
	<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#formedit<?= $number; ?>" style="border-radius: 8px;">
		<i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formedit<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg text-start">
			<div class="modal-content text-left">
				<div class="modal-header">
					<figure class="text-left">
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						}
						?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<FORM method="post" enctype="multipart/form-data" action="<?= $linkurl; ?>">
					<div class="modal-body">
						<?php
						for ($i = 0; $i <= $count_field; $i++) {
							switch ($afield[$i][3]) {

									// $object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled

								case 1:		// textbox
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 12:		// textbox required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 11:	// disable
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 111:	// numeric required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 1113:	// numeric
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 11111:	// password
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 13:	// updload
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 14:	// date required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 143:	// date
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], $afield[$i][0], "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 2:		// hidden value
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "", $afield[$i][2], "", "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 3:		// textarea
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], strtoupper($afield[$i][0]), "", "", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 32:		// textarea required
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], strtoupper($afield[$i][0]), "", "", "", "", "required", strtolower($afield[$i][0]), "");
									break;

								case 4:		// radio
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 41:	// radio inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 5:		// select
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "", strtolower($afield[$i][0]), "");
									break;

								case 52:		// select required
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][4]), "", "20", "", "", "required", strtolower($afield[$i][0]), "");
									break;
								
								case 6: // Select / ComboBox
									echo '<label for="' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									echo '<select name="' . $afield[$i][1] . '" class="form-control">';
									echo '<option value="'. $afield[$i][2] .'">' . $afield[$i][2] . '</option>';
									if ($afield[$i][1] === 'role') {
										foreach ($afield[$i][4] as $key => $label) {
											echo '<option value="' . $key . '">' . $label . '</option>';
										}
									} else {
										foreach ($afield[$i][4] as $option) {
											$trimmedValue = trim($option);
											echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
										}
									}
									echo '</select><br>';
									break;

								case 62: // Select / ComboBox required
									echo '<label for="' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									echo '<select name="' . $afield[$i][1] . '" class="form-control" required>';
									echo '<option value="'. $afield[$i][2] .'">' . $afield[$i][2] . '</option>';
									if ($afield[$i][1] === 'role') {
										foreach ($afield[$i][4] as $key => $label) {
											echo '<option value="' . $key . '">' . $label . '</option>';
										}
									} else {
										foreach ($afield[$i][4] as $option) {
											$trimmedValue = trim($option);
											echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
										}
									}
									echo '</select><br>';
									break;
								
								case 7: // Checkbox
									echo '<label>' . strtoupper($afield[$i][0]) . '</label><br>';
									
									// Pastikan data tersedia
									$arrayPilihan = is_array($afield[$i][4]) ? $afield[$i][4] : [];
								
									// Cek apakah ada nilai yang sudah tersimpan
									$selectedValues = is_array($afield[$i][2]) ? $afield[$i][2] : [];
								
									echo '<div class="row">';
									
									$jumlahKolom = 3;
									$totalPilihan = count($arrayPilihan);
									$pilihanPerKolom = ceil($totalPilihan / max(1, $jumlahKolom)); // Hindari pembagian dengan nol
									
									$x = 0;
									foreach ($arrayPilihan as $index => $pilihan) {
										$x++;
										$checkboxId = $afield[$i][1] . "_" . $x; // ID unik untuk tiap checkbox
										$checked = in_array($pilihan, $selectedValues) ? "checked" : "";
								
										// Mulai kolom baru jika perlu
										if ($index % $pilihanPerKolom == 0) {
											echo '<div class="col-md-4">';
										}
										
										echo '<div class="form-check">';
										echo '<input class="form-check-input" type="checkbox" id="' . $checkboxId . '" name="' . $afield[$i][1] . '[]" value="' . $pilihan . '" ' . $checked . '>';
										echo '<label class="form-check-label" for="' . $checkboxId . '">' . $pilihan . '</label>';
										echo '</div>';
								
										// Tutup div kolom jika sudah mencapai batas per kolom
										if (($index + 1) % $pilihanPerKolom == 0 || $index == $totalPilihan - 1) {
											echo '</div>';
										}
									}
								
									echo '</div><br>';
									break;	

								case 8:		// checkbox inline
									_myobject($afield[$i][3], $afield[$i][1], "form-control", $afield[$i][1], $afield[$i][2], trim($afield[$i][0]), "", "", "", "", "", trim($afield[$i][4]), "");
									break;

								case 23:	// upload
									echo '<div class="form-group">';
									echo '<label for="caption' . $i . '">' . strtoupper($afield[$i][0]) . '</label>';
									_myobject($afield[$i][3], $afield[$i][1], "form-control", "caption" . $i, $afield[$i][2], "", "", "20", "", "", $afield[$i][4], strtolower($afield[$i][0]), "");
									echo '</div>';
									break;

								case 9:		// label
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;

								case 91:	// bre
									_myobject($afield[$i][3], "", "", "", "", $afield[$i][0], "", "", "", "", "", "", "");
									break;
							}
						}
						?>
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
}
?>

<?php
function _CreateWindowModalDelete($number, $type, $name, $button, $width, $height, $title, $acaption, $value, $linkurl)
{
	// get title
	$titledetil = explode('#', $title);
	$countcolum = count($titledetil);
	$number = $type . $name . $number;

	// get width
	if (empty($width)) {
		$modalsize = '';
	} elseif ($width == 'xl') {
		$modalsize = 'modal-xl';
	} elseif ($width == 'lg') {
		$modalsize = 'modal-lg';
	} elseif ($width == 'sm') {
		$modalsize = 'modal-sm';
	} elseif ($width == 'xs') {
		$modalsize = 'modal-xs';
	}

?>
	<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#formdelete<?= $number; ?>" style="border-radius: 8px;">
		<i class="fa-solid fa-trash" style="color: #ffffff;"></i>
	</button>

	<!-- Modal -->
	<div class="modal fade" id="formdelete<?= $number; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header text-start">
					<figure>
						<blockquote class="blockquote">
							<p><?= $titledetil[0]; ?></p>
						</blockquote>
						<?php
						if (!empty($titledetil[1])) {
							for ($k = 1; $k < $countcolum; $k++) {
						?>
								<figcaption class="blockquote-footer">
									<?= $titledetil[$k]; ?>
								</figcaption>
						<?php
							}
						} ?>
					</figure>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body text-start">
					<h6 class="">Yakin akan menghapus <ion-icon name="help-outline"></ion-icon></h6>
				</div>
				<form action="" method="post">
					<div class="modal-footer">
						<?php foreach ($value as $key => $val) { ?>
							<input type="hidden" name="hiddendeletevalue[<?= $key ?>][field]" value="<?= $val[0] ?>">
							<input type="hidden" name="hiddendeletevalue[<?= $key ?>][value]" value="<?= $val[1] ?>">
							<input type="hidden" name="hiddendeletevalue[<?= $key ?>][table]" value="<?= $val[2] ?>">
						<?php } ?>
						<button type="submit" name="btnhapus" value="true" class="btn btn-danger btn-sm" style="border-radius: 25px;">HAPUS</button>
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
?>

<?php
// create window view file
function _CreateWindowModalViewFile($number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl)
{
	//echo $linkurl;
	$number = $type . $name . $number;
	$count_field   = count($afield) - 1;
	echo $count_field;
	echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal' . $number . '">&nbsp;<span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;';
	echo '</button>';
	echo '<FORM method="post" action="' . $linkurl . '">';
?>
	<div class="modal fade" id="myModal<?php echo $number; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
				</div>
				<div class="modal-body">
					Yakin akan menghapus data ini ?
				</div>
				<div class="modal-footer">
					<?php
					echo "-----" . $value[0][3] . "<br>";

					for ($k = 0; $k <= $count_field; $k++) {
						echo "-----" . $value[0][$k] . "<br>";
						echo '<input type="hidden" name="hiddendeletevalue' . $k . '" value="' . $value[0][$k] . '">';
					}
					echo '<button class="btn btn-danger btn-sm" type="submit" name="del" value="DELETE">DELETE</button>&nbsp;&nbsp;';
					echo '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>';
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	echo '</form>';
}
// end of window modal delete
function UploadTruePdf($field, $path)
{
	$docfilename    = $_FILES[$field]["name"];
	$docfilesize    = $_FILES[$field]["size"];
	$docfileerror   = $_FILES[$field]["error"];
	$docfiletype    = strtolower(pathinfo($docfilename, PATHINFO_EXTENSION));
	$docfilecheck   = getimagesize($_FILES[$field]['tmp_name']);
	$docfilenewname = $_POST[$field] . "." . $docfiletype;
	if ($docfiletype == "pdf") {
		$status_upload = 1;
	} else {
		$status_upload = 0;
	}

	if ($docfilesize > 0 || $docfileerror == 0) {
		$status_size = 1;
	} else {
		$status_size = 0;
	}
	$statustrue = $status_upload . $status_size;

	$status_all = array($field, $statustrue, $path, $docfilenewname, $docfilename);
	return $status_all;
}


function _createpage($l, $range, $page, $table, $rpp)
{
	$res = "SELECT count(*) as recnumber FROM " . $table . " ";
	//echo $res."<br>";
	$view = new cView();
	$view->vViewData($res);
	$arrayView = $view->vViewData($res);
	foreach ($arrayView as $dataarray) {
		$maxrows = $dataarray["recnumber"];
		$pages = ceil($maxrows / $rpp);
	}

	//$maxrows = (int)@mysqli_fetch_array($res, 0);
	//$pages = ceil($maxrows/$rpp);

	if ($maxrows > $rpp) {
		echo '<nav>';
		echo '<ul class="pagination">';
		echo '<li><a href="?l=' . $l . '&pg=1" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
		for ($i = ($page - $range); $i < (($page + $range) + 1); $i++) {
			if (($i > 0) && ($i <= $pages)) {
				if ($i == $page + 1) {
					echo '<li class="active"><a href="#">' . ($i) . '</a></li>';
				} else {
					echo '<li><a href="?l=' . $l . '&pg=' . $i . '">' . ($i) . '</a></li>';
				}
			}
		}
		$last = $pages;
		echo '<li><a href="?l=' . $l . '&pg=' . $last . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
		echo '</ul>';
		echo '</nav>';
	}
}

function _BackButton($url)
{
	echo '<ul class="pagination pagination-sm">';
	echo '<li class="page-item"><a class="page-link" href="' . $url . '" style="border-radius:30px; border:0.5px solid #c0c0c0">
      <ion-icon name="caret-back-sharp"></ion-icon>&nbsp;BACK&nbsp;</a></li>';
	echo '</ul>';
}

function _DateFormat($dformat, $ddate)
{
	if (empty(trim($ddate))) {
		$returndate = "";
	} else {
		$returndate = date('d-m-Y', strtotime($ddate));
	}
	if ($returndate == '30-11--0001') {
		$returndate = "";
	}
	return $returndate;
}

function _ShowHeader($header, $smallheader)
{
	if (empty(trim($smallheader))) {
		echo '<blockquote>' . $header . '</blockquote>';
	} else {
		echo '<blockquote>' . $header . '<small>' . $smallheader . '</small></blockquote>';
	}
}

function _CreateTabs($caption, $link, $active)
{
	echo '<li role="presentation" class="' . $active . '"><a href="' . $link . '">' . $caption . '</a></li>';
}

function _CreateNavsData($sql, $type)
{
	echo "<br>" . $sql . "<br>";
	$view = new cView();
	$arraysql = $view->vViewData($sql);
	if (empty($_GET["id"])) {
		$_GET["id"] = "0";
	} else {
		$_GET["id"] = $_GET["id"];
	}
	echo $_GET["id"];
?>
	<ul class="nav nav-tabs">
		<?php
		if ($_GET["id"] == 0) {
			$cactive = "active";
		} else {
			$cactive = "";
		}
		?>
		<?php
		if (empty($type)) {
		?>
			<li role="presentation" class="<?php echo $cactive; ?>"><a href="?l=<?php echo $_GET["l"]; ?>&id=0">Semua</a></li>
		<?php
		}
		?>
		<?php
		foreach ($arraysql as $datasql) {
			if ($_GET["id"] == $datasql["field1"]) {
				$cactive = "active";
			} else {
				$cactive = "";
			}
		?>
			<li role="presentation" class="<?php echo $cactive; ?>"><a href="?l=<?php echo $_GET["l"]; ?>&id=<?php echo $datasql["field1"]; ?>"><?php echo $datasql["field2"]; ?></a></li>
		<?php } ?>
	</ul>
	<p><br></p>
<?php
}


function _DefaultValueCheckBox($variabel)
{

	if (!isset($_POST[$variabel])) {
		$_POST[$variabel] = "";
	} else {
		$_POST[$variabel] = "on";
	}

	if (trim($_POST[$variabel]) == 'on') {
		$returnvalue = 1;
	} else {
		$returnvalue = 0;
	}

	return $returnvalue;
}

function _ShowHari($value)
{
	$hari = substr($value, 3, strlen($value));
	return $hari;
}



function UploadTrue($field, $path)
{
	$docfilename    = $_FILES[$field]["name"];
	$docfilesize    = $_FILES[$field]["size"];
	$docfileerror   = $_FILES[$field]["error"];
	$docfiletype    = strtolower(pathinfo($docfilename, PATHINFO_EXTENSION));
	$docfilecheck   = getimagesize($_FILES[$field]['tmp_name']);
	$docfilenewname = $_POST[$field] . ".pdf";

	if ($docfiletype == "pdf") {
		$status_upload = 1;
	} else {
		$status_upload = 0;
	}

	if ($docfilesize > 0 || $docfileerror == 0) {
		$status_size = 1;
	} else {
		$status_size = 0;
	}
	$statustrue = $status_upload . $status_size;

	$status_all = array($field, $statustrue, $path, $docfilenewname);
	return $status_all;
}


function _CreateButtonViewPdf($id, $name)
{
	$name = $name . $id;
?>

<?php
	echo '<button id="' . $id . '" onclick="reply_click(this.id)" class="btn btn-info" data-toggle="modal" data-target="' . $name . '" data-placement="bottom" title="" value="del"><span class="glyphicon glyphicon-download"></span></button>';

	//echo '<button id="'.$dataarray["namapath"].$dataarray["namafile"].'" onclick="reply_click(this.id)" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalDelete-JAFA-'.$cnourut.'" data-placement="bottom" title="DELETE SK"><span class="glyphicon glyphicon-trash"></span></button>'
}


function _CreateModalPDF($id, $pdf)
{
	//echo $id." ".$pdf;
	//echo $pdf;
	//echo $id."<br>";
?>
	<div class="modal fade" id="exampleModal<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<embed alt='Alt text for embed' src="<?php echo $pdf ?>" width="100%" height="500" type='application/pdf'>
					<!--  <div id="pdf"></div>  -->
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
<?php
}


function _CreateModalRemoveFilePDF($id, $pdf, $linkurl, $idpk, $jenis)
{
?>

	<div class="modal fade" id="exampleModal<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">DELETE FILE</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="<?php echo $linkurl ?>">
						<div class="modal-body">
							Yakin akan menghapus File ini ?
							<input type="hidden" name="file" value="<?php echo $pdf ?>">
							<input type="hidden" name="jenis" value="<?php echo $jenis ?>">
							<input type="hidden" name="idpk" value="<?php echo $idpk ?>">

						</div>
						<div class="modal-footer">
							<button class="btn btn-danger btn-sm" type="submit" name="del" value="DELETE">DELETE</button>&nbsp;
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">CLOSE</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
<?php
}

function _CariValue($sql)
{
	//echo "<br>".$sql."<br>";
	$view = new cView();
	$arrayView = $view->vViewData($sql);
	foreach ($arrayView as $dataView) {
		$foundvalue = $dataView["field2"];
	}
	if (!empty($foundvalue)) {
		$foundvalue = $foundvalue;
	} else {
		$foundvalue = null;
	}
	return $foundvalue;
}

function _CreateDropDown($caption, $sql, $url, $addurl, $all)
{
	//echo "<br>".$sql."<br>";
	$view = new cView();
	$arraysql = $view->vViewData($sql);
?>
	<p>
	<div class="btn-group" role="group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $caption; ?>&nbsp;<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<?php
			if ($all == 1) {
				echo '<li><a href="' . $url . '">Semua</a></li>';
			}
			foreach ($arraysql as $datasql) {
				echo '<li><a href="' . $url . '&' . $addurl . '=' . $datasql["field1"] . '">' . $datasql["field2"] . '</a></li>';
			}
			?>
		</ul>
	</div>
	</p>
	<?php
}


function _CountOfNumber($table, $field, $value1)
{
	$sql = "SELECT count(" . $field . ") as nJumlahnya FROM " . $table . " WHERE " . $value1 . "";
	//echo $sql."<br>";
	$view = new cView();
	$arrayView = $view->vViewData($sql);
	foreach ($arrayView as $dataView) {
		$nReturnValue = $dataView["nJumlahnya"];
	}
	return $nReturnValue;
}

function _ProgressBar($width, $value)
{
	echo '<div class="progress">';
	echo '<div class="progress-bar" role="progressbar" aria-valuenow="' . $width . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $width . '%;">' . $value . '';
	echo '</div>';
	echo '</div>';
}

function _ButtonSubmit($class, $caption, $name, $value, $hiddenname, $hiddenvalue, $url, $type)
{
	echo '<form method="post" action="' . $url . '">';
	echo '<button class="btn btn-' . $class . '" type="submit" name="' . $name . '" value="' . $value . '">' . $caption . '</button>';
	echo '<input type="hidden" name="' . $hiddenname . '" value="' . $hiddenvalue . '">';
	echo '</form>';
}

function _JenisKelamin($jkel)
{
	switch ($jkel) {
		case "L":
			$cjkel = "Laki-Laki";
			break;
		case "P":
			$cjkel = "Perempuan";
			break;
	}
	return $cjkel;
}



function _GetTotalPermohonan($kd_coa_dep, $unit_base, $kd_fiskal)
{
	$sql = "SELECT a.kd_coa_dep, sum(a.nominal) nominalpermohoan 
	FROM tra_pengajuan_detil a 
	WHERE a.kd_coa_dep = '" . $kd_coa_dep . "' 
	AND a.kd_fiskal = " . $kd_fiskal . " 
	AND a.unit_base = '" . $unit_base . "'";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	$nMohon = 0;
	foreach ($arrayview as $value) {
		$nMohon = $value["nominalpermohoan"];
	}
	return $nMohon;
}

function _GetPencairan($value)
{
	$sql = "SELECT a.idpengajuan, count(a.idpengajuan) nCair 
	FROM tra_pencairan_detil a 
	WHERE a.idpengajuan = '" . $value . "' GROUP BY 1";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	$nCair = 0;
	foreach ($arrayview as $value) {
		$nCair = $value["nCair"];
	}
	return $nCair;
}

function _GetJumlahAjuPerProses($value)
{
	$sql = "SELECT a.kodestatus, b.keterangan ketaju, b.warna, COUNT(a.idpengajuan) nAju
	FROM tra_pengajuan_detil a
	LEFT OUTER JOIN ref_statusaju b ON a.kodestatus = b.kodestatus
	WHERE a.idpengajuan = '" . $value . "' 
	GROUP BY 1, 2, 3";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	foreach ($arrayview as $dataview) {
		if ($dataview["kodestatus"] == 0) {
			$cl = "secondary";
		} elseif ($dataview["kodestatus"] == 1) {
			$cl = "success";
		} elseif ($dataview["kodestatus"] == 2) {
			$cl = "warning";
		} else {
			$cl = "danger";
		}
	?>
		<div class="row">
			<div class="col">
				<button type="button" class="btn btn-outline-<?= $cl; ?> btn-sm" style="border-radius:30px;">
					<?= $dataview["ketaju"]; ?>&nbsp;
					<span class="badge badge-secondary"><?= $dataview["nAju"]; ?></span>
				</button>
				</span>
			</div>
		</div>
		<?php
	}
}


function _GetBulanTahun($value)
{
	$bln = substr($value, 0, 2);
	$thn = substr($value, 2, 4);
	$nmb = "";
	$sql = "SELECT a.kodebulan, a.namabulan FROM ref_bulan a WHERE a.kodebulan ='" . $bln . "'";
	$view = new cView();
	$arrayview = $view->vViewData($sql);
	foreach ($arrayview as $dataview) {
		$nmb = $dataview["namabulan"];
	}
	$tahunbulan = array($thn, $bln, $nmb);
	return $tahunbulan;
}



function _CekUnit($value)
{
	$sql = "SELECT a.* FROM ref_unit_base a WHERE a.kdunit = '" . $value . "'";
	$view = new cView();
	$arraydata = $view->vViewData($sql);
	$namaunitnya = "";
	foreach ($arraydata as $datadata) {
		$namaunitnya = $datadata["nama_unit"];
	}
	return $namaunitnya;
}

function _GetNamaBulan($value)
{
	$n = $value;
	switch ($n) {
		case 1:
			$bulan = "JANUARI";
			$sibul = "JAN";
			break;
		case 2:
			$bulan = "FEBRUARI";
			$sibul = "FEB";
			break;
		case 3:
			$bulan = "MARET";
			$sibul = "MAR";
			break;
		case 4:
			$bulan = "APRIL";
			$sibul = "APR";
			break;
		case 5:
			$bulan = "MEI";
			$sibul = "MEI";
			break;
		case 6:
			$bulan = "JUNI";
			$sibul = "JUN";
			break;
		case 7:
			$bulan = "JULI";
			$sibul = "JUL";
			break;
		case 8:
			$bulan = "AGUSTUS";
			$sibul = "AGU";
			break;
		case 9:
			$bulan = "SEPTEMBER";
			$sibul = "SEPT";
			break;
		case 10:
			$bulan = "OKTOBER";
			$sibul = "OKT";
			break;
		case 11:
			$bulan = "NOVEMBER";
			$sibul = "NOV";
			break;
		case 12:
			$bulan = "DESEMBER";
			$sibul = "DES";
			break;
	}
	$result = array($bulan, $sibul);
	return $result;
}

// function _myobject($object, $name, $class, $id, $value, $caption, $maxlength, $size, $rows, $cols, $required, $placeholder, $disabled)

function _myobjectnew($object, $id, $value, $label)
{
	switch ($object) {
		case '1':
		?>
			<div class="mb-3">
				<label for="exampleFormControlInput1" class="form-label"><?= $label; ?></label>
				<input value="<?= $value; ?>" type="text" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
			</div>
		<?php
			break;

		case '11':
		?>
			<div class="mb-3">
				<label for="exampleFormControlInput1" class="form-label"><?= $label; ?></label>
				<input value="<?= $value; ?>" type="text" class="form-control" id="<?= $id; ?>" placeholder="" disabled>
			</div>
	<?php
			break;
	}



	?>
<?php
}

?>