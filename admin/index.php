<?php


session_start();

// === BASEURL DINAMIS (tanpa hardcode localhost) ===
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'); // ambil parent folder (../)
$baseurl = $protocol . $host . $path;

// === CEK LOGIN ===
if (!isset($_SESSION["idUser"])) {
    // Redirect ke halaman login jika belum login
    header("Location: $baseurl/");
    exit();
}

// === LOAD FUNCTION ===
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");
$ROUTES = include("../_function_i/inc_f_routes.php");

$conn = new cConnect();
$conn->goConnect();
?>


<style>
    @media (min-width: 992px) {
        #offcanvasNavbar {
            width: 25%;
        }
    }

    @media (max-width: 991px) {
        #offcanvasNavbar {
            width: 75%;
        }
    }

    ion-icon {
        --ionicon-stroke-width: 30px;
    }

    .required {
        color: red;
    }

    thead {
        background-color: #e3f2fd;
        color: black;
    }

    /* Menghilangkan panah atas/bawah pada tipe number */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    /* ============================================================
       MOBILE UI — hanya berlaku di layar <= 767px
       Tidak menyentuh logika atau tampilan desktop sama sekali
    ============================================================ */
    @media (max-width: 767px) {

        /* --- Tabel: cegah overflow, buat bisa scroll horizontal --- */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Ukuran font tabel lebih kompak agar tidak terlalu penuh */
        .table td,
        .table th {
            font-size: 0.78rem;
            padding: 0.4rem 0.35rem;
            white-space: nowrap;
        }

        /* Header tabel tetap terbaca */
        .table thead th {
            font-size: 0.78rem;
        }

        /* --- Tombol aksi (VIEW/EDIT/HAPUS) di tabel lebih mudah diklik --- */
        .table .btn {
            padding: 0.35rem 0.55rem;
            font-size: 0.75rem;
            min-width: 36px;
            min-height: 36px;
            border-radius: 8px !important;
        }

        /* --- Tombol Tambah (+) di pojok kanan atas --- */
        .btn.btn-primary.btn-sm[data-bs-target] {
            min-width: 44px;
            min-height: 44px;
        }

        /* --- Modal: pastikan tidak keluar layar --- */
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100vw - 1rem);
        }

        .modal-dialog.modal-lg,
        .modal-dialog.modal-xl {
            max-width: calc(100vw - 1rem);
        }

        .modal-dialog.modal-sm {
            max-width: calc(100vw - 1rem);
        }

        /* Body modal bisa di-scroll jika konten panjang */
        .modal-body {
            max-height: 65vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* --- Input, select, textarea lebih tinggi agar mudah diketuk --- */
        .form-control,
        .form-select,
        .select2-container .select2-selection--single {
            min-height: 44px;
            font-size: 1rem;
        }

        /* Tombol di footer modal lebih besar dan penuh lebar --- */
        .modal-footer {
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.75rem;
        }

        .modal-footer .btn {
            flex: 1 1 auto;
            min-height: 44px;
            font-size: 0.95rem;
        }

        /* --- DataTables: kontrol search & info lebih rapi --- */
        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            min-height: 40px;
            font-size: 1rem;
        }

        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            width: 100%;
            text-align: left;
            margin-bottom: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.4rem 0.65rem;
            font-size: 0.85rem;
            min-width: 36px;
            min-height: 36px;
        }

        /* --- Card di beranda: teks ringkasan --- */
        .card .card-body h2 {
            font-size: 1.8rem;
        }

        .card .card-body h3 {
            font-size: 1rem;
        }

        /* --- Heading halaman --- */
        .blockquote p,
        .blockquote-footer {
            font-size: 0.9rem;
        }

        /* --- Tombol Ekspor / Cetak --- */
        a.btn,
        button.btn {
            min-height: 40px;
        }

        /* --- Pagination: wrap ke baris baru, teks tidak terpotong --- */
        .dataTables_wrapper .dataTables_paginate {
            overflow-x: auto;
            white-space: nowrap;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block;
            padding: 0.45rem 0.7rem;
            font-size: 0.9rem;
            min-width: 40px;
            min-height: 40px;
            line-height: 1.5;
            text-align: center;
            border-radius: 6px !important;
            margin: 2px 1px;
        }

        /* "Previous" dan "Next" tetap terbaca penuh */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            padding-left: 0.6rem;
            padding-right: 0.6rem;
            min-width: 52px;
        }

        /* --- Tombol ikon di sel tabel (VIEW/EDIT/HAPUS): ikon selalu di tengah --- */
        .table td .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 38px !important;
            height: 38px !important;
            padding: 0 !important;
            border-radius: 8px !important;
            font-size: 1rem !important;
        }

        /* --- Card detail program layanan: th tidak hardcode lebar --- */
        .card .table th {
            width: auto !important;
            white-space: normal;
            font-size: 0.82rem;
            vertical-align: top;
            padding-right: 0.5rem;
        }

        .card .table td {
            font-size: 0.82rem;
            word-break: break-word;
            white-space: normal;
        }

        /* Card detail tidak overflow */
        .card {
            overflow: hidden;
        }
    }
