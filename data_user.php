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

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';

// Query to filter data based on search criteria
$whereClause = '';
if (!empty($search) && !empty($searchColumn)) {
    $search = mysqli_real_escape_string($conn, $search);
    $searchColumn = mysqli_real_escape_string($conn, $searchColumn);
    $whereClause = "WHERE $searchColumn LIKE '%$search%'";
}

// Count total records
$queryCount = "SELECT COUNT(*) as total FROM users $whereClause";
$resultCount = mysqli_query($conn, $queryCount);
$totalRows = mysqli_fetch_assoc($resultCount)['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch data for the current page
$query = "SELECT * FROM users $whereClause LIMIT $limit OFFSET $offset";
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
    <link href="css/style_datauser.css" rel="stylesheet">
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
        <h2>Data Pengguna</h2>

        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari data..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="search_column" class="form-control">
                        <option value="">Pilih kolom...</option>
                        <option value="name" <?= $searchColumn === 'name' ? 'selected' : '' ?>>Nama</option>
                        <option value="username" <?= $searchColumn === 'username' ? 'selected' : '' ?>>Username</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <!-- User Data Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                <th>Waktu Dibuat</th>
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
                        <td><?= $user['created_at'] ?></td>
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

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&search_column=<?= htmlspecialchars($searchColumn) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</body>
</html>
