<?php
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];

    // Validasi input file
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoData = file_get_contents($_FILES['foto']['tmp_name']);
    } else {
        die("Gagal mengunggah foto profil! Pastikan Anda memilih file yang valid.");
    }

    // Query insert ke tabel user
    $query = "INSERT INTO user (username, password, nama, alamat, foto) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $username, $password, $nama, $alamat, $fotoData);

    if ($stmt->execute()) {
        $message = "Registrasi berhasil!";
        header("location: userlogin.php");
        exit();
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
    <title>Form Registrasi</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Form Registrasi</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?= isset($error) ? 'error' : '' ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" required>

            <label for="foto">Foto Profil:</label>
            <input type="file" id="foto" name="foto" accept="image/*" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
