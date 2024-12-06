<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil dengan Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style_profil.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="logo">My App</div>
        <ul>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="chat.php">Chat</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php">Logout</a></li>
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
</body>
</html>
