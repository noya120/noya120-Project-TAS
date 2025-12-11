<?php
session_start();  // Pastikan session dimulai


// Jika parameter logout ditemukan, hancurkan session
if (isset($_GET['logout'])) {
    session_unset(); // Menghapus semua variabel session
    session_destroy(); // Menghancurkan session
    header("Location: index.php"); // Redirect ke halaman utama
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Modern</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* CSS Styles */
        :root {
            --primary-gradient: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            --secondary-gradient: linear-gradient(135deg, #97ABFF 10%, #123597 100%);
        }

        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
        }

        /* Header Styles */
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

        /* Hero Section */
        .back {
            background-image: url('img/bg.jpg');
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }

        .img {
            max-width: 100%;
            height: auto;
            position: absolute;
            bottom: 0;
            right: 0;
            opacity: 0.3;
        }

        .hero-content {
            text-align: center;
            color: white;
            z-index: 1;
            max-width: 800px;
            padding: 2rem;
        }

        .hero-content h2 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
        }

        /* Koleksi Section */
        .koleksi-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .koleksi-item {
            width: 250px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            text-align: center;
        }

        .koleksi-item:hover {
            transform: translateY(-10px);
        }

        .koleksi-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .koleksi-item h4 {
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }

        .koleksi-item button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .koleksi-item button:hover {
            background-color: #0056b3;
        }

        footer {
            margin-top: 5%;
            background-color: #111;
            color: #fff;
            padding: 40px 0;
            text-align: left;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1;
            margin: 0 20px;
            min-width: 200px;
        }

        .footer-section h2 {
            font-size: 18px;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .footer-section {
            margin-bottom: 10px;
            color: #bbb;
            text-decoration: none;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
            color: #bbb;
            text-decoration: none;
        }

        .footer-section ul li a {
            color: #bbb;
            text-decoration: none;
        }

        .footer-section ul li a:hover {
            color: #fff;
        }

        .footer-section .social-icons {
            margin-top: 20px;
        }

        .footer-section .social-icons a {
            display: inline-block;
            margin-right: 10px;
        }

        .footer-section .social-icons img {
            width: 24px;
            height: 24px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h2 {
                font-size: 2.5rem;
            }

            .koleksi-container {
                flex-direction: column;
                align-items: center;
            }

            .koleksi-item {
                width: 90%;
                margin: 10px 0;
            }
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
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo">PERPUSTAKAAN FTI</div>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="koleksibuku.php">Koleksi</a></li>
                        <li class="nav-item"><a class="nav-link" href="userdash.php">Profil</a></li>
                        <?php if (!isset($_SESSION['username'])): ?>
                            <li class="nav-item"><a class="nav-link" href="userlogin.php">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="registrasiuser.php">Register</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="index.php?logout=true">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Menampilkan Foto Profil Jika Ada -->
            <?php if (isset($_SESSION['foto']) && isset($_SESSION['username'])): ?>
            <div class="user-info">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <div class="profile-image">
                    <?php if(isset($_SESSION['foto']) && $_SESSION['foto']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($_SESSION['foto']); ?>" alt="Profile">
                    <?php else: ?>
                    <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="back">
        <div class="hero-content">
            <h2>Jelajahi Dunia Pengetahuan</h2>
            <p>Temukan ribuan buku, jurnal, dan sumber belajar digital dalam satu platform</p>
            
        </div>
    </section>
    <!-- Koleksi Buku Section -->
    <section id="koleksi" class="koleksi">
        <h2 style="text-align: center; margin-bottom: 2rem; margin-top: 20px;">Koleksi Buku</h2>
        <div class="koleksi-container">
            <div class="koleksi-item">
                <img src="fotobuku/buku1.jpg" alt="Buku 1">
            </div>
            <div class="koleksi-item">
                <img src="fotobuku/buku2.jpg" alt="Buku 2">
            </div>
            <div class="koleksi-item">
                <img src="fotobuku/buku3.jpg" alt="Buku 3">
            </div>
        </div>
    </section>
    <footer>
      <div class="footer-container">
          <div class="footer-section about">
              <h2>About Us</h2>
              <ul>
                  <li><a href="#">Facebook</a></li>
                  <li><a href="#">Instagram</a></li>
                  <li><a href="#">Youtube</a></li>
                  <li><a href="#">Twitter</a></li>
              </ul>
          </div>
          <div class="footer-section community">
              <h2>Community</h2>
              <ul>
                  <li><a href="#">Discord</a></li>
                  <li><a href="#">FAQs</a></li>
                  <li><a href="#">Help</a></li>
                  <li><a href="#">Support</a></li>
              </ul>
          </div>
          <div class="footer-section created-by">
              <h2>Created By</h2>
              <ul>
                  <li>Bobby</li>
                  <li>Febrian</li>
                  <li>Kresna</li>
                  <li>Solo</li>
              </ul>
          </div>
          <div class="footer-section address">
              <h2>Our Address</h2>
              <p>Jl. Makmur No.156, Kalicacing Sidomukti, Salatiga, Jawa Tengah, Indonesia</p>
              <div class="social-icons">
                  <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
                  <a href="#"><img src="img/ig.jpg" alt="Instagram"></a>
                  <a href="#"><img src="img/youtube.png" alt="YouTube"></a>
                  <a href="#"><img src="img/twitter1.png" alt="Twitter"></a>
              </div>
          </div>
      </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>