</style>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin EMR</title>

    <!-- Bootstrap 5.2.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script> var BASE_URL = "<?= $baseurl ?>"; </script>
    <script src="<?= $baseurl ?>/admin/js/config.js?v=<?= time(); ?>" type="text/javascript"></script>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"> -->

    <!-- sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/2b55107a0a.js" crossorigin="anonymous"></script>

    <!-- chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
</head>

<body>
    <?php

?>
    <nav class="navbar fixed-top" style="background-color: #e3f2fd;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">EMR RKD Pinilih</a>
            <ul class="nav-pills mt-2">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user fa-lg" style="color: #000000;"></i>
                    <span class="ms-2"><?= $_SESSION["nama"]; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text"><strong><?= $_SESSION["nama"]; ?></strong></span></li>
                    <li><span class="dropdown-item-text"><?= $_SESSION["jabatan"]; ?></span></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?= $baseurl; ?>/admin/atur-profil"><ion-icon
                                name="settings-outline"></ion-icon> Pengaturan</a>
                    </li>
                    <li><a class="dropdown-item text-danger" href="<?= $baseurl; ?>/logout.php"><ion-icon
                                name="log-out-outline"></ion-icon> Log Out</a></li>
                </ul>
            </ul>
            <div class="offcanvas offcanvas-start bg-light" tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-body mx-3 mt-3">
                    <ul class="navbar-nav me-auto mb-2">
                        <li class="nav-item mb-2">
                            <a class="nav-link active" aria-current="page" href="<?= $baseurl; ?>/admin/beranda">
                                <i class="fa-solid fa-house fa-lg" style="color:black;"></i> &nbsp; BERANDA
                            </a>
                        </li>
                        <li class="nav-item dropdown mb-2">
                            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-database fa-lg" style="color:black;"></i> &nbsp; MASTER DATA
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/data-pasien">
                                        <i class="fa-solid fa-hospital-user" style="color:black;"></i> &nbsp; Data
                                        Pasien
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/data-terapis">
                                        <i class="fa-solid fa-user-doctor" style="color:black;"></i> &nbsp; Data Tenaga
                                        Medis / <br> &nbsp; &nbsp; &nbsp; Paramedis / Non Medis
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/data-pengguna">
                                        <i class="fa-solid fa-user-tie" style="color:black;"></i> &nbsp; Data Pengguna
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/data-disabilitas">
                                        <i class="fa-solid fa-wheelchair" style="color:black;"></i> &nbsp; Data
                                        Disabilitas
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/data-peserta">
                                        <i class="fa-solid fa-users" style="color:black;"></i> &nbsp; Data Peserta
                                    </a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown mb-2">
                            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-hand-holding-medical fa-lg" style="color:black;"></i> &nbsp;
                                PROGRAM LAYANAN
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/fisioterapi">
                                        <i class="fa-solid fa-person-cane" style="color:black;"></i> &nbsp; Fisioterapi
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/kinesioterapi">
                                        <i class="fa-solid fa-people-robbery" style="color:black;"></i> &nbsp;
                                        Kinesioterapi
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/screening">
                                        <i class="fa-solid fa-user-shield" style="color:black;"></i> &nbsp; Screening
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/konsultasi">
                                        <i class="fa-solid fa-person-chalkboard" style="color:black;"></i> &nbsp;
                                        Konsultasi
                                    </a></li>
                                <li><a class="dropdown-item mb-2" href="<?= $baseurl; ?>/admin/edukasi">
                                        <i class="fa-solid fa-chalkboard-user" style="color:black;"></i> &nbsp; Edukasi
                                    </a></li>
                            </ul>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link active" aria-current="page" href="<?= $baseurl; ?>/admin/rekam-medis">
                                <i class="fa-solid fa-laptop-medical fa-lg" style="color:black;"></i> &nbsp; REKAM MEDIS
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link active" aria-current="page" href="<?= $baseurl; ?>/admin/laporan">
                                <i class="fa-solid fa-chart-line fa-lg" style="color:black;"></i> &nbsp; LAPORAN
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <br><br><br>

    <div class="container-fluid mt-5">
        <?php
