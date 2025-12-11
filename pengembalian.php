<?php
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: superlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses pengembalian buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $peminjaman_id = $_POST['peminjaman_id'];

    // Validasi input
    if (!empty($peminjaman_id)) {
        // Cek apakah ID peminjaman milik user dan statusnya masih "dipinjam"
        $check_query = $conn->prepare("
            SELECT status 
            FROM peminjaman 
            WHERE peminjaman_id = ? AND user_id = ? AND status = 'dipinjam'
        ");
        $check_query->bind_param("ii", $peminjaman_id, $user_id);
        $check_query->execute();
        $result = $check_query->get_result();

        if ($result->num_rows > 0) {
            // Perbarui status menjadi "dikembalikan" dan set tanggal kembali
            $update_query = $conn->prepare("
                UPDATE peminjaman 
                SET status = 'dikembalikan', tanggal_kembali = NOW() 
                WHERE peminjaman_id = ?
            ");
            $update_query->bind_param("i", $peminjaman_id);

            if ($update_query->execute()) {
                $success_message = "Buku berhasil dikembalikan.";
                header("Location: userdash.php");
                exit;
            } else {
                $error_message = "Gagal memperbarui status peminjaman.";
            }
        } else {
            $error_message = "ID Peminjaman tidak valid atau buku sudah dikembalikan.";
        }
    } else {
        $error_message = "Harap pilih ID Peminjaman.";
    }
}

// Ambil daftar peminjaman milik user yang belum dikembalikan
$peminjaman_query = $conn->prepare("
    SELECT peminjaman_id, buku.judulbuku 
    FROM peminjaman 
    JOIN buku ON peminjaman.buku_id = buku.buku_id 
    WHERE peminjaman.user_id = ? AND peminjaman.status = 'dipinjam'
");
$peminjaman_query->bind_param("i", $user_id);
$peminjaman_query->execute();
$peminjaman_result = $peminjaman_query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #1976d2;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .message.success {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .message.error {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .form-container a{
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
        .form-container a:hover{
         background-color:#0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Form Pengembalian Buku</h2>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="peminjaman_id">Pilih Buku yang Ingin Dikembalikan</label>
            <select name="peminjaman_id" id="peminjaman_id" required>
                <option value="">-- Pilih ID Peminjaman --</option>
                <?php while ($row = $peminjaman_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['peminjaman_id']; ?>">
                        <?php echo htmlspecialchars($row['judulbuku']) . " (ID: " . $row['peminjaman_id'] . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Kembalikan Buku</button>
            <a href="userdash.php">Kembali Ke Dashboard</a>
        </form>
    </div>
</body>
</html>
