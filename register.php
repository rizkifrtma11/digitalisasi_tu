<?php
include('db.php');  // Pastikan file db.php sudah menghubungkan ke database menggunakan PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nomor = $_POST['nomor'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $jenis = 'mahasiswa'; // Khusus untuk mahasiswa
    $nama = $_POST['nama'];

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Cek apakah nomor sudah ada
        $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nomor = ?");
        $cek->execute([$nomor]);
        if ($cek->fetchColumn() > 0) {
            throw new Exception("Nomor sudah digunakan. Silakan gunakan nomor lain.");
        }

        // Cek apakah username sudah ada
        $cekUser = $pdo->prepare("SELECT COUNT(*) FROM autentifikasi WHERE username = ?");
        $cekUser->execute([$username]);
        if ($cekUser->fetchColumn() > 0) {
            throw new Exception("Username sudah digunakan. Silakan pilih yang lain.");
        }

        // Mulai transaksi
        $pdo->beginTransaction(); // Memulai transaksi
        
        // Pastikan transaksi berhasil dimulai
        if (!$pdo->inTransaction()) {
            throw new Exception("Gagal memulai transaksi.");
        }

        // Insert data ke tabel users
        $stmt = $pdo->prepare("INSERT INTO users (nomor, jenis, nama, email, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nomor, $jenis, $nama, $email, $no_hp]);

        // Ambil ID pengguna
        $user_id = $pdo->lastInsertId();

        // Insert ke autentifikasi
        $stmt = $pdo->prepare("INSERT INTO autentifikasi (user_id, username, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $username, $password_hash]);

        // Commit transaksi jika semua berhasil
        $pdo->commit();

        // Redirect ke halaman yang sama dengan parameter sukses
        header("Location: register.php?status=success");
        exit;  // Pastikan setelah header tidak ada kode PHP lagi yang dieksekusi
    } catch (Exception $e) {
        // Jika transaksi gagal, rollback hanya jika transaksi dimulai
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .register-container {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        label {
            margin-bottom: 6px;
            color: #444;
            font-weight: 500;
        }

        input {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #1e90ff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #0b70d0;
        }

        p {
            text-align: center;
        }

        a {
            color: #1e90ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
                margin: 10px;
            }

            input {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Form Registrasi Mahasiswa</h2>

        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo "<p style='color: green; text-align: center;'>✅ Pendaftaran berhasil! Silakan login.</p>";
        }
        ?>

        <form method="POST" action="register.php">
            <label for="nomor">NIM (Nomor Induk Mahasiswa):</label>
            <input type="text" id="nomor" name="nomor" required>

            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="no_hp">Nomor HP:</label>
            <input type="text" id="no_hp" name="no_hp" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Daftar">
        </form>

        <p style="margin-top: 20px;">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>
        <p style="margin-top: 10px; text-align: center;">
            Bukan mahasiswa? Daftar <a href="register_pegawai.php">disini</a>
        </p>

    </div>
</body>
</html>