// Ambil URL path dari permintaan
$segments = explode("/", trim($_SERVER["REQUEST_URI"], "/"));

// cari posisi "admin" di URL
$posAdmin = array_search("admin", $segments);

// ambil slug setelah "admin" (module + opsional "detail")
$slug = ($posAdmin !== false && isset($segments[$posAdmin + 1]) && $segments[$posAdmin + 1] !== '')
    ? $segments[$posAdmin + 1]
    : 'beranda';
if (($segments[$posAdmin + 2] ?? null) === 'detail') {
    $slug .= '/detail';
}

switch ($slug) {
    case 'beranda':
        include("incHome.php");
        break;
    case 'data-pasien':
        include("incDataPasien.php");
        break;
    case 'data-terapis':
        include("incDataTerapis.php");
        break;
    case 'data-pengguna':
        include("incDataPengguna.php");
        break;
    case 'data-disabilitas':
        include("incDataDisabilitas.php");
        break;
    case 'data-peserta':
        include("incDataPeserta.php");
        break;
    case 'fisioterapi':
        include("incFisioterapi.php");
        break;
    case 'fisioterapi/detail':
        include("detailFisio.php");
        break;
    case 'kinesioterapi':
        include("incKinesioterapi.php");
        break;
    case 'kinesioterapi/detail':
        include("detailKinesio.php");
        break;
    case 'screening':
        include("incScreening.php");
        break;
    case 'screening/detail':
        include("detailScreening.php");
        break;
    case 'konsultasi':
        include("incKonsultasi.php");
        break;
    case 'konsultasi/detail':
        include("detailKonsultasi.php");
        break;
    case 'edukasi':
        include("incEdukasi.php");
        break;
    case 'edukasi/detail':
        include("detailEdukasi.php");
        break;
    case 'rekam-medis':
        include("incRekamMedis.php");
        break;
    case 'rekam-medis/detail':
        include("detailRekamMedis.php");
        break;
    case 'laporan':
        include("incLaporan.php");
        break;
    case 'atur-profil':
        include("incAturProfil.php");
        break;
    default:
        include("incHome.php");
        break;
}
?>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5.2.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
        crossorigin="anonymous"></script>


    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- ion icon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Aktifkan semua elemen dengan tooltip di dalam dokumen
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <!-- Inisialisasi DataTable -->
    <script>
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable("#example")) {
                $('#example').DataTable().destroy(); // Hapus instance sebelumnya
            }

            $('#example').DataTable({
                "order": [[0, "asc"]],
                "paging": true,
                "searching": true,
                "info": true
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Fungsi untuk memastikan elemen pesan error ada (agar dinamis untuk modal AJAX)
            function ensureErrorMsg(input) {
                if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-msg')) {
                    const errorMessage = document.createElement('small');
                    errorMessage.classList.add('error-msg');
                    errorMessage.style.color = 'red';
                    errorMessage.style.display = 'none';
                    errorMessage.textContent = 'Harus isi angka!';
                    input.parentNode.appendChild(errorMessage);
                }
                return input.nextElementSibling;
            }

            // Menerapkan Event Delegation pada document.body
            document.body.addEventListener('keydown', function (e) {
                if (e.target && e.target.type === 'number') {
                    const input = e.target;
                    const errorMsg = ensureErrorMsg(input);

                    const allowedKeys = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'];
                    const isNumber = /^[0-9]$/.test(e.key);

                    if (!isNumber && !allowedKeys.includes(e.key)) {
                        e.preventDefault();
                        errorMsg.style.display = 'block';
                        input.classList.add('is-invalid');
                    }
                }
            });

            // Validasi saat nilai berubah (termasuk copy-paste) via delegation
            document.body.addEventListener('input', function (e) {
                if (e.target && e.target.type === 'number') {
                    const input = e.target;
                    const errorMsg = ensureErrorMsg(input);
                    const onlyNumbers = /^\d*$/;

                    if (onlyNumbers.test(input.value)) {
                        errorMsg.style.display = 'none';
                        input.classList.remove('is-invalid');
                    } else {
                        errorMsg.style.display = 'block';
                        input.classList.add('is-invalid');
                    }
                }
            });
        });
    </script>

    <!-- validasi time (dihapus agar input time native AM/PM berfungsi normal) -->

    <!-- validasi date  -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dateInputs = document.querySelectorAll('input[type="date"]');

            dateInputs.forEach(function (input) {
                // Buat elemen pesan error jika belum ada
                if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-msg')) {
                    const errorMessage = document.createElement('small');
                    errorMessage.classList.add('error-msg');
                    errorMessage.style.color = 'red';
                    errorMessage.style.display = 'none';
                    errorMessage.textContent = 'Harus isi tanggal yang valid (mm/dd/yyyy)!';
                    input.parentNode.appendChild(errorMessage);
                }

                const errorMsg = input.nextElementSibling;

                function formatDateToMMDDYYYY(dateValue) {
                    const parts = dateValue.split('-');
                    if (parts.length !== 3) return '';
                    return parts[1] + '/' + parts[2] + '/' + parts[0]; // mm/dd/yyyy
                }

                function isValidDate(dateValue) {
                    const datePattern = /^\d{4}-\d{2}-\d{2}$/;
                    if (!datePattern.test(dateValue)) return false;

                    const parts = dateValue.split('-');
                    const year = parts[0];

                    return year.length === 4; // Pastikan tahun 4 digit
                }

                // Cegah ketik huruf
                input.addEventListener('keydown', function (e) {
                    const allowedKeys = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete', '-', '/'];
                    const isNumber = /^[0-9]$/.test(e.key);

                    if (!isNumber && !allowedKeys.includes(e.key)) {
                        e.preventDefault();
                        errorMsg.style.display = 'block';
                        input.classList.add('is-invalid');
                    }
                });

                // Validasi saat nilai berubah
                input.addEventListener('input', function () {
                    if (input.value && isValidDate(input.value)) {
                        const formattedDate = formatDateToMMDDYYYY(input.value);
                        input.setAttribute('data-formatted', formattedDate);
                        errorMsg.style.display = 'none';
                        input.classList.remove('is-invalid');
                    } else {
                        errorMsg.style.display = 'block';
                        input.classList.add('is-invalid');
                    }
                });

                // Validasi saat blur (keluar dari input)
                input.addEventListener('blur', function () {
                    if (!input.value || !isValidDate(input.value)) {
                        errorMsg.style.display = 'block';
                        input.classList.add('is-invalid');
                    } else {
                        const formattedDate = formatDateToMMDDYYYY(input.value);
                        input.setAttribute('data-formatted', formattedDate);
                        errorMsg.style.display = 'none';
                        input.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 when a Bootstrap modal is shown
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('select[name="idPasien"], select[name="idTerapis"], select[name="idPeserta"]').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $(this),
                    width: '100%',
                    placeholder: "- pilihan -"
                });
            });

            // Otomatis mereset isi form ke 'default html value' setiap kali modal form dttutup
            function resetModalForm(modal) {
                var form = modal.find('form');
                if (form.length > 0) {
                    form[0].reset();
                    form.find('.select2-hidden-accessible').trigger('change');
                    modal.find('.is-invalid').removeClass('is-invalid');
                    modal.find('.error-msg').hide();

                    // Restore initial HTML content for selects, textareas, etc if configured (specifically for textareas like in detailEdukasi)
                    form.find('textarea').each(function() {
                        if (typeof $(this).data('original-text') !== 'undefined') {
                            $(this).val($(this).data('original-text')); // Reset to what PHP originally generated
                        }
                    });
                }
            }

            // Capture initial textarea values on modal show so we can reset to PHP's pre-filled values
            $(document).on('show.bs.modal', '.modal', function () {
                $(this).find('textarea').each(function() {
                    if (typeof $(this).data('original-text') === 'undefined') {
                        $(this).data('original-text', $(this).val());
                    }
                });
            });

            // Auto-Reset Modals on Close (Tambah & Edit)
            $(document).on('hidden.bs.modal', '.modal', function() {
                resetModalForm($(this));
            });

            // Fallback: klik pada tombol [Tutup] atau [X]
            $(document).on('click', '[data-bs-dismiss="modal"]', function() {
                var modal = $(this).closest('.modal');
                resetModalForm(modal);
            });

            // Auto-Reset ketika tombol "Ulang" ditekan
            $(document).on('click', 'button[type="reset"], input[type="reset"]', function() {
                var form = $(this).closest('form');
                setTimeout(function() {
                    form.find('.select2-hidden-accessible').trigger('change');
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.error-msg').hide();
                    
                    form.find('textarea').each(function() {
                        if (typeof $(this).data('original-text') !== 'undefined') {
                            $(this).val($(this).data('original-text'));
                        }
                    });
                }, 50);
            });
        });
    </script>
</body>

</html>