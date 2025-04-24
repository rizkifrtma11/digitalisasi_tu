<?php
require 'db.php'; // biar koneksi jalan, oke ini yang dipakai

$jurusan_id = $_GET['jurusan_id'] ?? 0;

$stmt = $pdo->prepare("SELECT id, nama FROM program_studi WHERE jurusan_id = ?");
$stmt->execute([$jurusan_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
