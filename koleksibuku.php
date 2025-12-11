<?php
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");


// Memeriksa apakah session admin_id ada, jika tidak maka arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit(); 

    
}

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data buku
$sql = "SELECT buku_id, admin_id, judulbuku, penulis, genre, tahunterbit, fotobuku FROM buku";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* CSS Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            --secondary-gradient: linear-gradient(135deg, #97ABFF 10%, #123597 100%);
        }

        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            margin-top: 50px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        h1{
            text-align:center;
        }

        header {
            background: var(--primary-gradient);
            padding: 1rem;
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .navbar {
            background-color: transparent;
        }

        .navbar .navbar-nav .nav-item .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .navbar .navbar-nav .nav-item .nav-link:hover {
            background-color: #ffffff1a;
            color: #ffffff;
            border-radius: 25px;
            transform: translateY(-2px);
        }

        .navbar .navbar-nav .nav-item.active .nav-link {
            color: #FFD700 !important;
            font-weight: bold;
        }

        .navbar-nav {
            gap: 20px;
        }

        /* Foto Profil User di Header */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info span {
            color: white;
            font-size: 1rem;
        }
    </style>
</head>
<body>
     <!-- Header -->
<!-- Header -->
<header>
    <div class="header-content">
        <div class="logo">PERPUSTAKAAN FTI</div>
        <!-- Navbar menggunakan Bootstrap 5 -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="index.php">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="koleksibuku.php">Koleksi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="userdash.php">Profil</a>
                        </li>
                        <?php if (!isset($_SESSION['username'])): ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="userlogin.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="registrasiuser.php">Register</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="index.php?logout=true">Logout</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Menampilkan Foto Profil Jika Ada -->
        <?php if (isset($_SESSION['foto']) && isset($_SESSION['username'])): ?>
            <div class="user-info">
                <span class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <div class="profile-image">
                    <?php if(isset($_SESSION['foto']) && $_SESSION['foto']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($_SESSION['foto']); ?>" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px;">
                    <?php else: ?>
                        <i class="fas fa-user text-white"></i>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>
    <h1>Daftar Buku</h1>
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Genre</th>
                <th>Tahun Terbit</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($row['fotobuku']): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($row['fotobuku']); ?>" alt="Foto Buku">
                            <?php else: ?>
                                Tidak ada foto
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['judulbuku']); ?></td>
                        <td><?= htmlspecialchars($row['penulis']); ?></td>
                        <td><?= htmlspecialchars($row['genre']); ?></td>
                        <td><?= htmlspecialchars($row['tahunterbit']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Tidak ada buku tersedia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>