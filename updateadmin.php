<?php
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");

// Memeriksa apakah session superadmin ada, jika tidak maka arahkan ke halaman login
if (!isset($_SESSION['super_id'])) {
    header("Location: superlogin.php");
    exit();
}

// Variabel untuk pesan
$message = "";

// Pencarian admin berdasarkan ID
$data_admin = null;
if (isset($_GET['cari_id'])) {
    $cari_id = $_GET['cari_id'];
    $query = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cari_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data_admin = $result->fetch_assoc();
    } else {
        $message = "Admin dengan ID $cari_id tidak ditemukan.";
    }
}

// Proses update data admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $admin_id = $_POST['admin_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nama = $_POST['nama'];

    // Cek apakah ada file foto baru yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoData = file_get_contents($_FILES['foto']['tmp_name']);
        $query = "UPDATE admin SET username = ?, password = ?, nama = ?, foto = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $username, $password, $nama, $fotoData, $admin_id);
    } else {
        $query = "UPDATE admin SET username = ?, password = ?, nama = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $username, $password, $nama, $admin_id);
    }

    if ($stmt->execute()) {
        $message = "Data admin berhasil diperbarui.";
    } else {
        $message = "Terjadi kesalahan: " . $stmt->error;
    }
}

// Proses hapus admin
if (isset($_POST['delete'])) {
    $admin_id = $_POST['admin_id'];
    $query = "DELETE FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        $message = "Admin berhasil dihapus.";
        $data_admin = null;
        header("location: superdash.php");
        exit;
    } else {
        $message = "Terjadi kesalahan saat menghapus admin: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
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
        <h1>Update & Delete Admin</h1>
        <form method="GET" action="">
            <label for="cari_id">Cari Admin Berdasarkan ID:</label>
            <input type="number" id="cari_id" name="cari_id" required>
            <button type="submit">Cari Admin</button>
            <a href="superdash.php">Kembali Ke Dashboard</a>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($data_admin): ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="admin_id" value="<?= htmlspecialchars($data_admin['admin_id']); ?>">

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($data_admin['username']); ?>" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti">

                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data_admin['nama']); ?>" required>

                <label for="foto">Foto (Kosongkan jika tidak ingin mengganti):</label>
                <input type="file" id="foto" name="foto" accept="image/*">

                <button type="submit" name="update">Update Admin</button>
                <button type="submit" name="delete" style="background-color: red;">Hapus Admin</button>
                <a href="superdash.php">Kembali Ke Dashboard</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
