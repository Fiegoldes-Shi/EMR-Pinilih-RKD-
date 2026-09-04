<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
?>
<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");

$conn = new cConnect();
$conn->goConnect();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan RKD</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <style>
        .custom-dark-blue {
            border-color: rgb(43, 80, 135) !important;
            color: rgb(33, 87, 168) !important;
        }

        .chart-container-pie {
            width: 100%;
            height: 420px;
            position: relative;
        }

        @media (max-width: 1200px) {
            .chart-container-pie {
                height: 350px !important;
            }
        }
    </style>
</head>

<div class="row mx-2">
    <div class="col-12 mb-3">
        <?php
        _myHeader("LAPORAN", "Laporan RKD Pinilih");
        ?>
    </div>
</div>


<div class="row justify-content-center px-3">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="card text-center border border-4 custom-dark-blue h-100">
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
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="card text-center border border-4 custom-dark-blue h-100">
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
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="card text-center border border-4 custom-dark-blue h-100">
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
<br><br>

<div class="row mx-1 justify-content-center mb-4">
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <!-- JENIS DISABILITAS -->
        <div class="card text-center border border-2 h-100">
            <div class="card-body">
                <button onclick="exportToExcel('chartJD', 'Jenis Disabilitas')" class="btn btn-success mb-2 btn-sm ">
                    <i class="fa-solid fa-print"></i> CETAK EXCEL
                </button>
                <h5>Jenis Disabilitas Pasien</h5>
                <br>
                <canvas id="chartJD"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <!-- SUB DISABILITAS -->
        <div class="card text-center border border-2 h-100">
            <div class="card-body ">
                <button onclick="exportToExcel('chartSubJD', 'Sub Jenis Disabilitas')"
                    class="btn btn-success mb-2 btn-sm">
                    <i class="fa-solid fa-print"></i> CETAK EXCEL
                </button>
                <h5>Sub Jenis Disabilitas Pasien</h5>
                <small>
                    <p id="subJDText">Klik bar pada Jenis Disabilitas untuk melihat Sub Jenis Disabilitas</p>
                </small>
                <canvas id="chartSubJD"></canvas>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var chartJD, chartSubJD;
            loadChartJD(); //Chart Jenis Disabilitas
            loadEmptyChartSubJD(); // Chart Sub Jenis Disabilitas kosong

            // Chart Jenis Disabilitas
            function loadChartJD() {
                $.ajax({
                    url: 'chartDisabilitas.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        var ctx = document.getElementById('chartJD').getContext('2d');

                        var idJenisList = response.ids;
                        var labelJenisList = response.labels; // Tambahan: ambil label

                        if (chartJD) {
                            chartJD.destroy();
                        }

                        chartJD = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.labels,
                                datasets: [{
                                    label: 'Jenis Disabilitas',
                                    data: response.datas,
                                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                layout: {
                                    padding: {
                                        top: 30
                                    }
                                },
                                aspectRatio: window.innerWidth < 1200 ? 1 : 2,
                                responsive: true,
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'end',
                                        formatter: (value) => value,
                                        font: function (context) {
                                            return {
                                                weight: 'bold',
                                                size: window.innerWidth < 1200 ? 8 : 14
                                            };
                                        },
                                        color: '#000'
                                    },
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        display: true,
                                        title: { display: true, text: 'Jenis Disabilitas' },
                                        ticks: {
                                            autoSkip: false,
                                            maxRotation: window.innerWidth < 1200 ? 90 : 0,
                                            minRotation: window.innerWidth < 1200 ? 90 : 0
                                        }
                                    },
                                    y: {
                                        display: true,
                                        title: { display: true, text: 'Jumlah Pasien' },
                                        ticks: { stepSize: 1 },
                                        grace: '10%'
                                    }
                                },
                                onClick: function (event, elements) {
                                    if (elements.length > 0) {
                                        var index = elements[0].index;
                                        var idJenisDisabilitas = idJenisList[index];
                                        var labelJenis = labelJenisList[index];

                                        // Ubah judul grafik Sub JD
                                        $("#subJDText").html("Sub Jenis Disabilitas untuk <strong>" + labelJenis + "</strong>");

                                        // Load chart Sub JD
                                        loadChartSubJD(idJenisDisabilitas);
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                    }
                });
            }


            // Fungsi untuk membuat chart sub disabilitas kosong
            function loadEmptyChartSubJD() {
                var ctx = document.getElementById('chartSubJD').getContext('2d');
                chartSubJD = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Sub Jenis Disabilitas',
                            data: [],
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        layout: {
                            padding: {
                                top: 30,
                                bottom: 30,
                                left: 50,
                                right: 50
                            }
                        },
                        aspectRatio: window.innerWidth < 1200 ? 1 : 2,
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: { display: true, text: 'Sub Jenis Disabilitas' },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: window.innerWidth < 1200 ? 90 : 0,
                                    minRotation: window.innerWidth < 1200 ? 90 : 0
                                }
                            },
                            y: {
                                display: true,
                                title: { display: true, text: 'Jumlah Pasien' },
                                grace: '10%'
                            }
                        }
                    }
                });
            }

            // Fungsi untuk memuat chart sub jenis disabilitas dengan mengirim idJenisDisabilitas ke chartSubDisabilitas.php
            function loadChartSubJD(idJenisDisabilitas) {
                $.ajax({
                    url: 'chartSubDisabilitas.php',
                    type: 'GET',
                    data: { idJenisDisabilitas: idJenisDisabilitas },
                    dataType: 'json',
                    success: function (response) {
                        if (chartSubJD) {
                            chartSubJD.destroy();
                        }
                        var ctx = document.getElementById('chartSubJD').getContext('2d');
                        chartSubJD = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.labels,
                                datasets: [{
                                    label: 'Sub Jenis Disabilitas',
                                    data: response.datas,
                                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                layout: {
                                    padding: {
                                        top: 30
                                    }
                                },
                                aspectRatio: window.innerWidth < 1200 ? 1 : 2,
                                responsive: true,
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'end',
                                        formatter: (value) => value,
                                        font: function (context) {
                                            return {
                                                weight: 'bold',
                                                size: window.innerWidth < 1200 ? 8 : 14
                                            };
                                        },
                                        color: '#000'
                                    },
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        display: true,
                                        title: { display: true, text: 'Sub Jenis Disabilitas' },
                                        ticks: {
                                            autoSkip: false,
                                            maxRotation: window.innerWidth < 1200 ? 90 : 0,
                                            minRotation: window.innerWidth < 1200 ? 90 : 0
                                        }
                                    },
                                    y: {
                                        display: true,
                                        title: { display: true, text: 'Jumlah Pasien' },
                                        ticks: { stepSize: 1 },
                                        grace: '10%'
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                    }
                });
            }
        });
    </script>

    <script>
        function exportToExcel(chartId, sheetName) {
            var chart = Chart.getChart(chartId);
            if (!chart) {
                alert("Grafik tidak ditemukan!");
                return;
            }

            var labels = chart.data.labels; // sumbu X
            var datas = chart.data.datasets[0].data; // sumbu Y

            var dataExcel = [
                [sheetName.toUpperCase()], // Judul
                [],                        // Baris kosong
                ["Kategori", "Jumlah Pasien"] // Header tabel
            ];

            // Masukkan data ke dalam Excel
            for (var i = 0; i < labels.length; i++) {
                dataExcel.push([labels[i], datas[i]]);
            }

            // Buat worksheet Excel
            var ws = XLSX.utils.aoa_to_sheet(dataExcel);

            var wsCols = [
                { wch: 30 }, // Lebar kolom Kategori
                { wch: 15 }  // Lebar kolom Jumlah Pasien
            ];
            ws['!cols'] = wsCols;

            // header dan judul
            var titleCell = ws["A1"];
            if (titleCell) titleCell.s = { font: { bold: true, sz: 14 } };

            var headerCell1 = ws["A3"], headerCell2 = ws["B3"];
            if (headerCell1) headerCell1.s = { font: { bold: true } };
            if (headerCell2) headerCell2.s = { font: { bold: true } };

            // Buat workbook dan simpan file Excel
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, sheetName);
            XLSX.writeFile(wb, sheetName + ".xlsx");
        }
    </script>
