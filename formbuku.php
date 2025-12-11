<?php
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi sesi admin login
    if (!isset($_SESSION['admin_id'])) {
        die("Akses ditolak! Harap login terlebih dahulu.");
    }

    $admin_id = $_SESSION['admin_id']; // Ambil admin_id dari sesi login
    $judulbuku = $_POST['judulbuku'];
    $penulis = $_POST['penulis'];
    $tahunterbit = $_POST['tahunterbit'];
    $genre = $_POST['genre'];
    $fotobuku = $_FILES['fotobuku'];

    // Proses unggah file foto buku
    if ($fotobuku['error'] === UPLOAD_ERR_OK) {
        $fotobukuData = file_get_contents($fotobuku['tmp_name']);
    } else {
        die("Gagal mengunggah foto buku!");
    }

    // Query insert ke tabel buku
    $query = "INSERT INTO buku (admin_id, judulbuku, penulis, tahunterbit, genre, fotobuku) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $admin_id, $judulbuku, $penulis, $tahunterbit, $genre, $fotobukuData);

    if ($stmt->execute()) {
        $message = "Buku berhasil ditambahkan!";
    } else {
        $message = "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku</title>
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
        <h1>Tambah Buku</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?= isset($error) ? 'error' : '' ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="judulbuku">Judul Buku:</label>
            <input type="text" id="judulbuku" name="judulbuku" required>

            <label for="penulis">Penulis:</label>
            <input type="text" id="penulis" name="penulis" required>

            <label for="tahunterbit">Tahun Terbit:</label>
            <input type="number" id="tahunterbit" name="tahunterbit" min="1000" max="2100" required>

            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" required>

            <label for="fotobuku">Foto Buku:</label>
            <input type="file" id="fotobuku" name="fotobuku" accept="image/*" required>

            <button type="submit">Tambah Buku</button>
            <a href="admin.php">Kembali Ke Dashboard</a>
        </form>
    </div>
</body>
</html>
