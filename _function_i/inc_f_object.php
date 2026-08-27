<?php
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
