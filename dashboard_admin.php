<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

function showMessage($message, $type = 'success') {
    echo "<div class='alert alert-$type'>$message</div>";
}

if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $query = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($conn, $query)) {
        showMessage("Pengguna berhasil dihapus!");
    } else {
        showMessage("Gagal menghapus pengguna: " . mysqli_error($conn), 'danger');
    }
}

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style_dasadmin.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="logo">Dashboard Admin</div>
        <ul>
            <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="data_user.php"><i class="fas fa-users"></i> Data User</a></li>
            <li><a href="admin_topup.php"><i class="fas fa-wallet"></i>Data Top-Up User</a></li>
            <li><a href="data_transaksi.php"><i class="fas fa-exchange-alt"></i> Riwayat Transfer</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Selamat Datang di Halaman Admin</h2>
    </div>
</body>
</html>