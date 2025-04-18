<?php
$host = 'sql312.byethost22.com';
$db = 'b22_38778699_database_digitalisasi';
$user = 'b22_38778699';  // Ganti dengan username database Anda
$pass = 'digitalisasi12345';  // Ganti dengan password database Anda

// Koneksi ke database MySQL
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>