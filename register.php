<?php
require 'db.php';

// Ambil semua jurusan untuk dropdown awal
$stmt = $pdo->query("SELECT id, nama FROM jurusan");
$jurusan_list = $stmt->fetchAll();

// Proses form ketika disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomor = $_POST['nomor'];
    $jenis = 'mahasiswa';
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $program_studi_id = $_POST['program_studi_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (nomor, jenis, nama, email, no_hp, program_studi_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nomor, $jenis, $nama, $email, $no_hp, $program_studi_id]);

        $user_id = $pdo->lastInsertId();

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO autentifikasi (user_id, username, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $username, $password_hash]);

        $pdo->commit();

        header("Location: register.php?status=success");
        exit;
    } catch (Exception $e) {
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
    <title>Registrasi Mahasiswa</title>
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
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
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
        <h2>Registrasi Mahasiswa</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <p class="success-message">Pendaftaran berhasil! Silakan login.</p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="nomor">Nomor Induk Mahasiswa</label>
            <input type="text" name="nomor" id="nomor" required>

            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="no_hp">Nomor HP</label>
            <input type="text" name="no_hp" id="no_hp" required>

            <label for="jurusan">Jurusan</label>
            <select name="jurusan_id" id="jurusan" required>
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusan_list as $jurusan): ?>
                    <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="program_studi">Program Studi</label>
            <select name="program_studi_id" id="program_studi" required>
                <option value="">-- Pilih Program Studi --</option>
            </select>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Daftar">
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>

        <p style="text-align: center; margin-top: 10px;">
            Bukan mahasiswa? Daftar <a href="register_pegawai.php">disini</a>
        </p>
    </div>

    <script>
        document.getElementById('jurusan').addEventListener('change', function () {
            var jurusanId = this.value;
            var programStudiSelect = document.getElementById('program_studi');
            programStudiSelect.innerHTML = '<option value="">Memuat...</option>';

            fetch('get_program_studi.php?jurusan_id=' + jurusanId)
                .then(response => response.json())
                .then(data => {
                    programStudiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
                    data.forEach(function (item) {
                        var option = document.createElement('option');
                        option.value = item.id;
                        option.text = item.nama;
                        programStudiSelect.appendChild(option);
                    });
                });
        });
    </script>
</body>
</html>
