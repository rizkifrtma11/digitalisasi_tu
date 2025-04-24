<?php
include 'auth.php';
include 'db.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

// Menambahkan Jurusan
if (isset($_POST['add_jurusan'])) {
    $jurusan = $_POST['jurusan'];
    $stmt = $pdo->prepare("INSERT INTO jurusan (nama) VALUES (:nama)");
    $stmt->execute(['nama' => $jurusan]);
}

// Menambahkan Program Studi
if (isset($_POST['add_prodi'])) {
    $prodi = $_POST['prodi'];
    $jurusan_id = $_POST['jurusan_id'];
    $stmt = $pdo->prepare("INSERT INTO program_studi (nama, jurusan_id) VALUES (:nama, :jurusan_id)");
    $stmt->execute(['nama' => $prodi, 'jurusan_id' => $jurusan_id]);
}

// Verifikasi Surat Pengajuan
if (isset($_POST['verifikasi_pengajuan'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Memindahkan data ke tabel riwayat
    if ($status == 'disetujui' || $status == 'ditolak') {
        $stmt_transfer = $pdo->prepare("INSERT INTO riwayat_pengajuan_kp (user_id, tanggal_pengajuan, nama_file, path_file, tipe_file, status_verifikasi, tanggal_verifikasi) 
                                       SELECT user_id, tanggal_pengajuan, nama_file, path_file, tipe_file, :status, CURDATE() 
                                       FROM surat_pengajuan_kp WHERE id = :id");
        $stmt_transfer->execute(['status' => $status, 'id' => $id]);

        // Menghapus data dari tabel surat_pengajuan_kp setelah dipindahkan
        $stmt_delete = $pdo->prepare("DELETE FROM surat_pengajuan_kp WHERE id = :id");
        $stmt_delete->execute(['id' => $id]);
    }
}


// Ambil pengajuan yang statusnya 'pending'
$stmt = $pdo->prepare("SELECT * FROM surat_pengajuan_kp WHERE status_verifikasi = 'pending'");
$stmt->execute();
$pengajuan = $stmt->fetchAll();

// Ambil semua jurusan
$stmt_jurusan = $pdo->query("SELECT * FROM jurusan");
$jurusan_data = $stmt_jurusan->fetchAll();

// Ambil semua program studi
$stmt_prodi = $pdo->query("SELECT ps.nama AS prodi, j.nama AS jurusan FROM program_studi ps JOIN jurusan j ON ps.jurusan_id = j.id");
$prodi_data = $stmt_prodi->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
    * {
        box-sizing: border-box;
    }

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

    h1, h2, h3 {
        color: #333;
        margin-top: 30px;
    }

    form {
        margin-bottom: 20px;
    }

    input[type="text"],
    select {
        padding: 10px;
        font-size: 16px;
        margin-right: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-bottom: 10px;
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
        margin-top: 15px;
        background-color: #fafafa;
    }

    table th,
    table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #e6f0ff;
    }

    form select {
        min-width: 120px;
    }

    .section {
        margin-bottom: 50px;
    }
    .logout-button {
        background-color: #ff4d4d;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }
    .logout-button:hover {
        background-color: #d93636;
    }

</style>

</head>
<body>
    <div class="container">
    <a href="logout.php" class="logout-button">Logout</a>

        <h1>Dashboard Admin</h1>

        <!-- Form Tambah Jurusan -->
        <div class="section">
            <h2>Tambah Jurusan</h2>
            <form method="POST">
                <input type="text" name="jurusan" placeholder="Nama Jurusan" required>
                <button type="submit" name="add_jurusan">Tambah Jurusan</button>
            </form>

            <h3>Daftar Jurusan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Jurusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jurusan_data as $jurusan): ?>
                    <tr>
                        <td><?php echo $jurusan['nama']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Form Tambah Program Studi -->
        <div class="section">
            <h2>Tambah Program Studi</h2>
            <form method="POST">
                <input type="text" name="prodi" placeholder="Nama Program Studi" required>
                <select name="jurusan_id" required>
                    <option value="">Pilih Jurusan</option>
                    <?php foreach ($jurusan_data as $row): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="add_prodi">Tambah Program Studi</button>
            </form>

            <h3>Daftar Program Studi</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Program Studi</th>
                        <th>Jurusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prodi_data as $prodi): ?>
                    <tr>
                        <td><?= $prodi['prodi'] ?></td>
                        <td><?= $prodi['jurusan'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Verifikasi Surat KP -->
        <div class="section">
            <h2>Pengajuan Surat KP</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama File</th>
                        <th>Status Verifikasi</th>
                        <th>Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengajuan as $item): ?>
                        <?php
                            $stmt_user = $pdo->prepare("SELECT username FROM autentifikasi WHERE user_id = :user_id");
                            $stmt_user->execute(['user_id' => $item['user_id']]);
                            $user = $stmt_user->fetch();
                            $nama_mahasiswa = $user['username'];
                        ?>
                        <tr>
                            <td><?= $nama_mahasiswa ?></td>
                            <td><?= $item['tanggal_pengajuan'] ?></td>
                            <td>
                                <a href="/uploads/kp/<?= rawurlencode($item['nama_file']); ?>" target="_blank">
                                    <?= htmlspecialchars($item['nama_file']); ?>
                                </a>
                            </td>

                            <td><?= $item['status_verifikasi'] ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <select name="status">
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                    <button type="submit" name="verifikasi_pengajuan">Verifikasi</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
