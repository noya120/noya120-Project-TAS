<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Pastikan pengguna login
    exit;
}

// Koneksi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "perpus";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan data yang dibutuhkan tersedia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buku_id'])) {
    $user_id = $_SESSION['user_id']; // Ambil user_id dari sesi
    $buku_id = intval($_POST['buku_id']); // Sanitasi input buku_id
    $tanggal_pinjam = date('Y-m-d'); // Tanggal hari ini
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days')); // Tanggal kembali otomatis +7 hari
    $status = "Dipinjam";

    // Simpan data ke tabel peminjaman
    $sql = "INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $user_id, $buku_id, $tanggal_pinjam, $tanggal_kembali, $status);

    if ($stmt->execute()) {
        $message = "Buku berhasil dipinjam!";
    } else {
        $message = "Gagal meminjam buku: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: databuku.php"); // Redirect jika akses langsung tanpa POST
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Peminjaman</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Konfirmasi Peminjaman</h1>
        <div class="alert alert-info">
            <?= htmlspecialchars($message); ?>
        </div>
        <a href="databuku.php" class="btn btn-primary">Kembali ke Daftar Buku</a>
    </div>
</body>
</html>
