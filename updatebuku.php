<?php
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");


// Memeriksa apakah session admin_id ada, jika tidak maka arahkan ke halaman login
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit(); 

    
}
// Variabel untuk pesan
$message = "";

// Pencarian buku berdasarkan ID
$data_buku = null;
if (isset($_GET['cari_id'])) {
    $cari_id = $_GET['cari_id'];
    $query = "SELECT * FROM buku WHERE buku_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cari_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data_buku = $result->fetch_assoc();
    } else {
        $message = "Buku dengan ID $cari_id tidak ditemukan.";
    }
}

// Proses update data buku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $buku_id = $_POST['buku_id'];
    $judulbuku = $_POST['judulbuku'];
    $penulis = $_POST['penulis'];
    $tahunterbit = $_POST['tahunterbit'];
    $genre = $_POST['genre'];

    // Cek apakah ada file foto baru yang diunggah
    if (isset($_FILES['fotobuku']) && $_FILES['fotobuku']['error'] === UPLOAD_ERR_OK) {
        $fotobukuData = file_get_contents($_FILES['fotobuku']['tmp_name']);
        $query = "UPDATE buku SET judulbuku = ?, penulis = ?, tahunterbit = ?, genre = ?, fotobuku = ? WHERE buku_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $judulbuku, $penulis, $tahunterbit, $genre, $fotobukuData, $buku_id);
    } else {
        // Jika tidak ada file baru, update tanpa kolom foto
        $query = "UPDATE buku SET judulbuku = ?, penulis = ?, tahunterbit = ?, genre = ? WHERE buku_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $judulbuku, $penulis, $tahunterbit, $genre, $buku_id);
    }

    if ($stmt->execute()) {
        $message = "Data buku berhasil diperbarui.";
    } else {
        $message = "Terjadi kesalahan: " . $stmt->error;
    }
}

// Proses hapus buku
if (isset($_POST['delete'])) {
    $buku_id = $_POST['buku_id'];
    $query = "DELETE FROM buku WHERE buku_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $buku_id);

    if ($stmt->execute()) {
        $message = "Buku berhasil dihapus.";
        $data_buku = null; // Hapus data dari tampilan
        header("location: admin.php");
        exit;
    } else {
        $message = "Terjadi kesalahan saat menghapus buku: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update & Delete Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, button {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: green;
        }
        .error {
            color: red;
        }
        .container a{
            display:inline-block;
            text-decoration:none;
            color:white;
            background:#007bff;
            padding:10px;
            text-align:center;
            border-radius:4px;
            margin-top:10px;
            width: 93%;
        }
        .container a:hover{
            background-color:#0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Buku</h1>
        <form method="GET" action="">
            <label for="cari_id">Cari Buku Berdasarkan ID:</label>
            <input type="number" id="cari_id" name="cari_id" required>
            <button type="submit">Cari Buku</button> 
            <a href="admin.php">Kembali Ke Dashboard</a>

        </form>

        <?php if (!empty($message)): ?>
            <div class="message <?= isset($error) ? 'error' : '' ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($data_buku): ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="buku_id" value="<?= htmlspecialchars($data_buku['buku_id']); ?>">

                <label for="judulbuku">Judul Buku:</label>
                <input type="text" id="judulbuku" name="judulbuku" value="<?= htmlspecialchars($data_buku['judulbuku']); ?>" required>

                <label for="penulis">Penulis:</label>
                <input type="text" id="penulis" name="penulis" value="<?= htmlspecialchars($data_buku['penulis']); ?>" required>

                <label for="tahunterbit">Tahun Terbit:</label>
                <input type="number" id="tahunterbit" name="tahunterbit" min="1900" max="2100" value="<?= htmlspecialchars($data_buku['tahunterbit']); ?>" required>

                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($data_buku['genre']); ?>" required>

                <label for="fotobuku">Foto Buku (Kosongkan jika tidak ingin mengganti):</label>
                <input type="file" id="fotobuku" name="fotobuku" accept="image/*">

                <button type="submit" name="update">Update Buku</button>
                <button type="submit" name="delete" style="background-color: red;">Hapus Buku</button>
                <a href="admin.php">Kembali Ke Dashboard</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
