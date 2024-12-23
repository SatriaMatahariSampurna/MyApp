<?php
session_start();
include 'db.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php"); // Arahkan ke halaman login jika bukan admin
    exit;
}

// Pagination setup
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';

// Query pencarian
$whereClause = '';
if (!empty($search) && !empty($searchColumn)) {
    $search = mysqli_real_escape_string($conn, $search);
    $searchColumn = mysqli_real_escape_string($conn, $searchColumn);
    $whereClause = "WHERE $searchColumn LIKE '%$search%'";
}

// Hitung total data
$queryCount = "SELECT COUNT(*) as total FROM transfer_history $whereClause";
$resultCount = mysqli_query($conn, $queryCount);
$totalRows = mysqli_fetch_assoc($resultCount)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data dengan batasan untuk pagination
$queryTransfer = "SELECT * FROM transfer_history $whereClause ORDER BY transfer_date DESC LIMIT $limit OFFSET $offset";
$resultTransfer = mysqli_query($conn, $queryTransfer);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Riwayat Transfer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style_dataTrans.css" rel="stylesheet">
  
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
        <h2>Riwayat Transfer</h2>

        <!-- Form Pencarian -->
        <form method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari data..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="search_column" class="form-control">
                        <option value="">Pilih kolom...</option>
                        <option value="sender_username" <?= $searchColumn === 'sender_username' ? 'selected' : '' ?>>Pengirim</option>
                        <option value="receiver_username" <?= $searchColumn === 'receiver_username' ? 'selected' : '' ?>>Penerima</option>
                        <option value="transfer_date" <?= $searchColumn === 'transfer_date' ? 'selected' : '' ?>>Tanggal Transfer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal Transfer</th>
                    <th>Pengirim</th>
                    <th>Penerima</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultTransfer)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['transfer_date'] ?></td>   
                        <td><?= htmlspecialchars($row['sender_username']) ?></td>
                        <td><?= htmlspecialchars($row['receiver_username']) ?></td>
                        <td><?= $row['amount'] ?> IDR</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Navigasi Halaman -->
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
