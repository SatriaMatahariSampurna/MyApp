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
    <link href="css/style_admin.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="logo">Admin Dashboard</div>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Data Pengguna</h2>



        <!-- Tabel Data Pengguna -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['alamat'] ?></td>

                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="dashboard_admin.php?delete_user=<?= $user['id'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
