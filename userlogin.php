<?php
// Membuat koneksi ke database
session_start();
$conn = new mysqli("localhost", "root", "", "perpus");

// Mengecek apakah request adalah POST (form login dikirim)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil username dari input form
    $username = $_POST['username'];
    // Mengambil password dari input form
    $password = $_POST['password'];

    // mencari username di database
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
     // Mengecek apakah ada 1 pengguna dengan username tersebut
    if ($result->num_rows === 1) {
        // Mengambil data pengguna
        $user = $result->fetch_assoc();
        // Mengecek password benar atau tidak
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            // Menyimpan sessi username
            $_SESSION['username'] = $user['username'];
             // Menyimpan path foto ke sesi
            $_SESSION['foto'] = $user['foto'];
            // Jika berhasil login akan diarahkan ke dashboard
            header("Location: userdash.php");
            exit;
        } else {
            // Jika password salah akan mengeluarkan pesan password salah
            echo "<p style='color: red; text-align: center;'>Password Salah.</p>";
        }
    } else { // jika user di berhasil ditemukan akan memunculkan pesan user tidak di temukan
        echo "<p style='color: red; text-align: center;'>User tidak di temukan.</p>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
body {
       font-family: Arial, sans-serif;
       background-color: #f7f7f7;
       display: flex;
       justify-content: center;
       align-items: center;
       height: 100vh;
       margin: 0;
   }
   .login {
       background-color: #ffff;
       padding: 20px;
       border-radius: 8px;
       box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
       width: 300px;
   }
   .login h2 {
       text-align: center;
   }
   .login label {
       display: block;
       margin-bottom: 8px;
       color: #555;
   }
   .login input {
       width: 95% ;
       padding: 8px;
       margin-bottom: 12px;
       border: 1px solid #ddd;
       border-radius: 4px;
   }
   .login button {
       width: 100%;
       padding: 10px;
       border: none;
       background-color: #007bff;
       color: #fff;
       font-size: 16px;
       border-radius: 4px;
       cursor: pointer;
   }
   .login button:hover {
       background-color: #0056b3;
   }
   .login p {
       text-align: center;
       color: red;
   }
   .login a{
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
    .login a:hover{
        background-color:#0056b3;
    }



</style>
<body>
<div class="login">
    <form method="POST">
        <h2>PERPUSTAKAAN FTI</h2>
        <hr>
        <h2>LOGIN USER</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
        <a href="registrasiuser.php">Registrasi</a>
        <hr>
        <a href="superlogin.php">Login Sebagai SuperAdmin</a><br>
        <a href="adminlogin.php">Login Sebagai Admin</a><br>
        <a href="userlogin.php">Login Sebagai User</a><br>
        
    </form>
</div>
</body>
</html>