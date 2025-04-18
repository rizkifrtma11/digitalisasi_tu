<?php
include 'auth.php';
include 'db.php';

if (!isUser()) {
    header('Location: index.php');
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Cek apakah ada pengajuan pending
$stmt_check_pending = $pdo->prepare("SELECT * FROM surat_pengajuan_kp WHERE user_id = :user_id AND status_verifikasi = 'pending'");
$stmt_check_pending->execute(['user_id' => $user_id]);
$pending_pengajuan = $stmt_check_pending->fetch();

// Handle Pengajuan Baru
if (isset($_POST['ajukan_kp']) && !$pending_pengajuan) {
    $nama_file = $_FILES['file']['name'];
    $path_file = 'uploads/kp/' . $nama_file;
    $tipe_file = pathinfo($nama_file, PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $path_file)) {
        $stmt_insert = $pdo->prepare("INSERT INTO surat_pengajuan_kp (user_id, tanggal_pengajuan, nama_file, path_file, tipe_file, status_verifikasi) 
                                      VALUES (:user_id, CURDATE(), :nama_file, :path_file, :tipe_file, 'pending')");
        $stmt_insert->execute([
            'user_id' => $user_id,
            'nama_file' => $nama_file,
            'path_file' => $path_file,
            'tipe_file' => $tipe_file
        ]);
        header("Location: user_dashboard.php"); // biar refresh data
        exit;
    } else {
        $error = "Upload file gagal!";
    }
}

// Ambil data pengajuan
if ($pending_pengajuan) {
    // Jika ada pending, ambil dari surat_pengajuan_kp
    $stmt = $pdo->prepare("SELECT * FROM surat_pengajuan_kp WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $pengajuan_data = $stmt->fetchAll();
    $source = "pengajuan saat ini";
} else {
    // Jika tidak ada pending, ambil dari riwayat
    $stmt = $pdo->prepare("SELECT * FROM riwayat_pengajuan_kp WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $pengajuan_data = $stmt->fetchAll();
    $source = "riwayat pengajuan";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="file"] {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0d74d1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fafafa;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #e6f0ff;
        }
        .section {
            margin-bottom: 40px;
        }
        .logout-btn {
            font-size: 16px;
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        .username {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="?logout" class="logout-btn">Logout</a>
        <h1>Dashboard Mahasiswa <span class="username">(<?= htmlspecialchars($username); ?>)</span></h1>

        <!-- Form Pengajuan -->
        <div class="section">
            <h2>Ajukan Surat KP</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= $error; ?></p>
            <?php elseif ($pending_pengajuan): ?>
                <p style="color: orange;">Pengajuan Anda masih <strong>pending</strong>, harap tunggu verifikasi.</p>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="file" required>
                    <button type="submit" name="ajukan_kp">Ajukan</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Tabel Status -->
        <div class="section">
            <h2>Status <?= $source; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama File</th>
                        <th>Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pengajuan_data) > 0): ?>
                        <?php foreach ($pengajuan_data as $item): ?>
                            <tr>
                                <td><?= $item['tanggal_pengajuan']; ?></td>
                                <!-- <td><?= htmlspecialchars($item['nama_file']); ?></td> nama file tidak di link -->
                                <td>
                                    <a href="/uploads/kp/<?= rawurlencode($item['nama_file']); ?>" target="_blank">
                                        <?= htmlspecialchars($item['nama_file']); ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($item['status_verifikasi']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Belum ada pengajuan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
