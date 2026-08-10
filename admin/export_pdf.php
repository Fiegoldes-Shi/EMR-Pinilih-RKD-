<?php
date_default_timezone_set('Asia/Jakarta');

require_once("../_tcpdf/tcpdf.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cConnect.php");

$conn = new cConnect();
$conn->goConnect();

$tanggalCetak = date("d-m-Y H:i") . " WIB";

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $tanggalCetak;
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 6, 'Rumah Kebugaran Difabel Yayasan Pinilih Sejahtera', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 7, 'LAPORAN DATA PASIEN', 0, 1, 'C');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'Tanggal Cetak: ' . $tanggalCetak, 0, 1, 'C');
        $this->Ln(3);
    }
}

// PDF setup
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 35, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

$view = new cView();
$where = [];
$params = [];
$types = "";

if (!empty($_POST['kelompokUsia'])) {
    $where[] = "p.kelompokUsia = ?";
    $params[] = $_POST['kelompokUsia'];
    $types .= "s";
}
if (!empty($_POST['jenisKelamin'])) {
    $where[] = "p.jenisKelamin = ?";
    $params[] = $_POST['jenisKelamin'];
    $types .= "s";
}
if (!empty($_POST['golonganDarah'])) {
    $where[] = "p.golonganDarah = ?";
    $params[] = $_POST['golonganDarah'];
    $types .= "s";
}
if (!empty($_POST['jenisDisabilitas'])) {
    $where[] = "jd.jenisDisabilitas = ?";
    $params[] = $_POST['jenisDisabilitas'];
    $types .= "s";
}
if (!empty($_POST['idKelurahan'])) {
    $where[] = "p.idKelurahanDomisili = ?";
    $params[] = (int) $_POST['idKelurahan'];
    $types .= "i";
}

$sql = "SELECT p.*, sd.*, jd.*, k.namaKelurahan
        FROM pasien p
        JOIN sub_disabilitas sd ON sd.idSubDisabilitas = p.idSubDisabilitas
        JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas
        LEFT JOIN kelurahan k ON k.idKelurahan = p.idKelurahanDomisili";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " GROUP BY p.idPasien";

$data = $view->vViewDataPrepared($sql, $params, $types);

if (empty($data)) {
    echo "<script>alert('Data tidak ditemukan untuk filter ini.'); window.close();</script>";
    exit;
}

// Kolom
$w = array(8, 25, 20, 20, 15, 25, 18, 20, 20, 15);
$header = array('No', 'Nama Pasien', 'Jenis Kelamin', 'Usia', 'Gol. Darah', 'Alamat', 'Kelurahan', 'Jenis Disabilitas', 'Sub Jenis Disabilitas', 'Alat Bantu');

function printTableHeader($pdf, $w, $header)
{
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    $headerHeight = 7;
    foreach ($header as $i => $text) {
        $lines = $pdf->getNumLines($text, $w[$i]);
        $headerHeight = max($headerHeight, $lines * 5);
    }
    for ($i = 0; $i < count($header); $i++) {
        $pdf->MultiCell($w[$i], $headerHeight, $header[$i], 1, 'C', 1, 0, '', '', true, 0, false, true, $headerHeight, 'M');
    }
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 9);
}

$pdf->Ln(); // beri jarak dari header laporan ke tabel
printTableHeader($pdf, $w, $header);

$no = 1;
foreach ($data as $row) {
    // Hitung tinggi baris
    $nb = 1;
    $fields = array('namaLengkap', 'jenisKelamin', 'kelompokUsia', 'golonganDarah', 'alamatDomisili', 'namaKelurahan', 'jenisDisabilitas', 'namaDisabilitas', 'alatBantu');
    for ($i = 0; $i < count($fields); $i++) {
        $text = isset($row[$fields[$i]]) ? $row[$fields[$i]] : '';
        $nb = max($nb, $pdf->getNumLines($text, $w[$i + 1]));
    }
    $h = $nb * 5;

    $pageHeight = $pdf->getPageHeight();
    $bottomMargin = $pdf->getBreakMargin();
    $currentY = $pdf->GetY();
    $spaceLeft = $pageHeight - $bottomMargin - $currentY;

    // Kalau tinggi baris lebih besar dari space, pindah halaman
    if ($h > $spaceLeft) {
        $pdf->AddPage();
        printTableHeader($pdf, $w, $header);
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->MultiCell($w[0], $h, $no++, 1, 'C', 0, 0, $x, $y);
    $x += $w[0];

    $pdf->MultiCell($w[1], $h, $row['namaLengkap'], 1, 'L', 0, 0, $x, $y);
    $x += $w[1];

    $pdf->MultiCell($w[2], $h, $row['jenisKelamin'], 1, 'C', 0, 0, $x, $y);
    $x += $w[2];

    $pdf->MultiCell($w[3], $h, $row['kelompokUsia'], 1, 'C', 0, 0, $x, $y);
    $x += $w[3];

    $pdf->MultiCell($w[4], $h, $row['golonganDarah'], 1, 'C', 0, 0, $x, $y);
    $x += $w[4];

    $pdf->MultiCell($w[5], $h, $row['alamatDomisili'], 1, 'L', 0, 0, $x, $y);
    $x += $w[5];

    $pdf->MultiCell($w[6], $h, $row['namaKelurahan'], 1, 'L', 0, 0, $x, $y);
    $x += $w[6];

    $pdf->MultiCell($w[7], $h, $row['jenisDisabilitas'], 1, 'L', 0, 0, $x, $y);
    $x += $w[7];

    $pdf->MultiCell($w[8], $h, $row['namaDisabilitas'], 1, 'L', 0, 0, $x, $y);
    $x += $w[8];

    $pdf->MultiCell($w[9], $h, $row['alatBantu'], 1, 'L', 0, 0, $x, $y);
    $pdf->Ln($h);
}

$pdf->Output('Laporan_Pasien_' . date('Ymd_His') . '.pdf', 'I');
?>