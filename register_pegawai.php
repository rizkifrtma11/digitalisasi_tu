<?php
session_start();
include('db.php');  // Pastikan untuk menghubungkan ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nomor = $_POST['nomor'];  // Pastikan input di form memiliki nama 'nomor'
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $jenis = $_POST['jenis'];  // Dosen atau Pegawai
    $nama = $_POST['nama'];  // Nama lengkap dosen/pegawai

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Mulai transaksi untuk menjaga integritas data
        $pdo->beginTransaction();

        // Insert data ke tabel users untuk dosen/pegawai
        $stmt = $pdo->prepare("INSERT INTO users (nomor, jenis, nama, email, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nomor, $jenis, $nama, $email, $no_hp]);

        // Ambil ID pengguna yang baru saja dimasukkan
        $user_id = $pdo->lastInsertId();

        // Insert data ke tabel autentifikasi
        $stmt = $pdo->prepare("INSERT INTO autentifikasi (user_id, username, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $username, $password_hash]);

        // Commit transaksi
        $pdo->commit();

        // Redirect ke halaman yang sama setelah sukses
        header("Location: register_pegawai.php?status=success");
        exit;  // Pastikan untuk menghentikan eksekusi lebih lanjut
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Dosen dan Pegawai</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .register-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 6px;
            color: #444;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"] {
            padding: 12px;
            background-color: #1e90ff;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0d74d1;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
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
                border-radius: 10px;
                margin: 10px;
            }

            h2 {
                font-size: 20px;
            }

            input,
            select,
            input[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Registrasi Dosen atau Pegawai</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <p class="success-message">Pendaftaran berhasil! Silakan login.</p>
        <?php endif; ?>

        <form method="POST" action="register_pegawai.php">
            <label for="nomor">Nomor Induk:</label>
            <input type="text" id="nomor" name="nomor" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="no_hp">Nomor HP:</label>
            <input type="text" id="no_hp" name="no_hp" required>

            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="jenis">Jenis Pengguna:</label>
            <select id="jenis" name="jenis" required>
                <option value="admin">Admin</option>
            </select>

            <input type="submit" value="Daftar">
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>

        <p style="text-align: center; margin-top: 10px;">
            Bukan pegawai/dosen? Daftar <a href="register.php">mahasiswa disini</a>
        </p>
    </div>
</body>

</html>