</div>
</div>

<!-- pie chart -->
<div class="row mx-2 justify-content-center mt-5 mb-4">
    <div class="col-12 col-md-4 mb-3">
        <!-- KELOMPOK USIA -->
        <div class="card text-center border border-2">
            <div class="card-body">
                <div class="mb-3">
                    <button onclick="exportUsiaToExcel()" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-print" style="color: #ffffff;"></i> CETAK EXCEL
                    </button>
                </div>
                <h5 class="text-center mb-4">Kelompok Usia Pasien</h5>
                <div class="chart-container-pie">
                    <canvas id="chartUsia"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php
    $sql = "SELECT * FROM vKelompokUsia";
    $view = new cView();
    $arrayhasil = $view->vViewData($sql);

    $labels = [];
    $datas = [];
    $total = 0;

    foreach ($arrayhasil as $value) {
        $labels[] = trim($value["kelompokUsia"]); // kelompok usia
        $datas[] = (int) $value["jumlah"]; //jumlah pasien
        $total += (int) $value["jumlah"]; // total keseluruhan
    }

    // Urutin dari besar ke kecil
    array_multisort($datas, SORT_DESC, $labels);

    //konversi php ke json
    $labels = json_encode($labels, JSON_UNESCAPED_UNICODE);
    $datas = json_encode($datas);
    $total = max($total, 1);
    ?>

    <script>
        function getDynamicColors(values, total, baseColor) {
            const targetColors = { //warna dasar utk usia, kelurahan, dan goldar
                'rgba(2, 32, 92, 0.8)': { r: 135, g: 206, b: 250 },
                'rgba(0, 103, 48, 0.8)': { r: 144, g: 238, b: 144 },
                'rgba(153, 0, 0, 0.8)': { r: 255, g: 182, b: 193 }
            };

            // ambil RGB
            var match = baseColor.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
            var baseR = parseInt(match[1]);
            var baseG = parseInt(match[2]);
            var baseB = parseInt(match[3]);

            var target = targetColors[baseColor] || { r: 255, g: 255, b: 255 }; // fallback putih kalau baseColor gak ada

            return values.map(function (value, index) {
                if (index === 0) { // data pertama pake warna dasar
                    return baseColor;
                }

                //buat gradasinya 
                var ratio = index / (values.length - 1);
                var r = Math.round(baseR + (target.r - baseR) * ratio);
                var g = Math.round(baseG + (target.g - baseG) * ratio);
                var b = Math.round(baseB + (target.b - baseB) * ratio);

                return 'rgb(' + r + ',' + g + ',' + b + ')';
            });
        }

        const pieLabelsLinePlugin = {
            id: 'pieLabelsLine',
            afterDraw: function (chart) {
                if (chart.config.type !== 'pie') return;
                var ctx = chart.ctx;
                chart.data.datasets.forEach(function (dataset, i) {
                    var meta = chart.getDatasetMeta(i);
                    if (!meta.hidden) {
                        meta.data.forEach(function (arc, index) {
                            var data = dataset.data[index];
                            if (!data || data === 0) return;
                            var total = dataset.data.reduce(function (a, b) { return a + b; }, 0);
                            var percent = ((data / total) * 100).toFixed(1) + '%';

                            var angle = arc.startAngle + (arc.endAngle - arc.startAngle) / 2;
                            var radius = arc.outerRadius;
                            var centerX = arc.x;
                            var centerY = arc.y;

                            var startX = centerX + Math.cos(angle) * radius;
                            var startY = centerY + Math.sin(angle) * radius;

                            var isMobile = window.innerWidth < 1200;
                            var extraLine = isMobile ? ((index % 3) * 10 + 10) : ((index % 3) * 20 + 20); // Tambah panjang garis untuk melebarkan jarak
                            var midX = centerX + Math.cos(angle) * (radius + extraLine);

                            // Modifikasi Y untuk sedikit menarik ke atas agar tidak nabrak legend bawah
                            var yPush = (!isMobile && Math.sin(angle) > 0) ? -15 : 0;
                            var midY = centerY + Math.sin(angle) * (radius + extraLine) + yPush;

                            var sign = Math.cos(angle) >= 0 ? 1 : -1;
                            var endX = isMobile ? (midX + sign * 10) : (midX + sign * 20); // Garis ujung lebih panjang

                            // Pembatasan strict agar garis tidak keluar kanvas dan teks tetap muat
                            var canvasWidth = chart.width;
                            var safeMargin = 35; // Ruang memadai untuk teks persentase
                            if (endX < safeMargin) endX = safeMargin;
                            if (endX > canvasWidth - safeMargin) endX = canvasWidth - safeMargin;

                            var endY = midY;

                            var color = typeof dataset.backgroundColor === 'string' ? dataset.backgroundColor : dataset.backgroundColor[index] || '#000';

                            ctx.beginPath();
                            ctx.moveTo(startX, startY);
                            ctx.lineTo(midX, midY);
                            ctx.lineTo(endX, endY);
                            ctx.strokeStyle = color;
                            ctx.lineWidth = 1.5;
                            ctx.stroke();

                            var textX = endX + sign * 5;

                            ctx.font = 'bold 13px Arial';
                            ctx.textAlign = sign >= 0 ? 'left' : 'right';
                            ctx.textBaseline = 'middle';

                            ctx.strokeStyle = '#fff';
                            ctx.lineWidth = 4;
                            ctx.strokeText(percent, textX, endY);

                            ctx.fillStyle = '#000';
                            ctx.fillText(percent, textX, endY);
                        });
                    }
                });
            }
        };

        document.addEventListener("DOMContentLoaded", function () {
            var ctxUsia = document.getElementById("chartUsia").getContext("2d");
            var labelsUsia = <?= $labels; ?>;
            var dataValuesUsia = <?= $datas; ?>;
            var totalUsia = <?= $total; ?>;

            var warnaUsia = getDynamicColors(dataValuesUsia, totalUsia, 'rgba(2, 32, 92, 0.8)');

            if (window.chartUsia instanceof Chart) {
                window.chartUsia.destroy();
            }

            window.chartUsia = new Chart(ctxUsia, {
                type: "pie",
                data: {
                    labels: labelsUsia,
                    datasets: [{
                        data: dataValuesUsia,
                        backgroundColor: warnaUsia,
                        borderColor: "#fff",
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    radius: window.innerWidth < 1200 ? 65 : 110,
                    layout: {
                        padding: {
                            top: window.innerWidth < 1200 ? 30 : 30,
                            bottom: window.innerWidth < 1200 ? 30 : 30,
                            left: window.innerWidth < 1200 ? 30 : 50,
                            right: window.innerWidth < 1200 ? 30 : 50
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: { font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    var jumlah = dataValuesUsia[tooltipItem.dataIndex] || 0;
                                    var persentase = ((jumlah / totalUsia) * 100).toFixed(1);
                                    return jumlah + " pasien (" + persentase + "%)";
                                }
                            }
                        },
                        datalabels: {
                            color: '#000',
                            font: { weight: 'bold', size: 16 },
                            textStrokeColor: '#fff',
                            textStrokeWidth: 3,
                            anchor: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                return persentase < 15 ? 'end' : 'center';
                            },
                            align: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                return persentase < 15 ? 'end' : 'center';
                            },
                            offset: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                if (persentase < 15) {
                                    // Bawa persentase kecil sedikit memutar ke atas bukan ke bawah
                                    return (context.dataIndex % 2 === 0) ? 20 : 45;
                                }
                                return 0;
                            },
                            formatter: function (value, context) {
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                if (persentase === 0) return "";
                                return persentase + "%";
                            }
                        }
                    }
                },
                plugins: [pieLabelsLinePlugin]
            });
        });
    </script>

    <div class="col-12 col-md-4 mb-3">
        <!-- WILAYAH -->
        <div class="card text-center border border-2">
            <div class="card-body">
                <div class="mb-3">
                    <button onclick="exportKelurahanToExcel()" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-print" style="color: #ffffff;"></i> CETAK EXCEL
                    </button>
                </div>
                <h5 class="text-center mb-4">Kelurahan Domisili Pasien</h5>
                <div class="chart-container-pie">
                    <canvas id="chartKelurahan"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php
    $sql = "SELECT k.namaKelurahan, COUNT(p.idPasien) AS jumlah
                FROM pasien p
                JOIN kelurahan k ON p.idKelurahanDomisili = k.idKelurahan
                WHERE p.idKelurahanDomisili IN (40579, 40580, 40581, 40582)
                GROUP BY p.idKelurahanDomisili";
    $view = new cView();
    $arrayhasil = $view->vViewData($sql);

    $labels = [];
    $datas = [];
    $total = 0;

    foreach ($arrayhasil as $value) {
        $labels[] = trim($value["namaKelurahan"]);
        $datas[] = (int) $value["jumlah"];
        $total += (int) $value["jumlah"];
    }

    // Urutin dari besar ke kecil
    array_multisort($datas, SORT_DESC, $labels);

    $labels = json_encode($labels, JSON_UNESCAPED_UNICODE);
    $datas = json_encode($datas);
    $total = max($total, 1);
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctxKelurahan = document.getElementById("chartKelurahan").getContext("2d");
            var labelsKelurahan = <?= $labels; ?>;
            var dataValuesKelurahan = <?= $datas; ?>;
            var totalKelurahan = <?= $total; ?>;

            var warnaKelurahan = getDynamicColors(dataValuesKelurahan, totalKelurahan, 'rgba(0, 103, 48, 0.8)');

            if (window.chartKelurahan instanceof Chart) {
                window.chartKelurahan.destroy();
            }

            window.chartKelurahan = new Chart(ctxKelurahan, {
                type: "pie",
                data: {
                    labels: labelsKelurahan,
                    datasets: [{
                        data: dataValuesKelurahan,
                        backgroundColor: warnaKelurahan,
                        borderColor: "#fff",
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    radius: 110,
                    layout: {
                        padding: {
                            top: window.innerWidth < 1200 ? 30 : 30,
                            bottom: window.innerWidth < 1200 ? 30 : 30,
                            left: window.innerWidth < 1200 ? 30 : 50,
                            right: window.innerWidth < 1200 ? 30 : 50
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: { font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    var jumlah = dataValuesKelurahan[tooltipItem.dataIndex] || 0;
                                    var persentase = ((jumlah / totalKelurahan) * 100).toFixed(1);
                                    return jumlah + " pasien (" + persentase + "%)";
                                }
                            }
                        },
                        datalabels: {
                            color: '#000',
                            font: { weight: 'bold', size: 16 },
                            textStrokeColor: '#fff',
                            textStrokeWidth: 3,
                            anchor: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                return persentase < 15 ? 'end' : 'center';
                            },
                            align: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                return persentase < 15 ? 'end' : 'center';
                            },
                            offset: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                if (persentase < 15) {
                                    return (context.dataIndex % 2 === 0) ? 20 : 45;
                                }
                                return 0;
                            },
                            formatter: function (value, context) {
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var persentase = Math.round((value / total) * 100);
                                if (persentase === 0) return "";
                                return persentase + "%";
                            }
                        }
                    }
                },
                plugins: [pieLabelsLinePlugin]
            });
        });
    </script>

    <div class="col-12 col-md-4 mb-3">
        <!-- GOLONGAN DARAH -->
        <div class="card text-center border border-2">
            <div class="card-body">
                <div class="mb-3">
                    <button onclick="exportGoldarToExcel()" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-print" style="color: #ffffff;"></i> CETAK EXCEL
                    </button>
                </div>
                <h5 class="text-center mb-4">Golongan Darah Pasien</h5>
                <div class="chart-container-pie">
                    <canvas id="chartGoldar"></canvas>
                </div>
            </div>
        </div>

        <?php
        $sql = "SELECT * FROM vGolonganDarah";
        $view = new cView();
        $arrayhasil = $view->vViewData($sql);

        $labels = [];
        $datas = [];
        $total = 0;

        foreach ($arrayhasil as $value) {
            $labels[] = $value["golonganDarah"];
            $datas[] = (int) $value["jumlah"];
            $total += (int) $value["jumlah"];
        }

        // Urutin dari besar ke kecil
        array_multisort($datas, SORT_DESC, $labels);

        $labels = json_encode($labels);
        $datas = json_encode($datas);
        $total = max($total, 1);
        ?>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var ctxGoldar = document.getElementById('chartGoldar').getContext('2d');
                var labelsGoldar = <?= $labels; ?>;
                var dataValuesGoldar = <?= $datas; ?>;
                var totalGoldar = <?= $total; ?>;

                var warnaGoldar = getDynamicColors(dataValuesGoldar, totalGoldar, 'rgba(153, 0, 0, 0.8)');

                if (window.chartGoldar instanceof Chart) {
                    window.chartGoldar.destroy();
                }

                window.chartGoldar = new Chart(ctxGoldar, {
                    type: 'pie',
                    data: {
                        labels: labelsGoldar,
                        datasets: [{
                            data: dataValuesGoldar,
                            backgroundColor: warnaGoldar,
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        radius: window.innerWidth < 1200 ? 65 : 110,
                        layout: {
                            padding: {
                                top: window.innerWidth < 1200 ? 10 : 30,
                                bottom: window.innerWidth < 1200 ? 10 : 30,
                                left: window.innerWidth < 1200 ? 10 : 50,
                                right: window.innerWidth < 1200 ? 10 : 50
                            }
                        },
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom",
                                labels: { font: { size: 10 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        var jumlah = dataValuesGoldar[tooltipItem.dataIndex];
                                        var persentase = ((jumlah / totalGoldar) * 100).toFixed(1);
                                        return jumlah + " pasien (" + persentase + "%)";
                                    }
                                }
                            },
                            datalabels: {
                                color: '#000',
                                font: { weight: 'bold', size: 16 },
                                textStrokeColor: '#fff',
                                textStrokeWidth: 3,
                                anchor: 'center',
                                align: 'center',
                                formatter: function (value, context) {
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var persentase = Math.round((value / total) * 100);
                                    if (persentase === 0) return "";
                                    return persentase + "%";
                                }
                            }
                        }
                    },
                    plugins: [pieLabelsLinePlugin]
                });
            });
        </script>
    </div>

    <script>
        function exportUsiaToExcel() {
            var labels = window.chartUsia.data.labels;
            var dataValues = window.chartUsia.data.datasets[0].data;

            // Format Data dengan Judul
            var data = [
                ["LAPORAN PASIEN BERDASARKAN KELOMPOK USIA "],  // Judul
                [""], // Baris kosong sebagai pemisah
                ["Kelompok Usia", "Jumlah Pasien"]  // Header
            ];

            for (var i = 0; i < labels.length; i++) {
                data.push([labels[i], dataValues[i]]);
            }

            // Buat Worksheet
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Styling untuk judul
            ws["A1"].s = { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } };

            // Styling untuk header
            ws["A3"].s = { font: { bold: true } };
            ws["B3"].s = { font: { bold: true } };

            // Auto-width kolom
            ws["!cols"] = [
                { wch: 20 },  // Kolom "Kelompok Usia"
                { wch: 15 }   // Kolom "Jumlah Pasien"
            ];

            // Buat Workbook dan Simpan File
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Kelompok Usia");
            XLSX.writeFile(wb, "Kelompok_Usia.xlsx");
        }

        function exportKelurahanToExcel() {
            var labels = window.chartKelurahan.data.labels;
            var dataValues = window.chartKelurahan.data.datasets[0].data;

            // Format Data dengan Judul
            var data = [
                ["LAPORAN PASIEN BERDASARKAN KELURAHAN DOMISILI"],  // Judul
                [""], // Baris kosong sebagai pemisah
                ["Kelurahan Domisili", "Jumlah Pasien"]  // Header
            ];

            for (var i = 0; i < labels.length; i++) {
                data.push([labels[i], dataValues[i]]);
            }

            // Buat Worksheet
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Styling untuk judul
            ws["A1"].s = { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } };

            // Styling untuk header
            ws["A3"].s = { font: { bold: true } };
            ws["B3"].s = { font: { bold: true } };

            // Auto-width kolom
            ws["!cols"] = [
                { wch: 20 },
                { wch: 15 }
            ];

            // Buat Workbook dan Simpan File
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Kelurahan Domisili");
            XLSX.writeFile(wb, "Kelurahan_Domisili.xlsx");
        }

        function exportGoldarToExcel() {
            var labels = window.chartGoldar.data.labels;
            var dataValues = window.chartGoldar.data.datasets[0].data;

            // Format Data dengan Judul
            var data = [
                ["LAPORAN PASIEN BERDASARKAN GOLONGAN DARAH"],  // Judul
                [""], // Baris kosong sebagai pemisah
                ["Golongan Darah", "Jumlah Pasien"]  // Header
            ];

            for (var i = 0; i < labels.length; i++) {
                data.push([labels[i], dataValues[i]]);
            }

            // Buat Worksheet
            var ws = XLSX.utils.aoa_to_sheet(data);

            // Styling untuk judul
            ws["A1"].s = { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } };

            // Styling untuk header
            ws["A3"].s = { font: { bold: true } };
            ws["B3"].s = { font: { bold: true } };

            // Auto-width kolom
            ws["!cols"] = [
                { wch: 20 },  // Kolom "Golongan Darah"
                { wch: 15 }   // Kolom "Jumlah Pasien"
            ];

            // Buat Workbook dan Simpan File
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Golongan Darah");
            XLSX.writeFile(wb, "Golongan_Darah.xlsx");
        }
    </script>
