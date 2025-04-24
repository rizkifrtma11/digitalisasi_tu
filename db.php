<?php
$host = '';
$db = '';
$user = '';  // Ganti dengan username database Anda
$pass = '';  // Ganti dengan password database Anda

// Koneksi ke database MySQL
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
