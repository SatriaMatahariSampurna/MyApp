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
$queryCount = "SELECT COUNT(*) as total FROM topup_requests $whereClause";
$resultCount = mysqli_query($conn, $queryCount);
$totalRows = mysqli_fetch_assoc($resultCount)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data dengan batasan untuk pagination
$queryTopup = "SELECT * FROM topup_requests $whereClause LIMIT $limit OFFSET $offset";
$resultTopup = mysqli_query($conn, $queryTopup);

// Proses untuk approve permintaan top-up
if (isset($_GET['approve_id'])) {
    $approveId = $_GET['approve_id'];
    $queryApprove = "UPDATE topup_requests SET status = 'approved' WHERE id = '$approveId'";
    if (mysqli_query($conn, $queryApprove)) {
        $queryTopupDetail = "SELECT * FROM topup_requests WHERE id = '$approveId'";
        $resultTopupDetail = mysqli_query($conn, $queryTopupDetail);
        $topupDetail = mysqli_fetch_assoc($resultTopupDetail);
        $username = $topupDetail['username'];
        $amount = $topupDetail['amount'];

        // Update saldo pengguna
        $queryUpdateSaldo = "UPDATE users SET saldo = saldo + $amount WHERE username = '$username'";
        mysqli_query($conn, $queryUpdateSaldo);
        $_SESSION['message'] = 'Permintaan top-up berhasil disetujui dan saldo diperbarui.';
    }
}

// Proses untuk reject permintaan top-up
if (isset($_GET['reject_id'])) {
    $rejectId = $_GET['reject_id'];
    $queryReject = "UPDATE topup_requests SET status = 'rejected' WHERE id = '$rejectId'";
    if (mysqli_query($conn, $queryReject)) {
        $_SESSION['message'] = 'Permintaan top-up berhasil ditolak.';
    }
}

// Proses untuk delete permintaan top-up
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $queryDelete = "DELETE FROM topup_requests WHERE id = '$deleteId'";
    if (mysqli_query($conn, $queryDelete)) {
        $_SESSION['message'] = 'Permintaan top-up berhasil dihapus.';
    } else {
        $_SESSION['message'] = 'Terjadi kesalahan saat menghapus permintaan top-up.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Top-Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style_admintopup.css" rel="stylesheet">
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
        <h2>Manajemen Top-Up Saldo</h2>

        <!-- Notifikasi -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Form Pencarian -->
        <form method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari data..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="search_column" class="form-control">
                        <option value="">Pilih kolom...</option>
                        <option value="created_at" <?= $searchColumn === 'created_at' ? 'selected' : '' ?>>Waktu Dibuat</option>
                        <option value="username" <?= $searchColumn === 'username' ? 'selected' : '' ?>>Username</option>
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
                    <th>Waktu Dibuat</th>
                    <th>Username</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultTopup)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['amount'] ?> IDR</td>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <a href="?approve_id=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-success">Setujui</a>
                                <a href="?reject_id=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-danger">Tolak</a>
                            <?php endif; ?>
                            <a href="?delete_id=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-warning" onclick="return confirm('Anda yakin ingin menghapus permintaan top-up ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Navigasi Halaman -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>&search_column=<?= $searchColumn ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <script>
        // Memeriksa apakah ada parameter tertentu yang mengindikasikan perubahan status
        <?php if (isset($_GET['approve_id']) || isset($_GET['reject_id']) || isset($_GET['delete_id'])): ?>
            // Menunggu beberapa detik setelah aksi, lalu refresh halaman
            setTimeout(function() {
                window.location.href = window.location.href.split('?')[0]; // Menghapus query string untuk menghindari duplikasi
            }, 1000); // 1000 ms (1 detik) sebelum halaman di-refresh
        <?php endif; ?>
    </script>

</body>
</html>
