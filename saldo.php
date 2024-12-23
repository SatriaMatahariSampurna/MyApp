<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Arahkan ke halaman login jika belum login
    exit;
}

$username = $_SESSION['username'];

// Ambil data pengguna dari database
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil dijalankan
if (!$result) {
    die("Query gagal dijalankan: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

// Ambil saldo pengguna
$querySaldo = "SELECT saldo FROM users WHERE username = '$username'";
$resultSaldo = mysqli_query($conn, $querySaldo);

// Cek apakah query saldo berhasil dijalankan
if (!$resultSaldo) {
    die("Query gagal dijalankan: " . mysqli_error($conn));
}

$saldo = mysqli_fetch_assoc($resultSaldo);

$topupSuccess = false;
$transferSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topup_amount'])) {
    // Proses top-up saldo
    $topupAmount = $_POST['topup_amount'];

    // Pastikan jumlah top-up lebih dari 0
    if ($topupAmount <= 0) {
        echo "Jumlah top-up tidak valid.";
    } else {
        // Insert permintaan top-up ke database (tunggu persetujuan admin)
        $queryTopup = "INSERT INTO topup_requests (username, amount, status) VALUES ('$username', '$topupAmount', 'pending')";
        if (mysqli_query($conn, $queryTopup)) {
            $topupSuccess = true;
        } else {
            echo "Terjadi kesalahan. Coba lagi.";
        }
    }
}

// Proses transfer saldo antar pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_username']) && isset($_POST['transfer_amount'])) {
    $receiverUsername = $_POST['receiver_username'];
    $transferAmount = $_POST['transfer_amount'];

    // Pastikan jumlah transfer lebih dari 0
    if ($transferAmount <= 0) {
        echo "Jumlah transfer tidak valid.";
    } else {
        // Validasi saldo pengirim
        if ($saldo['saldo'] < $transferAmount) {
            echo "Saldo Anda tidak mencukupi untuk melakukan transfer.";
        } else {
            // Periksa apakah penerima ada
            $queryReceiver = "SELECT * FROM users WHERE username = '$receiverUsername'";
            $resultReceiver = mysqli_query($conn, $queryReceiver);

            if (mysqli_num_rows($resultReceiver) > 0) {
                // Update saldo pengirim dan penerima
                $queryUpdateSender = "UPDATE users SET saldo = saldo - $transferAmount WHERE username = '$username'";
                $queryUpdateReceiver = "UPDATE users SET saldo = saldo + $transferAmount WHERE username = '$receiverUsername'";

                if (mysqli_query($conn, $queryUpdateSender) && mysqli_query($conn, $queryUpdateReceiver)) {
                    // Simpan riwayat transfer ke database
                    $queryHistory = "INSERT INTO transfer_history (sender_username, receiver_username, amount) VALUES ('$username', '$receiverUsername', $transferAmount)";
                    if (mysqli_query($conn, $queryHistory)) {
                        $transferSuccess = true;
                    } else {
                        echo "Terjadi kesalahan saat menyimpan riwayat transfer.";
                    }
                } else {
                    echo "Terjadi kesalahan dalam proses transfer.";
                }
            } else {
                echo "Pengguna tujuan tidak ditemukan.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Saldo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/saldo.css" rel="stylesheet">
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
        <h2>Profil Pengguna</h2>
        <div class="card">
            <div class="profile-detail">
                <strong>Username:</strong> <span><?= $user['username'] ?></span>
            </div>
            <div class="profile-detail">
                <strong>Saldo:</strong> <span>Rp <?= number_format($saldo['saldo'], 0, ',', '.') ?></span>
            </div>
        </div>

        <h3>Dompet Digital</h3>
        <div class="d-flex gap-3">
            <button id="topupButton" class="btn btn-primary"><i class="fas fa-money-bill-wave"></i> Top-Up</button>
            <button id="transferButton" class="btn btn-success"><i class="fas fa-exchange-alt"></i> Transfer</button>
        </div>

        <!-- Formulir Top-Up -->
        <form id="topupForm" method="POST" style="display: none;" class="card">
            <div class="mb-3">
                <label for="topup_amount" class="form-label">Jumlah Top-Up (IDR)</label>
                <input type="number" class="form-control" id="topup_amount" name="topup_amount" required>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Permintaan Top-Up</button>
        </form>

        <!-- Formulir Transfer -->
        <form id="transferForm" method="POST" style="display: none;" class="card">
            <div class="mb-3">
                <label for="receiver_username" class="form-label">Username Penerima</label>
                <input type="text" class="form-control" id="receiver_username" name="receiver_username" required>
            </div>
            <div class="mb-3">
                <label for="transfer_amount" class="form-label">Jumlah Transfer (IDR)</label>
                <input type="number" class="form-control" id="transfer_amount" name="transfer_amount" required>
            </div>
            <button type="submit" class="btn btn-success">Kirim Transfer</button>
        </form>

        <!-- Pesan Transfer Berhasil -->
        <?php if ($transferSuccess): ?>
            <div class="alert alert-success mt-3">Transfer berhasil dilakukan!</div>
        <?php endif; ?>

        <!-- Pesan Top-Up Berhasil -->
        <?php if ($topupSuccess): ?>
            <div class="alert alert-success mt-3">Permintaan top-up berhasil dikirim dan menunggu persetujuan admin.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('topupButton').addEventListener('click', function () {
            const form = document.getElementById('topupForm');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        });

        document.getElementById('transferButton').addEventListener('click', function () {
            const form = document.getElementById('transferForm');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        });
    </script>
</body>
</html>
