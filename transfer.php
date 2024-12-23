<?php
include 'db.php';
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Menampilkan pesan
$message = '';

// Proses transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_username = $_SESSION['username']; // Username pengirim
    $receiver_username = $_POST['receiver_username']; // Username penerima
    $amount = $_POST['amount']; // Jumlah yang ditransfer

    // Validasi input
    if (empty($receiver_username) || empty($amount)) {
        $message = "Semua field harus diisi!";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $message = "Jumlah transfer harus angka positif!";
    } else {
        // Ambil saldo pengirim
        $query_sender = "SELECT saldo FROM users WHERE username = '$sender_username'";
        $result_sender = mysqli_query($conn, $query_sender);
        $sender = mysqli_fetch_assoc($result_sender);

        // Pastikan saldo pengirim cukup
        if ($sender['saldo'] < $amount) {
            $message = "Saldo Anda tidak cukup untuk transfer!";
        } else {
            // Cek apakah penerima ada
            $query_receiver = "SELECT * FROM users WHERE username = '$receiver_username'";
            $result_receiver = mysqli_query($conn, $query_receiver);
            
            if (mysqli_num_rows($result_receiver) > 0) {
                // Lakukan transfer
                $query_update_sender = "UPDATE users SET saldo = saldo - $amount WHERE username = '$sender_username'";
                $query_update_receiver = "UPDATE users SET saldo = saldo + $amount WHERE username = '$receiver_username'";

                if (mysqli_query($conn, $query_update_sender) && mysqli_query($conn, $query_update_receiver)) {
                    $message = "Transfer berhasil!";
                } else {
                    $message = "Terjadi kesalahan dalam transfer!";
                }
            } else {
                $message = "Pengguna tujuan tidak ditemukan!";
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
    <title>Transfer Antar Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Transfer Antar Pengguna</h2>
        <p>Silakan isi formulir di bawah ini untuk mentransfer saldo.</p>

        <!-- Menampilkan pesan -->
        <?php if (!empty($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="receiver_username" class="form-label">Username Penerima</label>
                <input type="text" name="receiver_username" id="receiver_username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Jumlah yang Akan Ditransfer</label>
                <input type="number" name="amount" id="amount" class="form-control" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Transfer</button>
        </form>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