</div>
</div><br>

<!-- Distribusi Pasien -->
<div class="row mx-2 justify-content-center">
    <div class="col-12">
        <!-- DISTRIBUSI PASIEN -->
        <div class="card text-center border border-2">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-12 col-lg-4 text-start mb-2 mb-lg-0">
                        <button id="btnExportDistrubsiPasien" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-print" style="color: #ffffff;"></i> CETAK EXCEL
                        </button>
                    </div>
                    <div class="col-12 col-lg-4 text-center mb-2 mb-lg-0">
                        <h5 class="mb-0">Distribusi Pasien</h5>
                    </div>
                    <div class="col-12 col-lg-4 d-flex justify-content-lg-end justify-content-center">
                        <select id="filterTahun" class="form-select form-select-sm w-auto">
                            <option value="">Semua Tahun</option>
                        </select>
                    </div>
                </div>
                <div style="width: 100%; height: 300px;">
                    <canvas id="chartDistribusiPasien"></canvas>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let ctx = document.getElementById('chartDistribusiPasien').getContext('2d');
                let chartDistribusi;
                let chartData = {}; // Simpan data untuk ekspor
                let selectedYear = ""; // Simpan tahun yang dipilih
                function loadChart(tahun = '') {
                    selectedYear = tahun; // Simpan tahun untuk ekspor
                    fetch(`chartDistribusiPasien.php?tahun=${tahun}`)
                        .then(response => response.json())
                        .then(data => {
                            chartData = data;
                            if (chartDistribusi) {
                                chartDistribusi.destroy();
                            }
                            chartDistribusi = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        { label: 'Fisioterapi', data: data.datasets.fisioterapi, borderColor: 'rgba(255, 99, 132, 1)', fill: false },
                                        { label: 'Kinesioterapi', data: data.datasets.kinesioterapi, borderColor: 'rgba(54, 162, 235, 1)', fill: false },
                                        { label: 'Screening', data: data.datasets.screening, borderColor: 'rgba(255, 206, 86, 1)', fill: false },
                                        { label: 'Konsultasi', data: data.datasets.konsultasi, borderColor: 'rgba(75, 192, 192, 1)', fill: false }]
                                },
                                options: {
                                    layout: {
                                        padding: {
                                            top: 30
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: {
                                            title: { display: true, text: 'Bulan' }
                                        },
                                        y: {
                                            title: { display: true, text: 'Jumlah Pasien' }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Gagal memuat grafik distribusi pasien:', error);
                        });
                }

                function loadTahun() {
                    fetch(`getTahunDistribusi.php`)
                        .then(response => response.json())
                        .then(data => {
                            let select = document.getElementById('filterTahun');
                            select.innerHTML = '<option value="">Semua Tahun</option>';
                            data.forEach(tahun => {
                                let option = document.createElement('option');
                                option.value = tahun;
                                option.textContent = tahun;
                                select.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Gagal memuat daftar tahun:', error);
                        });
                }

                document.getElementById('filterTahun').addEventListener('change', function () {
                    loadChart(this.value);
                });

                // EKSPOR KE EXCEL DENGAN JUDUL & FILTER
                document.getElementById('btnExportDistrubsiPasien').addEventListener('click', function () {
                    if (!chartData.labels) {
                        alert("Data belum tersedia untuk diekspor!");
                        return;
                    }
                    let worksheetData = [];

                    // Tambahkan JUDUL di Excel
                    worksheetData.push(["Distribusi Pasien"]);
                    worksheetData.push(["Tahun:", selectedYear || "Semua Tahun"]);
                    worksheetData.push([]); // Baris kosong untuk pemisah

                    // Header Data
                    worksheetData.push(["Bulan", "Fisioterapi", "Kinesioterapi", "Screening", "Konsultasi"]);

                    // Tambahkan Data Grafik
                    chartData.labels.forEach((bulan, index) => {
                        worksheetData.push([
                            bulan,
                            chartData.datasets.fisioterapi[index] || 0,
                            chartData.datasets.kinesioterapi[index] || 0,
                            chartData.datasets.screening[index] || 0,
                            chartData.datasets.konsultasi[index] || 0]);
                    });

                    let ws = XLSX.utils.aoa_to_sheet(worksheetData);
                    let wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Distribusi Pasien");

                    XLSX.writeFile(wb, `Distribusi_Pasien_${selectedYear || "Semua_Tahun"}.xlsx`);
                });

                loadTahun();
                loadChart();
            });
        </script>
    </div>
</div>
<br>

<!-- total program layanan -->
<div class="row mx-2 justify-content-center">
    <div class="col-12">
        <!-- TOTAL PROGRAM -->
        <div class="card text-center border border-2">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-12 col-lg-4 text-start mb-2 mb-lg-0">
                        <button id="btnExportTotalProgram" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-print" style="color: #ffffff;"></i> CETAK EXCEL
                        </button>
                    </div>
                    <div class="col-12 col-lg-4 text-center mb-2 mb-lg-0">
                        <h5 class="mb-0">Total Program Layanan</h5>
                    </div>

                    <!-- Filter -->
                    <div
                        class="col-12 col-lg-4 d-flex flex-wrap justify-content-center justify-content-lg-end gap-2 align-items-center">
                        <select id="filterMonth" class="form-select form-select-sm w-auto">
                            <option value="">Pilih Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>

                        <select id="filterYear" class="form-select form-select-sm w-auto">
                            <option value="">Pilih Tahun</option>
                            <?php
                            $currentYear = date("Y");
                            for ($year = $currentYear; $year >= 2020; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                        <button id="btnFilter" class="btn btn-primary btn-sm">Filter</button>
                    </div>
                </div> <!-- End Header Row -->

                <!-- Chart -->
                <div style="width: 100%; height: 300px;">
                    <canvas id="chartTotalProgram"></canvas>
                </div>
            </div>


            <script>
                $(document).ready(function () {
                    var ctx = document.getElementById('chartTotalProgram').getContext('2d');
                    var myChart;

                    function loadChart(bulan = '', tahun = '') {
                        $.ajax({
                            url: 'chartTotalProgram.php',
                            type: 'GET',
                            data: { bulan: bulan, tahun: tahun },
                            dataType: 'json',
                            success: function (response) {
                                var labels = response.labels;
                                var datas = response.datas;

                                if (myChart) { myChart.destroy(); }

                                myChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Total Program',
                                            data: datas,
                                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        layout: {
                                            padding: {
                                                top: 30
                                            }
                                        },
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            datalabels: {
                                                anchor: 'end',          // Posisi di ujung
                                                align: 'end',         // Teks di atas bar
                                                formatter: function (value, context) {
                                                    return value; // Label di angka
                                                },
                                                font: function (context) {
                                                    return {
                                                        size: window.innerWidth < 768 ? 8 : 16,           // Perbesar font
                                                        weight: 'bold'
                                                    };
                                                },
                                                color: '#000'           // Warna teks hitam
                                            },
                                            legend: {
                                                display: false
                                            }
                                        },
                                        scales: {
                                            x: {
                                                display: true,
                                                title: {
                                                    display: true,
                                                    text: 'Program Layanan'
                                                },
                                                ticks: {
                                                    stepSize: 1
                                                }
                                            },
                                            y: {
                                                display: true,
                                                title: {
                                                    display: true,
                                                    text: 'Jumlah Program'
                                                },
                                                grace: '10%',
                                                ticks: {
                                                    stepSize: 1,
                                                    precision: 0
                                                }
                                            }
                                        }
                                    },
                                    plugins: [ChartDataLabels] // ✅ Penting: aktifkan plugin
                                });


                            },
                            error: function (xhr, status, error) {
                                console.error("AJAX Error:", status, error);
                            }
                        });
                    }

                    // Load chart pertama kali tanpa filter
                    loadChart();

                    // Event saat tombol filter diklik
                    $("#btnFilter").click(function () {
                        var bulan = $("#filterMonth").val();
                        var tahun = $("#filterYear").val();
                        loadChart(bulan, tahun);
                    });
                });
                $("#btnExportTotalProgram").click(function () {
                    var bulan = $("#filterMonth").val();
                    var tahun = $("#filterYear").val();

                    // Konversi bulan angka ke teks
                    var bulanText = $("#filterMonth option:selected").text();
                    var tahunText = tahun ? tahun : "Semua Tahun";
                    var filterInfo = bulan ? `Bulan: ${bulanText}, Tahun: ${tahunText}` : `Tahun: ${tahunText}`;

                    $.ajax({
                        url: 'chartTotalProgram.php',
                        type: 'GET',
                        data: { bulan: bulan, tahun: tahun },
                        dataType: 'json',
                        success: function (response) {
                            var labels = response.labels;
                            var datas = response.datas;

                            // Buat data Excel dalam format array
                            var excelData = [
                                ["Total Program Layanan"],   // Judul laporan
                                [filterInfo],                // Informasi filter
                                [],                          // Baris kosong
                                ["No", "Nama Program", "Total"] // Header tabel
                            ];

                            // Tambahkan data program layanan
                            labels.forEach((label, index) => {
                                excelData.push([index + 1, label, datas[index]]);
                            });

                            // Buat worksheet
                            var ws = XLSX.utils.aoa_to_sheet(excelData);

                            // Merge cell untuk judul dan informasi filter
                            ws["!merges"] = [
                                { s: { r: 0, c: 0 }, e: { r: 0, c: 2 } }, // Judul di baris pertama
                                { s: { r: 1, c: 0 }, e: { r: 1, c: 2 } }  // Info filter di baris kedua
                            ];

                            // Atur lebar kolom agar lebih rapi
                            ws["!cols"] = [
                                { wch: 5 },   // Kolom "No"
                                { wch: 30 },  // Kolom "Nama Program"
                                { wch: 10 }   // Kolom "Total"
                            ];

                            // **Terapkan alignment rata kiri ke semua sel**
                            Object.keys(ws).forEach(cell => {
                                if (cell[0] !== '!') { // Hindari properti metadata (!cols, !merges, dll.)
                                    ws[cell].s = { alignment: { horizontal: "left" } };
                                }
                            });

                            // Buat workbook & simpan file
                            var wb = XLSX.utils.book_new();
                            XLSX.utils.book_append_sheet(wb, ws, "Total Program");
                            XLSX.writeFile(wb, "Laporan_Total_Program.xlsx");
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>
<br><br><br>
<script>
    // Penyesuaian responsif dinamis saat ukuran layar berubah tanpa refresh
    window.addEventListener('resize', function () {
        var isMobile = window.innerWidth < 1200;

        for (var id in Chart.instances) {
            var chart = Chart.instances[id];
            if (chart.config.type === 'pie') {
                chart.options.radius = isMobile ? 65 : 110;
                chart.options.layout.padding = isMobile ? { top: 30, bottom: 30, left: 30, right: 30 } : { top: 30, bottom: 30, left: 50, right: 50 };
            } else if (chart.config.type === 'bar') {
                chart.options.aspectRatio = isMobile ? 1 : 2;
                if (chart.options.scales && chart.options.scales.x && chart.options.scales.x.ticks) {
                    chart.options.scales.x.ticks.maxRotation = isMobile ? 90 : 0;
                    chart.options.scales.x.ticks.minRotation = isMobile ? 90 : 0;
                }
            }
            chart.update();
        }
    });

    // Trigger event saat halaman pertama kali load untuk memastikan bentuk grafik
    window.addEventListener('load', function () {
        window.dispatchEvent(new Event('resize'));
    });
</script>
</body>

</html>