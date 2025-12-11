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

// Pencarian anggota berdasarkan ID
$data_anggota = null;
if (isset($_GET['cari_id'])) {
    $cari_id = $_GET['cari_id'];
    $query = "SELECT * FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cari_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data_anggota = $result->fetch_assoc();
    } else {
        $message = "Anggota dengan ID $cari_id tidak ditemukan.";
    }
}

// Proses update data anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];

    // Cek apakah ada file foto baru yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoData = file_get_contents($_FILES['foto']['tmp_name']);
        $query = "UPDATE user SET username = ?, nama = ?, alamat = ?, foto = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $username, $nama, $alamat, $fotoData, $user_id);
    } else {
        // Jika tidak ada file baru, update tanpa kolom foto
        $query = "UPDATE user SET username = ?, nama = ?, alamat = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $username, $nama, $alamat, $user_id);
    }

    if ($stmt->execute()) {
        $message = "Data anggota berhasil diperbarui.";
    } else {
        $message = "Terjadi kesalahan: " . $stmt->error;
    }
}

// Proses hapus anggota
if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    $query = "DELETE FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "Anggota berhasil dihapus.";
        $data_anggota = null; // Hapus data dari tampilan
    } else {
        $message = "Terjadi kesalahan saat menghapus anggota: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update & Delete Anggota</title>
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
        .container a {
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
        .container a:hover {
            background-color:#0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Anggota</h1>
        <form method="GET" action="">
            <label for="cari_id">Cari Anggota Berdasarkan ID:</label>
            <input type="number" id="cari_id" name="cari_id" required>
            <button type="submit">Cari Anggota</button> 
            <a href="userdash.php">Kembali Ke Dashboard</a>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message <?= isset($error) ? 'error' : '' ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($data_anggota): ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($data_anggota['user_id']); ?>">

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($data_anggota['username']); ?>" required>

                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data_anggota['nama']); ?>" required>

                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($data_anggota['alamat']); ?>" required>

                <label for="foto">Foto Anggota (Kosongkan jika tidak ingin mengganti):</label>
                <input type="file" id="foto" name="foto" accept="image/*">

                <button type="submit" name="update">Update Anggota</button>
                <button type="submit" name="delete" style="background-color: red;">Hapus Anggota</button>
                <a href="userdash.php">Kembali Ke Dashboard</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
