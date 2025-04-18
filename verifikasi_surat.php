<?php
session_start();
include('db.php');

// Cek role admin/pegawai
if (!isset($_SESSION['user_id']) || $_SESSION['jenis'] !== 'pegawai') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $aksi = $_POST['aksi'];

    if (in_array($aksi, ['disetujui', 'ditolak'])) {
        $stmt = $pdo->prepare("UPDATE surat_pengajuan_kp SET status_verifikasi = ? WHERE id = ?");
        $stmt->execute([$aksi, $id]);
    }
}

header("Location: admin_dashboard.php");
exit();
