<?php
session_start();
include 'db.php';

function login($username, $password) {
    global $pdo;

    // Ambil data autentifikasi berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM autentifikasi WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session user_id dan username
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        // Ambil data pengguna berdasarkan user_id untuk mendapatkan role
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user['user_id']]);
        $user_data = $stmt->fetch();

        if ($user_data) {
            // Set role (jenis) pengguna di session
            $_SESSION['role'] = $user_data['jenis'];

            // Menyimpan log login
            $log_stmt = $pdo->prepare("INSERT INTO log_login (user_id, username, role) VALUES (?, ?, ?)");
            $log_stmt->execute([$user['user_id'], $user['username'], $user_data['jenis']]);

            return true; // Login berhasil
        }
    }
    return false; // Login gagal
}

// Fungsi untuk cek apakah pengguna adalah Admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Fungsi untuk cek apakah pengguna adalah Mahasiswa
function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'mahasiswa';
}

// Fungsi untuk cek apakah pengguna adalah Dosen
function isDosen() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'dosen';
}

?>
