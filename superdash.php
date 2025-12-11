<?php
session_start(); 
$conn = new mysqli("localhost", "root", "", "perpus");


// Memeriksa apakah session admin_id ada, jika tidak maka arahkan ke halaman login
if (!isset($_SESSION['super_id'])) {
    header("Location: superlogin.php");
    exit(); 

    
}


// Proses logout jika tombol logout ditekan
if (isset($_POST['logout'])) {
    session_destroy(); 
    header("Location: index.php");
    exit();
}

// Query untuk mengambil statistik jumlah buku, pengguna, dan peminjaman yang sedang berlangsung
$total_admin = $conn->query("SELECT COUNT(*) as total FROM admin")->fetch_assoc()['total'];
$total_buku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$total_user = $conn->query("SELECT COUNT(*) as total FROM user")->fetch_assoc()['total'];
$total_peminjaman = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'")->fetch_assoc()['total'];

// Query untuk mengambil aktivitas peminjaman terbaru
$recent_activities = $conn->query("
    SELECT u.nama, b.judulbuku, p.tanggal_pinjam, p.status
    FROM peminjaman p 
    JOIN user u ON p.user_id = u.user_id 
    JOIN buku b ON p.buku_id = b.buku_id 
    ORDER BY p.tanggal_pinjam DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Perpustakaan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
</head>
<style>
    :root {
    /* Variabel untuk warna utama, sekunder, latar belakang, dan teks */
    --primary-color: #1976d2;
    --secondary-color: #388e3c;
    --background-color: #f4f6f9;
    --text-color: #333;
    --hover-color: #e3f2fd;
    --danger-color: #d32f2f;
    --danger-hover: #ffebee;
}

body {
    /* Gaya dasar untuk seluruh halaman */
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: var(--background-color);
    color: var(--text-color);
}

/* HEADER */
header {
    background-color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1001;
}

header .logo {
    display: flex;
    align-items: center;
    color: var(--primary-color);
    font-size: 20px;
    font-weight: bold;
}

header .logo i {
    margin-right: 10px;
}

.user-section {
    /* Bagian untuk menampilkan informasi pengguna */
    display: flex; 
    align-items: center;
    margin-right: 20px; 
    gap: 10px; /* Menambahkan jarak antara nama dan gambar profil */
}

.profile-image {
    /* Gaya untuk foto profil pengguna */
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    overflow: hidden; /* Memastikan gambar tidak terpotong */
    display: flex; 
    align-items: center; 
    justify-content: center; 
    background-color: var(--primary-color);
}

.profile-image img {
    /* Gaya untuk gambar profil */
    width: 100%; 
    height: 100%; 
    object-fit: cover; /* Memastikan gambar sesuai dalam lingkaran tanpa terpotong */
}


/* SIDEBAR */
.sidebar {
    width: 250px;
    background-color: #fff;
    box-shadow: 4px 0 6px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 80px;
    left: 0;
    height: calc(100vh - 80px);
    padding-top: 20px;
    display: flex;
    flex-direction: column;
}

.sidebar .nav-item {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

.sidebar .nav-item:hover {
    background-color: var(--hover-color);
    color: var(--primary-color);
}

.sidebar .nav-item.active {
    background-color: var(--hover-color);
    border-left: 4px solid var(--primary-color);
}

.sidebar .nav-item i {
    margin-right: 10px;
    font-size: 1.2em;
}

.logout-form {
    margin-top: auto;
    margin-bottom: 20px;
}

.logout-button {
    width: 100%;
    background: none;
    border: none;
    display: flex;
    align-items: center;
    padding: 15px 20px;
    cursor: pointer;
    color: var(--danger-color);
    font-size: 16px;
    transition: background-color 0.3s;
}

.logout-button:hover {
    background-color: var(--danger-hover);
}

/* MAIN CONTENT */
.main-content {
    margin-left: 260px;
    padding: 30px;
    margin-top: 80px;
    flex: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    border-radius: 8px;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: scale(1.05);
}

.stat-card i {
    font-size: 2em;
    color: var(--primary-color);
}

.stat-card h3 {
    font-size: 2em;
    margin: 10px 0;
    color: var(--secondary-color);
}

.stat-card p {
    color: black;
}

.data-peminjaman {
    margin-top: 40px;
}

.data-peminjaman h2 {
    font-size: 1.5em;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--primary-color);
}
.table-responsive {
    overflow-x: auto; /* Mengaktifkan scroll horizontal */
    -webkit-overflow-scrolling: touch; /* Mendukung scroll lancar pada perangkat sentuh */
}

.peminjaman-table {
    min-width: 600px; /* Pastikan tabel tidak terpotong di perangkat kecil */
    border-spacing: 0; /* Menghilangkan jarak antar sel tabel */
}

.peminjaman-table th, .peminjaman-table td {
    white-space: nowrap; /* Mencegah teks membungkus pada kolom */
}

.peminjaman-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.peminjaman-table th, .peminjaman-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

.peminjaman-table th {
    background-color: var(--primary-color);
    color: #fff;
    text-transform: uppercase;
}

.peminjaman-table tr:hover {
    background-color: var(--hover-color);
}
a{
    text-decoration:none;
    color:blue;
}


/* RESPONSIVENESS */
@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        box-shadow: none;
    }

    .main-content {
        margin-left: 0;
    }
    .peminjaman-table th, .peminjaman-table td {
        font-size: 0.9em; /* Perkecil teks pada layar kecil */
        padding: 10px; /* Kurangi padding */
    }
}
</style>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-book-reader"></i>
            Perpustakaan FTI
        </div>
        <div class="user-section">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <div class="profile-image">
                <?php if(isset($_SESSION['foto']) && $_SESSION['foto']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($_SESSION['foto']); ?>" alt="Profile">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <aside class="sidebar">
        <div class="nav-item">
            <i class="fas fa-tachometer-alt"></i>
            <a href="superdash.php"><span>Dashboard</span></a>
        </div>
        <div class="nav-item">
            <i class="fas fa-book"></i>
            <a href="databuku.php"><span>Data Buku</span></a>
        </div>
        <div class="nav-item">
            <i class="fas fa-users"></i>
            <a href="tambahadmin.php"><span>Tambah Admin</span></a>
        </div>
        <div class="nav-item">
            <i class="fas fa-users"></i>
            <a href="updateadmin.php"><span>Edit Admin</span></a>
        </div>
        <div class="nav-item">
            <i class="fas fa-users"></i>
            <a href="dataadmin.php"><span>Data Admin</span></a>
        </div>
        <div class="nav-item">
            <i class="fas fa-users"></i>
            <a href="dataanggotaS.php"><span>Data Anggota</span></a>
        </div>
        <form method="POST" class="nav-item logout-form">
            <button type="submit" name="logout" class="logout-button">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    <main class="main-content">
        <h2>Dashboard Overview</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-book fa-2x"></i>
                <h3><?php echo $total_buku; ?></h3>
                <p>Total Buku</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users fa-2x"></i>
                <h3><?php echo $total_admin; ?></h3>
                <p>Total Admin</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users fa-2x"></i>
                <h3><?php echo $total_user; ?></h3>
                <p>Total Anggota</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-book-reader fa-2x"></i>
                <h3><?php echo $total_peminjaman; ?></h3>
                <p>Buku Dipinjam</p>
            </div>
        </div>

        

        <div class="data-peminjaman">
            <h2 class="peminjaman-header">
                <i class="fas fa-exchange-alt"></i>
                Data Peminjaman
            </h2>
        <div class="table-responsive">
            <table class="peminjaman-table">
                <thead>
                    <tr>
                        <th>ID Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_peminjaman = "
                        SELECT p.peminjaman_id, u.nama AS nama_user, b.judulbuku, p.tanggal_pinjam, p.tanggal_kembali, p.status
                        FROM peminjaman p
                        JOIN user u ON p.user_id = u.user_id
                        JOIN buku b ON p.buku_id = b.buku_id
                        ORDER BY p.tanggal_pinjam DESC
                    ";
                    $result_peminjaman = $conn->query($query_peminjaman);

                    while ($row = $result_peminjaman->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['peminjaman_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                        <td><?php echo htmlspecialchars($row['judulbuku']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo htmlspecialchars($row['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </div>
    </main>
</body>
</html>
