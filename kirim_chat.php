<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['penerima']) || !isset($data['message'])) {
    http_response_code(400);
    exit;
}

// Mendapatkan ID pengirim berdasarkan username dari sesi
$query_user = "SELECT id FROM users WHERE username = '" . $_SESSION['username'] . "'";
$result_user = mysqli_query($conn, $query_user);
$pengirim = mysqli_fetch_assoc($result_user)['id'];

// Mendapatkan data penerima dan pesan
$penerima = intval($data['penerima']);
$message = mysqli_real_escape_string($conn, $data['message']);

// Menyimpan pesan ke tabel messages
$query = "INSERT INTO messages (pengirim, penerima, message) VALUES ($pengirim, $penerima, '$message')";
mysqli_query($conn, $query);

// Mengembalikan respons sukses
echo json_encode(['success' => true]);
?>
