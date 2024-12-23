<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil dengan Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style_profil.css" rel="stylesheet">
</head>
<body>
<div class="sidebar">
    <div class="logo"><i class="fas fa-comments"></i> My App</div>
    <ul>
        <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
        <li><a href="chat.php"><i class="fas fa-comments"></i> Chat</a></li>
        <li><a href="saldo.php"><i class="fas fa-wallet"></i> Dompet Digital</a></li>
        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
            <li><a href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="content">
    <div class="profile-card">
        <h2>Profil Pengguna</h2>
        <?php
        session_start();
        include 'db.php';
        $username = $_SESSION['username'];
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        ?>
        <div class="profile-detail">
            <strong>Nama:</strong> <span><?= $user['name'] ?></span>
        </div>
        <div class="profile-detail">
            <strong>Username:</strong> <span><?= $user['username'] ?></span>
        </div>
        <div class="profile-detail">
            <strong>Email:</strong> <span><?= $user['email'] ?></span>
        </div>
        <div class="profile-detail">
            <strong>Alamat:</strong> <span><?= $user['alamat'] ?></span>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
