<?php

// Peta terpusat kode rute lama (angka) ke slug URL baru (kebab-case).
// Dipakai oleh index.php tiap peran (parsing + switch) dan oleh incHome.php
// untuk membangun tautan pintasan dashboard ($urlProgram).
return [
    'beranda'              => 'beranda',
    'data_pasien'          => 'data-pasien',
    'data_terapis'         => 'data-terapis',
    'data_pengguna'        => 'data-pengguna',
    'data_disabilitas'     => 'data-disabilitas',
    'data_peserta'         => 'data-peserta',
    'fisioterapi'          => 'fisioterapi',
    'fisioterapi_detail'   => 'fisioterapi/detail',
    'kinesioterapi'        => 'kinesioterapi',
    'kinesioterapi_detail' => 'kinesioterapi/detail',
    'screening'            => 'screening',
    'screening_detail'     => 'screening/detail',
    'konsultasi'           => 'konsultasi',
    'konsultasi_detail'    => 'konsultasi/detail',
    'edukasi'              => 'edukasi',
    'edukasi_detail'       => 'edukasi/detail',
    'rekam_medis'          => 'rekam-medis',
    'rekam_medis_detail'   => 'rekam-medis/detail',
    'laporan'              => 'laporan',
    'atur_profil'          => 'atur-profil',
];
